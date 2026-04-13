<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class MockSmsService implements SmsServiceInterface
{
    public function send(string $phoneNumber, string $message): bool
    {
        Log::channel('stack')->info('SMS sent (mock)', [
            'to'      => $phoneNumber,
            'message' => $message,
        ]);

        return true;
    }
}
