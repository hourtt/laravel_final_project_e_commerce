<?php

namespace App\Providers;

use App\Services\PayWayService;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    // Register any application services.
    public function register(): void
    {
        //
        $this->app->singleton(PayWayService::class,function($app){
            return new PayWayService();
        });
    }

    // Bootstrap any application services.
    public function boot(): void
    {
        //* Setting URL to https when deploying on vercel
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            URL::forceHttps();
        }
    }
}
