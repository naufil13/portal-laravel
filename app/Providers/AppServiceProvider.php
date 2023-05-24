<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Schema;

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
        if(config('app.env') === 'Production') {
            \URL::forceScheme('https');
        }

       /* if (env('APP_ENV') != 'Development') {
            $this->app['request']->server->set('HTTPS', true);
        }*/

        error_reporting(E_ERROR);
        if (env('APP_QUERY_LOG')) {
            \DB::enableQueryLog();
        }

        $this->load_configurations();
        Schema::defaultStringLength(191);
    }


    private function load_configurations(){
        return config([
            'global' => \App\Setting::all([
                'name','value'
            ])
            ->keyBy('name')
            ->transform(function ($setting) {
                 return $setting->value;
            })
            ->toArray()
        ]);
    }
}
