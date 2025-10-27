<?php

namespace Bitbox\Cache;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
class BitCacheServiceProvider extends ServiceProvider
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
        
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadViewsFrom(__DIR__.'/views', 'installer');
        
        

    }
}
