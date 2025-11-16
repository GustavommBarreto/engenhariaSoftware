<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $appUrl = config('app.url');

        if ($appUrl) {
            // força o Laravel a usar SEMPRE o APP_URL para gerar links (route(), url(), redirect()->route() etc.)
            URL::forceRootUrl($appUrl);

            // se o APP_URL começa com https, força o esquema https também
            if (str_starts_with($appUrl, 'https://')) {
                URL::forceScheme('https');
            }
        }
    }
}
