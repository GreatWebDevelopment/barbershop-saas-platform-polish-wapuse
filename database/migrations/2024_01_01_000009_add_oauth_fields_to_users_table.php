<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('stripe_account_id')->nullable();
            $table->timestamp('stripe_connected_at')->nullable();
            $table->boolean('stripe_livemode')->default(false);
            $table->string('paypal_merchant_id')->nullable();
            $table->timestamp('paypal_connected_at')->nullable();
            $table->boolean('paypal_payments_receivable')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'stripe_account_id', 'stripe_connected_at', 'stripe_livemode',
                'paypal_merchant_id', 'paypal_connected_at', 'paypal_payments_receivable',
            ]);
        });
    }
};
