<?php

namespace App\Providers;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
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
        \View::composer('*', function ($view) {
            $allSetting = \Cache::rememberForever('allSetting', function () {
                return   \DB::table('configurations')->get()->keyBy('name');
            });

            $view->with('allSetting', $allSetting);
        });
    }
}
