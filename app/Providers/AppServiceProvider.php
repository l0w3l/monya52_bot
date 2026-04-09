<?php

namespace App\Providers;

use App\Auth\TelegramGuard;
use Illuminate\Support\Facades\Auth;
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
        Auth::extend('telegram', function ($app, $name, array $config) {
            return new TelegramGuard(
                Auth::createUserProvider($config['provider']),
                $app['request']
            );
        });

        if (\Illuminate\Support\Facades\DB::connection() instanceof \Illuminate\Database\SQLiteConnection) {
            $pdo = \Illuminate\Support\Facades\DB::connection()->getPdo();

            $pdo->sqliteCreateFunction('SIMILARITY', function ($a, $b) {
                if ($a === null || $b === null) {
                    return 0;
                }
                similar_text(mb_strtolower((string) $a), mb_strtolower((string) $b), $percent);

                return $percent;
            }, 2);

            $pdo->sqliteCreateFunction('LOWER_UNICODE', function ($string) {
                return mb_strtolower((string) $string);
            }, 1);

            $pdo->sqliteCreateFunction('CONTAINS_UNICODE', function ($haystack, $needle) {
                if ($haystack === null || $needle === null) {
                    return false;
                }

                return mb_stripos((string) $haystack, (string) $needle) !== false;
            }, 2);
        }
    }
}
