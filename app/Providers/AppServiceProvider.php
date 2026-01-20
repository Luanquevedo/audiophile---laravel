<?php
namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
    public function boot(): void
    {

        /*
         |----------------------------------------------------------------------
         | Rate Limiting
         |----------------------------------------------------------------------
         | - 'api': baseline protection for all API endpoints
         | - 'login': stricter limit keyed by IP + email to mitigate brute-force
         | - 'register': stricter limit per IP to mitigate signup abuse
         */

        RateLimiter::for('api', function (Request $request) {
            // Authenticated users are limited per user id; guests per IP.
            return Limit::perMinute(60)->by(
                $request->user()?->id ?: $request->ip()
            );
        });

    RateLimiter::for('login', function (Request $request) {
            $email = mb_strtolower(trim((string) $request->input('email')));

            // If email is missing, fallback to IP to avoid a shared "IP|" bucket.
            $key = $email !== '' ? $request->ip() . '|' . $email : $request->ip();

            return Limit::perMinute(5)->by($key);
        });
         
        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinute(3)->by($request->ip());
        });
    }

}
