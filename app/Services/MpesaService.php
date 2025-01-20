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
    public function authorize(): mixed
    {
        $url = $this->base_url . "/oauth/v1/generate?grant_type=client_credentials";

        $response = Http::withBasicAuth(
            env('MPESA_CONSUMER_KEY'),
            env('MPESA_CONSUMER_SECRET')
        )->get($url);

        if ($response->successful()) {
            return $response->json("access_token");
        }

        return [
            'error' => true,
            'message' => $response->body(),
        ];
    }

    public function stkPush($phone,$amount,$ref="N/A"): mixed
    {
        $url = $this->base_url . "/mpesa/stkpush/v1/processrequest"; 
        $password = base64_encode(env('MPESA_SHORT_CODE') .env('MPESA_PASSKEY') . Carbon::now()->format(format: 'YmdHis'));
        $response = Http::withToken(token: $this->authorize(),type: 'Bearer')->post(url:$url,data: [
            [    
                "BusinessShortCode" => "174379",    
                "Password" => "MTc0Mzc5YmZiMjc5ZjlhYTliZGJjZjE1OGU5N2RkNzFhNDY3Y2QyZTBjODkzMDU5YjEwZjc4ZTZiNzJhZGExZWQyYzkxOTIwMTYwMjE2MTY1NjI3",    
                "Timestamp" => Carbon::now()->format(format: 'YmdHis'),    
                "TransactionType" => "CustomerPayBillOnline",    
                "Amount" => $amount,    
                "PartyA" => $phone,    
                "PartyB" => "174379",    
                "PhoneNumber" => $phone,    
                "CallBackURL" => route(name: 'mpesa.callback'),    
                "AccountReference" => $ref,    
                "TransactionDesc" => $ref
                ]
        ]); 

        return $response->json();

    }
    public function stkQuery(): void{}
}
