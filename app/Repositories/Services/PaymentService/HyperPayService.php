<?php

namespace App\Repositories\Services\PaymentService;

use App\Models\{Order, User};
use Illuminate\Support\Facades\DB;
use App\Repositories\Interfaces\Payment\IPaymentGateway;
use App\Repositories\Services\PaymentService\BasePaymentGateway;

class HyperPayService extends BasePaymentGateway implements IPaymentGateway
{
    /**
     * Initializes a payment transaction with the HyperPay payment gateway.
     *
     * @param User $user The user initiating the payment.
     * @param Order $order The order for which the payment is being made.
     * @return array The payment data returned from the HyperPay API.
     */
    public function initializePayment(User $user, Order $order): array
    {
        $paymentData = $this->getPaymentData($user, $order);
        $credentials = 'Bearer ' . env('HYPERPAY_ACCESS_TOKEN');
        $response = $this->prepareRequest(
            'POST',
            env('HYPERPAY_BASE_URL') . 'v1/checkouts',
            $credentials,
            $paymentData
        );

        $data = json_decode($response->getBody(), true);
        if (isset($data['id'])) {
            DB::commit();
            return $data;
        }

        throw new \Exception(__('Failed to initialize payment.'));
    }

    /**
     * Handles the payment callback from the HyperPay payment gateway.
     *
     * @param \Illuminate\Http\Request $request The incoming payment callback request.
     * @return array The response data from the HyperPay API.
     */
    public function handleCallback($request): array
    {
        $credentials = 'Bearer ' . env('HYPERPAY_ACCESS_TOKEN');
        $response = $this->prepareRequest(
            'GET',
            env('HYPERPAY_BASE_URL') . 'v1/checkouts/' . $request->id . '/payment',
            $credentials
        );

        return json_decode($response->getBody(), true);
    }

    /**
     * Processes a refund for the specified order.
     *
     * @param Order $order The order for which the refund is being processed.
     * @return array The response data from the HyperPay API.
     */
    public function processRefund(Order $order): array
    {
        $refundData = $this->getRefundData($order);
        $credentials = 'Bearer ' . env('HYPERPAY_ACCESS_TOKEN');
        $response = $this->prepareRequest(
            'POST',
            env('HYPERPAY_BASE_URL') . 'v1/refunds',
            $credentials,
            $refundData
        );

        return json_decode($response->getBody(), true);
    }

    private function getPaymentData(User $user, Order $order): array
    {
        return [
            'entityId' => env('HYPERPAY_ENTITY_ID'),
            'amount' => number_format($order->total_price, 2, '.', ''),
            'currency' => 'SAR',
            'paymentType' => 'DB',
            'customer' => [
                'givenName' => $user->name,
                'email' => $user->email,
            ],

            'notificationUrl' => route('payment.callback'),
        ];
    }

    private function getRefundData(Order $order): array
    {
        return [
            'entityId' => env('HYPERPAY_ENTITY_ID'),
            'amount' => number_format($order?->payment?->amount, 2, '.', ''),
            'currency' => 'SAR',
            'paymentId' => $order?->payment?->payment_id,
            'reason' => 'Customer request refund',
        ];
    }
}