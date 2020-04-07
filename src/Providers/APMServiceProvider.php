<?php

namespace SmartAPM\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use SmartAPM\APMCollector;

class APMServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(APMCollector::class, function(){
            return new APMCollector;
        });

        if(config('smartapm.key') && !$this->app->runningInConsole()){
            resolve(APMCollector::class);
        }
    }
    
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        
    }
}
