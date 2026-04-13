<?php

namespace App\Providers;

use App\Services\MockSmsService;
use App\Services\SmsServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SmsServiceInterface::class, MockSmsService::class);
    }

    public function boot(): void
    {
        //
    }
}
