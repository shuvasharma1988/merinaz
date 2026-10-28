<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Recaptcha implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            // Ping Google's API. Removed 'remoteip' to prevent local loopback rejections.
            $response = Http::withoutVerifying()->asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => config('services.recaptcha.secret_key'),
                'response' => $value,
            ]);

            $body = $response->json();

            if (!$response->successful() || !isset($body['success']) || !$body['success']) {
                Log::error('reCAPTCHA validation failed', ['google_response' => $body]);
                $fail('The reCAPTCHA verification failed. Please try again.');
            }
        } catch (\Exception $e) {
            Log::error('reCAPTCHA Exception: ' . $e->getMessage());
            $fail('The reCAPTCHA verification failed. Please try again.');
        }
    }
}
