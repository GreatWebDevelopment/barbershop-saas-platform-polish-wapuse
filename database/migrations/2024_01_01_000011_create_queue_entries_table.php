<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queue_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->cascadeOnDelete();
            $table->foreignId('staff_id')->nullable()->constrained('staff')->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->string('queue_number', 10); // e.g. A001
            $table->string('customer_name');
            $table->string('customer_phone', 20);
            $table->integer('party_size')->default(1);
            $table->string('status')->default('waiting'); // waiting, called, in_service, completed, no_show, cancelled
            $table->string('stylist_preference')->default('first_available'); // first_available or staff_id
            $table->integer('position')->default(0);
            $table->integer('estimated_wait_minutes')->default(0);
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('called_at')->nullable();
            $table->timestamp('service_started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['shop_id', 'status', 'position']);
            $table->index(['shop_id', 'queue_number']);
        });

        Schema::create('queue_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('queue_entry_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // sms, push, email
            $table->string('event'); // checked_in, almost_ready, your_turn, no_show
            $table->string('status')->default('pending'); // pending, sent, failed
            $table->text('message')->nullable();
            $table->string('recipient'); // phone or email or device token
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queue_notifications');
        Schema::dropIfExists('queue_entries');
    }
};
