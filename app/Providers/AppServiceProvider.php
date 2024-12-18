<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Mews\Purifier\PurifierServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->register(PurifierServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
