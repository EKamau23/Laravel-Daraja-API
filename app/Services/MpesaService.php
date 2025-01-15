<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MpesaService
{
    /**
     * The base URL for M-Pesa API.
     */
    public $base_url;

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->base_url = env('MPESA_ENV') == 'production'
            ? "https://api.safaricom.co.ke"
            : "https://sandbox.safaricom.co.ke";
    }

    /**
     * Authenticate and get the access token.
     */
    public function authenticate(): mixed
    {
        $url = $this->base_url . "/oauth/v1/generate?grant_type=client_credentials";

        $response = Http::withBasicAuth(
            env('MPESA_CONSUMER_KEY'),
            env('MPESA_CONSUMER_SECRET')
        )->get($url);

        if ($response->successful()) {
            return $response->json();
        }

        return [
            'error' => true,
            'message' => $response->body(),
        ];
    }
}
