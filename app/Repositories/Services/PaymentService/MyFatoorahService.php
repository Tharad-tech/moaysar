<?php

namespace App\Repositories\Services\MyFatoorahService;

use App\Models\{Order, User};
use Illuminate\Support\Facades\DB;
use App\Repositories\Interfaces\Payment\IPaymentGateway;
use App\Repositories\Services\PaymentService\BasePaymentGateway;


class MyFatoorahService extends BasePaymentGateway implements IPaymentGateway
{
    /**
     * Initializes a payment transaction with the MyFatoorah payment gateway.
     *
     * @param User $user The user initiating the payment.
     * @param Order $order The order for which the payment is being made.
     * @return array The payment data returned from the MyFatoorah API.
     */
    public function initializePayment(User $user, Order $order): array
    {
        $paymentData = $this->getPaymentData($user, $order);
        $credentials = 'Bearer ' . env('MYFATOORAH_API_KEY');
        $response = $this->prepareRequest(
            'POST',
            env('MYFATOORAH_BASE_URL') . 'v2/sendpayment',
            $credentials,
            $paymentData
        );

        $data = json_decode($response->getBody(), true);
        if (isset($data['Data']['InvoiceURL'])) {
            DB::commit();
            return $data['Data'];
        }

        throw new \Exception(__('Failed to initialize payment.'));
    }

    /**
     * Handles the payment callback from the MyFatoorah payment gateway.
     *
     * @param \Illuminate\Http\Request $request The incoming payment callback request.
     * @return array The response data from the MyFatoorah API.
     */
    public function handleCallback($request): array
    {
        $credentials = 'Bearer ' . env('MYFATOORAH_API_KEY');
        $response = $this->prepareRequest(
            'POST',
            env('MYFATOORAH_BASE_URL') . 'v2/getPaymentStatus',
            $credentials,
            ['key' => $request->paymentId, 'keyType' => 'paymentId']
        );

        return json_decode($response->getBody(), true);
    }

    /**
     * Processes a refund for the specified order.
     *
     * @param Order $order The order for which the refund is being processed.
     * @return array The response data from the MyFatoorah API.
     */
    public function processRefund(Order $order): array
    {
        $refundData = $this->getRefundData($order);
        $credentials = 'Bearer ' . env('MYFATOORAH_API_KEY');
        $response = $this->prepareRequest(
            'POST',
            env('MYFATOORAH_BASE_URL') . 'v2/MakeRefund',
            $credentials,
            $refundData
        );

        return json_decode($response->getBody(), true);
    }

    private function getPaymentData(User $user, Order $order): array
    {
        return [
            'CustomerName' => $user->name,
            'NotificationOption' => 'LNK',
            'InvoiceValue' => $order->total_price,
            'CurrencyCode' => 'SAR',
            'CallBackUrl' => route('payment.success', ['order_id' => $order->uuid]),
            'ErrorUrl' => route('payment.failure', ['order_id' => $order->uuid]),
        ];
    }

    private function getRefundData(Order $order): array
    {
        return [
            'KeyType' => 'InvoiceId',
            'Key' => $order?->invoice?->inv_number,
            'Amount' => $order?->invoice?->due_amount,
            'Comments' => 'Cancellation refund',
        ];
    }
}
