<?php

namespace App\Repositories\Services\PaymentService;

use App\Repositories\Interfaces\Payment\IPaymentGateway;

class PaymentService
{
    protected IPaymentGateway $gateway;

    public function __construct(IPaymentGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    public function initializePayment($user, $order)
    {
        return $this->gateway->initializePayment($user, $order);
    }

    public function handleCallback($request)
    {
        return $this->gateway->handleCallback($request);
    }

    public function processRefund($order)
    {
        return $this->gateway->processRefund($order);
    }
}
