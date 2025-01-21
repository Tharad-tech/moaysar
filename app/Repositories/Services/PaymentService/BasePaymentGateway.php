<?php

namespace App\Repositories\Services\PaymentService;
use GuzzleHttp\Client;

abstract class BasePaymentGateway
{
    protected function prepareRequest($method, $url, $credentials, $data)
    {
        $client = new Client();
        return $client->request($method, $url, [
            'headers' => [
                'Authorization' => $credentials,
                'Content-Type' => 'application/json',
            ],
            'json' => $data,
        ]);
    }
}
