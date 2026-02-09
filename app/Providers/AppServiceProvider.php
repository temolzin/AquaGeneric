<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        require_once app_path('Helpers/ColorHelper.php');
        
        // Forzar HTTPS cuando estamos detrás de un proxy (ngrok, load balancer, etc.)
        if ($this->app->environment('production') || 
            request()->header('X-Forwarded-Proto') === 'https' ||
            str_contains(config('app.url', ''), 'https://')) {
            URL::forceScheme('https');
        }
    }
}
