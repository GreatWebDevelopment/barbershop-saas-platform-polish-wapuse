<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->string('stripe_account_id')->nullable();
            $table->boolean('stripe_enabled')->default(false);
            $table->string('paypal_email')->nullable();
            $table->string('paypal_client_id')->nullable();
            $table->boolean('paypal_enabled')->default(false);
            $table->json('payment_methods')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn(['stripe_account_id', 'stripe_enabled', 'paypal_email', 'paypal_client_id', 'paypal_enabled', 'payment_methods']);
        });
    }
};
