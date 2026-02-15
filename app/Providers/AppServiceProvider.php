<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Pagination\Paginator;

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
    }
}
