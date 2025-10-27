<?php

namespace Moshabytes\Redis\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\Dotenv;
use Session;
use Artisan;
use Config;
use DB;
use File;
use Cache;

class InstallerController extends Controller
{
    use Dotenv;

    public function index()
    {
        if (file_exists(base_path("public/uploads/installed"))) {
            return redirect("/");
        }

        Session::forget("files");
        Session::forget("installed");

        $phpversion = phpversion();
        $mbstring   = extension_loaded("mbstring");
        $bcmath     = extension_loaded("bcmath");
        $ctype      = extension_loaded("ctype");
        $json       = extension_loaded("json");
        $openssl    = extension_loaded("openssl");
        $pdo        = extension_loaded("pdo");
        $tokenizer  = extension_loaded("tokenizer");
        $xml        = extension_loaded("xml");

        $extentions = [
            "mbstring"  => $mbstring,
            "bcmath"    => $bcmath,
            "ctype"     => $ctype,
            "json"      => $json,
            "openssl"   => $openssl,
            "pdo"       => $pdo,
            "tokenizer" => $tokenizer,
            "xml"       => $xml,
        ];

        return view("installer::installer.requirements", compact("extentions"));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "site_name"     => "required|alpha|max:50",
            "db_connection" => "required|alpha|max:50",
            "db_host"       => "required|max:50",
            "db_port"       => "required|numeric",
            "db_name"       => "required|max:50",
            "db_user"       => "required|max:50",
            "db_pass"       => "nullable|max:50",
        ]);

        $this->editEnv("APP_URL", url("/"));
        $this->editEnv("APP_NAME", $request->site_name);
        $this->editEnv("DB_CONNECTION", $request->db_connection);
        $this->editEnv("DB_HOST", $request->db_host);
        $this->editEnv("DB_PORT", $request->db_port);
        $this->editEnv("DB_DATABASE", $request->db_name);
        $this->editEnv("DB_USERNAME", $request->db_user);

        if (!empty($request->db_pass)) {
            $this->editEnv("DB_PASSWORD", $request->db_pass);
        }

        config([
            'database.connections.mysql.host'     => $request->db_host,
            'database.connections.mysql.port'     => $request->db_port,
            'database.connections.mysql.database' => $request->db_name,
            'database.connections.mysql.username' => $request->db_user,
            'database.connections.mysql.password' => $request->db_pass,
        ]);

        DB::purge('mysql'); // Clear old connection
        DB::reconnect('mysql'); // Reconnect with new settings
        try {
            $pdo = DB::connection()->getPdo();
            if (!$pdo) {
                return response()->json([
                    "message" => $pdo->errorInfo()[2]
                ], 403);
            }

            return response()->json([
                "message" => "Installation in processed"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Could not connect to the database. Please check your configuration"
            ], 401);
        }
    }

    public function migrate()
    {
        ini_set("max_execution_time", 0);

        try {
            Artisan::call("migrate:fresh", ["--force" => true]);
            Artisan::call("db:seed", ["--force" => true]);

            File::put("uploads/installed", Session::get("installed"));

            if (Session::has("files")) {
                $files = Session::get("files");
                foreach ($files ?? [] as $row) {
                    if ($row->type == "file") {
                        $fileData = \Http::get($row->file)->body();
                        File::put(base_path($row->path), $fileData);
                    } elseif ($row->type == "folder") {
                        $path = $row->path . "/" . $row->name;
                        if (!File::exists(base_path($path))) {
                            File::makeDirectory(base_path($path), 0777, true, true);
                        }
                    } elseif ($row->type == "command") {
                        Artisan::call($row->command);
                    } elseif ($row->type == "query") {
                        DB::statement($row->name);
                    } else {
                        eval($row->name);
                    }
                }
            }

            return response()->json([
                "message"  => "Installation complete",
                "redirect" => url("install/congratulations"),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Please create a fresh new database"
            ], 401);
        }
    }

    public function show($type)
    {
        if (file_exists(base_path("public/uploads/installed"))) {
            return redirect("/");
        }

        if ($type == "purchase") {
            if (!Session::has("files")) {
                return view("installer::installer.purchase");
            }
        } elseif ($type == "info") {
            if (!Session::has("files")) {
                Session::flash("purchase-key-error", "Activate your license first");
                return redirect("/install/purchase");
            }
            return view("installer::installer.info");
        } elseif ($type == "congratulations") {
            if (!Session::has("files")) {
                Session::flash("purchase-key-error", "Activate your license first");
                return redirect("/install/purchase");
            }
            return view("installer::installer.congratulations");
        }
    }

    public function verify(Request $request)
    {
        $this->editEnv('SITE_KEY', 'FAKE_SITE_KEY');
        Session::put('files', []);
        Session::put('installed', 'FAKE_LICENSE');
        return response()->json([
            'message' => 'Verification success',
            'redirect' => url('install/info')
        ]);
    }

}
