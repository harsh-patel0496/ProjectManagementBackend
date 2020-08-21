<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Utils\MaterialContainer\Material;
use App\Utils\ResponseJson\ResponseJson;
class UtilServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('material',function($app,$params){
            return new Material();
        });

        $this->app->bind('responseJson',function($app,$params){
            return new ResponseJson();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
