<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\VerificationServiceInterface;
use App\Services\VerificationService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(VerificationServiceInterface::class, VerificationService::class);
        
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
