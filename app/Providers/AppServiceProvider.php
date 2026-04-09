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
                $a = mb_strtolower((string) $a);
                $b = mb_strtolower((string) $b);
                if ($a === $b) {
                    return 100;
                }
                if ($a === '' || $b === '') {
                    return 0;
                }
                similar_text($a, $b, $percent);

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

            $pdo->sqliteCreateFunction('FUZZY_MATCH', function ($text, $query) {
                if ($text === null || $query === null || $query === '') {
                    return 0;
                }
                $text = mb_strtolower((string) $text);
                $query = mb_strtolower((string) $query);

                // 1. Exact or partial word match (High score)
                if (mb_stripos($text, $query) !== false) {
                    return 100;
                }

                // 2. Character overlap (Fuzzy/Scattered match)
                $charsQ = preg_split('//u', $query, -1, PREG_SPLIT_NO_EMPTY);
                $charsT = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY);
                $matched = 0;
                $tempText = $text;
                foreach ($charsQ as $char) {
                    $pos = mb_stripos($tempText, $char);
                    if ($pos !== false) {
                        $matched++;
                        // Remove matched char to handle duplicates correctly
                        $tempText = mb_substr($tempText, 0, $pos).mb_substr($tempText, $pos + 1);
                    }
                }

                $score = ($matched / (mb_strlen($query) ?: 1)) * 100;
                // Penalize for length difference to avoid long texts matching everything
                $lenDiff = abs(mb_strlen($text) - mb_strlen($query));
                if ($lenDiff > 10) {
                    $score -= min(30, ($lenDiff - 10));
                }

                return max(0, $score);
            }, 2);
        }
    }
}
