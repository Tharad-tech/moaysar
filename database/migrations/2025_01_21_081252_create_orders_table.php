<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->nullable()->unique();
            $table->boolean('is_now')->default(false);
            $table->timestamp('date')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('payment_method');
            $table->string('water_type');
            $table->float('lat')->nullable();
            $table->float('lng')->nullable();
            $table->longText('location')->nullable();
            $table->string('status')->default('pending');
            $table->boolean('client_completed')->nullable();
            $table->boolean('provider_completed')->nullable();
            $table->float('subtotal')->nullable(); // total without any addition like(vat, coupon,....) the service it self without any commission or discount
            $table->float('extra_price')->nullable();
            $table->float('app_profit')->nullable();
            $table->float('app_percent')->nullable();
            $table->float('coupon_discount')->nullable();
            $table->float('total_price')->nullable();
            $table->float('vat_price')->nullable();
            $table->float('vat_percent')->nullable();
            $table->float('provider_profit')->nullable();
            $table->float('provider_percent')->nullable();
            $table->float('client_rate')->nullable(); // reflect openion of provider about the client
            $table->float('driver_rate')->nullable(); // reflect openion of client about the provider
            $table->string('cancel_reason')->nullable();
            $table->string('reject_reason')->nullable();
            $table->string('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
