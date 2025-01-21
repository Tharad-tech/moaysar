<?php

namespace App\Providers;

use App\Repositories\Interfaces\Payment\IPaymentGateway;
use App\Repositories\Services\PaymentService\HyperPayService;
use  App\Repositories\Services\PaymentService\MoyasarService;
use App\Repositories\Services\MyFatoorahService\MyFatoorahService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

        $this->app->bind(IPaymentGateway::class, function ($app) {
            $paymentGateway = config('payment.default_gateway');

            switch ($paymentGateway) {
                case 'moyasar':
                    return new MoyasarService();
                case 'hyperpay':
                    return new HyperPayService();
                default:
                    throw new \Exception("Unsupported payment gateway: $paymentGateway");
            }
        });

       

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->bind(IPaymentGateway::class, MoyasarService::class);
    }
}
