<?php

namespace App\Providers;

use App\Services\MNotifySmsService;
use App\Services\MockSmsService;
use App\Services\SmsServiceInterface;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SmsServiceInterface::class, function () {
            $apiKey = (string) config('services.mnotify.api_key');
            $senderId = (string) config('services.mnotify.sender_id');

            if ($apiKey !== '' && $senderId !== '') {
                return new MNotifySmsService();
            }

            return new MockSmsService();
        });
    }

    public function boot()
    {
        Schema::defaultStringLength(191);
    }
}
