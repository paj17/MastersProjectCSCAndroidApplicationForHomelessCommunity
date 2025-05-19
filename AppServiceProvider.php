<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
      Paginator::useBootstrap();
      Validator::extend('mp3_ogg_extension', function($attribute, $value, $parameters, $validator) {

        if(!empty($value->getClientOriginalExtension()) && ($value->getClientOriginalExtension() == 'mp3' || $value->getClientOriginalExtension() == 'ogg')){
            return true;
        }else{
            return false;
        }

    });
    }
}
