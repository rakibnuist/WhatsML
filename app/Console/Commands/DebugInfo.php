<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Helpers\DebugHelper;

class DebugInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:info {--check-db : Check database connection} {--check-cache : Check cache connection} {--check-logs : Check log files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display debugging information about the application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 WhatsML Debug Information');
        $this->line('');

        // Environment Information
        $this->info('📋 Environment Information:');
        $this->line('App Name: ' . config('app.name'));
        $this->line('App Environment: ' . config('app.env'));
        $this->line('App Debug: ' . (config('app.debug') ? 'Enabled' : 'Disabled'));
        $this->line('App URL: ' . config('app.url'));
        $this->line('');

        // Database Check
        if ($this->option('check-db')) {
            $this->info('🗄️ Database Connection:');
            try {
                $connection = DB::connection();
                $this->line('Driver: ' . $connection->getDriverName());
                $this->line('Database: ' . $connection->getDatabaseName());
                $this->line('Status: ✅ Connected');
                
                // Test a simple query
                $result = DB::select('SELECT 1 as test');
                $this->line('Query Test: ✅ Success');
            } catch (\Exception $e) {
                $this->error('Database Error: ' . $e->getMessage());
                DebugHelper::log('Database connection failed', ['error' => $e->getMessage()], 'error');
            }
            $this->line('');
        }

        // Cache Check
        if ($this->option('check-cache')) {
            $this->info('💾 Cache Connection:');
            try {
                $driver = config('cache.default');
                $this->line('Driver: ' . $driver);
                
                // Test cache
                Cache::put('debug_test', 'test_value', 60);
                $value = Cache::get('debug_test');
                
                if ($value === 'test_value') {
                    $this->line('Status: ✅ Working');
                    Cache::forget('debug_test');
                } else {
                    $this->error('Status: ❌ Failed');
                }
            } catch (\Exception $e) {
                $this->error('Cache Error: ' . $e->getMessage());
                DebugHelper::log('Cache connection failed', ['error' => $e->getMessage()], 'error');
            }
            $this->line('');
        }

        // Log Files Check
        if ($this->option('check-logs')) {
            $this->info('📝 Log Files:');
            $logPath = storage_path('logs');
            
            if (is_dir($logPath)) {
                $files = glob($logPath . '/*.log');
                if (empty($files)) {
                    $this->line('No log files found');
                } else {
                    foreach ($files as $file) {
                        $size = filesize($file);
                        $modified = date('Y-m-d H:i:s', filemtime($file));
                        $this->line(basename($file) . ' - ' . $this->formatBytes($size) . ' - Modified: ' . $modified);
                    }
                }
            } else {
                $this->error('Log directory does not exist');
            }
            $this->line('');
        }

        // Module Status
        $this->info('📦 Module Status:');
        $modulesFile = base_path('modules_statuses.json');
        if (file_exists($modulesFile)) {
            $modules = json_decode(file_get_contents($modulesFile), true);
            foreach ($modules as $module => $status) {
                $statusIcon = $status ? '✅' : '❌';
                $this->line("$module: $statusIcon");
            }
        } else {
            $this->line('No modules_statuses.json file found');
        }
        $this->line('');

        // Environment Variables Check
        $this->info('🔧 Critical Environment Variables:');
        $criticalVars = [
            'APP_KEY' => env('APP_KEY'),
            'DB_CONNECTION' => env('DB_CONNECTION'),
            'MAIL_TO' => env('MAIL_TO'),
            'WHATSAPP_WEB_API_KEY' => env('WHATSAPP_WEB_API_KEY'),
        ];

        foreach ($criticalVars as $var => $value) {
            $status = $value ? '✅ Set' : '❌ Missing';
            $displayValue = $var === 'APP_KEY' ? (strlen($value) > 10 ? substr($value, 0, 10) . '...' : $value) : $value;
            $this->line("$var: $status" . ($value ? " ($displayValue)" : ''));
        }

        $this->line('');
        $this->info('🎯 Debug Helper Available:');
        $this->line('Use DebugHelper::log() for custom debugging');
        $this->line('Use DebugHelper::logWhatsAppOperation() for WhatsApp debugging');
        $this->line('Use DebugHelper::logOpenAIOperation() for OpenAI debugging');
    }

    private function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        return round($size, $precision) . ' ' . $units[$i];
    }
}
