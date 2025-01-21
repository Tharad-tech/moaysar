<?php

namespace App\Repositories\Interfaces\Payment;

use App\Models\{Order, User};

interface IPaymentGateway
{
    public function initializePayment(User $user, Order $order): array;

    public function handleCallback($request): array;

    public function processRefund(Order $order): array;
}
