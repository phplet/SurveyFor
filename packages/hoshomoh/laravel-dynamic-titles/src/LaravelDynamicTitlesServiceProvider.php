<?php

namespace hoshomoh\LaravelDynamicTitles;

use Illuminate\Support\ServiceProvider;

class LaravelDynamicTitlesServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        //
        view()->composer(
            '*', 'hoshomoh\LaravelDynamicTitles\Http\ViewComposers\RouteComposer'
        );
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}