<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

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
        Vite::prefetch(concurrency: 3);
        
        // Force HTTPS in production
        if (env('APP_ENV') === 'production' || config('app.env') === 'production') {
            URL::forceScheme('https');
            $appUrl = config('app.url');
            if ($appUrl) {
                URL::forceRootUrl($appUrl);
            }
            
            // Force HTTPS for Vite assets
            if (!env('ASSET_URL')) {
                $assetUrl = str_replace('http://', 'https://', config('app.url', ''));
                if ($assetUrl) {
                    config(['app.asset_url' => $assetUrl]);
                }
            }
        }
    }
}
