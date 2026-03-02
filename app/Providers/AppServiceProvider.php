<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Pagination\Paginator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Failed;
use App\Models\LoginHistory;

class AppServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        //
    }


    public function boot(): void
    {
        Paginator::useTailwind();

        // ده بيقول لـ لارافل: لو إحنا مش على السيرفر المحلي (Local)، أجبر الروابط تبقى HTTPS
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Enforce strict password rules universally
        Password::defaults(function () {
            return Password::min(8)
                           ->letters()
                           ->mixedCase()
                           ->numbers()
                           ->symbols()
                           ->uncompromised();
        });

        // Track Successful Logins
        Event::listen(function (Login $event) {
            LoginHistory::create([
                'user_id' => $event->user->getAuthIdentifier(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'status' => 'success',
            ]);
        });

        // Track Failed Logins
        Event::listen(function (Failed $event) {
            LoginHistory::create([
                'user_id' => $event->user ? $event->user->getAuthIdentifier() : null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'status' => 'failed',
            ]);
        });
    }
}
