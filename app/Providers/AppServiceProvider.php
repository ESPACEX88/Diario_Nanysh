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
        
        // Limpiar y corregir DATABASE_URL si es necesario
        $dbUrl = env('DATABASE_URL') ?: env('DB_URL');
        if ($dbUrl) {
            // Eliminar comillas y espacios al inicio/final
            $dbUrl = trim($dbUrl, " \t\n\r\0\x0B\"'");
            
            // Limpiar formato JDBC si está presente
            $dbUrl = preg_replace('/^jdbc:postgresql:\/\//', 'postgresql://', $dbUrl);
            $dbUrl = preg_replace('/^jdbc:postgres:\/\//', 'postgresql://', $dbUrl);
            
            // Asegurar que no haya doble postgresql://
            $dbUrl = preg_replace('/postgresql:\/\/postgresql:\/\//', 'postgresql://', $dbUrl);
            
            // Si la URL no tiene puerto explícito, agregarlo
            // Formato esperado: postgresql://user:pass@host:port/db
            if (preg_match('/^postgresql:\/\/[^@]+@([^:]+)\/([^?]+)/', $dbUrl, $matches)) {
                // No tiene puerto, agregarlo
                $host = $matches[1];
                $db = $matches[2];
                $dbUrl = str_replace('@' . $host . '/' . $db, '@' . $host . ':5432/' . $db, $dbUrl);
            }
            
            // Asegurar que tenga sslmode=require si es Neon (contiene .neon.tech o .aws.neon.tech)
            if (strpos($dbUrl, 'neon.tech') !== false && strpos($dbUrl, 'sslmode') === false) {
                // Agregar sslmode=require si no está presente
                $separator = strpos($dbUrl, '?') !== false ? '&' : '?';
                $dbUrl .= $separator . 'sslmode=require';
            }
            
            // Actualizar la variable de entorno
            $_ENV['DATABASE_URL'] = $dbUrl;
            putenv("DATABASE_URL={$dbUrl}");
        }
        
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
