<?php

namespace App\Services;

interface SmsServiceInterface
{
    public function send(string $phoneNumber, string $message): bool;
}
