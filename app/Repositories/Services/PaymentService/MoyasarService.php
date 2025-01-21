<?php

namespace App\Repositories\Services\PaymentService;

use App\Models\{Order, User};
use Illuminate\Support\Facades\DB;
use App\Repositories\Interfaces\Payment\IPaymentGateway;
use App\Repositories\Services\PaymentService\BasePaymentGateway;

class MoyasarService extends BasePaymentGateway implements IPaymentGateway
{
    /**
     * Initializes a payment transaction with the Moyasar payment gateway.
     *
     * @param User $user The user initiating the payment.
     * @param Order $order The order for which the payment is being made.
     * @return array The payment data returned from the Moyasar API.
     */
    public function initializePayment(User $user, Order $order): array
    {
        $paymentData = $this->getPaymentData($user, $order);
        $credentials = 'Basic ' . base64_encode(env('MOYASAR_API_KEY') . ':');
        $response = $this->prepareRequest(
            'POST',
            env('MOYASAR_BASE_URL') . 'v1/payments',
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
     * Handles the payment callback from the Moyasar payment gateway.
     *
     * @param \Illuminate\Http\Request $request The incoming payment callback request.
     * @return array The response data from the Moyasar API.
     */
    public function handleCallback($request): array
    {
        // Moyasar usually sends data directly in the callback request body.
        return $request->all();
    }

    /**
     * Processes a refund for the specified order.
     *
     * @param Order $order The order for which the refund is being processed.
     * @return array The response data from the Moyasar API.
     */
    public function processRefund(Order $order): array
    {
        $refundData = $this->getRefundData($order);
        $credentials = 'Basic ' . base64_encode(env('MOYASAR_API_KEY') . ':');
        $response = $this->prepareRequest(
            'POST',
            env('MOYASAR_BASE_URL') . 'v1/refunds',
            $credentials,
            $refundData
        );

        return json_decode($response->getBody(), true);
    }

    private function getPaymentData(User $user, Order $order): array
    {
        return [
            'amount' => $order->total_price * 100, // Convert to smallest currency unit
            'currency' => 'SAR',
            'source' => 'creditcard', // Example source type
            'callback_url' => route('payment.success', ['order_id' => $order->uuid]),
            'description' => 'Order Payment for ' . $user->name,
        ];
    }

    private function getRefundData(Order $order): array
    {
        return [
            'payment_id' => $order?->payment?->payment_id,
            'amount' => $order?->payment?->amount * 100, // Convert to smallest currency unit
            'reason' => 'Cancellation refund',
        ];
    }
}