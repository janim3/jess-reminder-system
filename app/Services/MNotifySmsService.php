<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MNotifySmsService implements SmsServiceInterface
{
    public function send(string $phoneNumber, string $message): bool
    {
        $apiKey = (string) config('services.mnotify.api_key');
        $sender = (string) config('services.mnotify.sender_id');
        $url = rtrim((string) config('services.mnotify.base_url', 'https://api.mnotify.com'), '/');

        if ($apiKey === '' || $sender === '') {
            Log::warning('MNotify SMS configuration is incomplete.', [
                'has_api_key' => $apiKey !== '',
                'has_sender_id' => $sender !== '',
            ]);

            return false;
        }

        try {
            $response = Http::acceptJson()
                ->asJson()
                ->post($url . '/api/sms/quick?key=' . urlencode($apiKey), [
                    'recipient' => [$phoneNumber],
                    'sender' => $sender,
                    'message' => $message,
                    'is_schedule' => false,
                ]);
        } catch (ConnectionException $exception) {
            Log::error('MNotify SMS connection failed.', [
                'message' => $exception->getMessage(),
                'to' => $phoneNumber,
            ]);

            return false;
        }

        if ($response->failed()) {
            Log::error('MNotify SMS request failed.', [
                'status' => $response->status(),
                'body' => $response->body(),
                'to' => $phoneNumber,
            ]);

            return false;
        }

        return true;
    }
}
