<?php

namespace Moshabytes\Redis;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
class MoshaServiceProvider extends ServiceProvider
{


    public function register()
    {
       
    }
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(\Illuminate\Routing\Router $router)
    {
        $router->middleware('HerdMiddleware', \Moshabytes\Redis\Middleware\HerdMiddleware::class);
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadViewsFrom(__DIR__.'/views', 'installer');
        
        

    }
}
