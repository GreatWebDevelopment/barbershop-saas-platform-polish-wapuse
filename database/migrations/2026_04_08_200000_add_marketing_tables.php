<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Reviews table
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('comment')->nullable();
            $table->string('source', 20)->default('internal'); // google, yelp, internal
            $table->timestamps();
        });

        // Loyalty rewards / redemption tiers
        Schema::create('loyalty_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedInteger('points_required');
            $table->decimal('discount_amount', 8, 2);
            $table->string('discount_type', 20)->default('fixed'); // fixed, percentage
            $table->timestamps();
        });

        // Email campaigns
        Schema::create('email_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->cascadeOnDelete();
            $table->string('subject');
            $table->longText('body_html');
            $table->string('segment', 20)->default('all'); // all, regulars, lapsed, new
            $table->string('status', 20)->default('draft'); // draft, sent
            $table->timestamp('sent_at')->nullable();
            $table->unsignedInteger('recipient_count')->default(0);
            $table->timestamps();
        });

        // Add loyalty config fields to shops
        Schema::table('shops', function (Blueprint $table) {
            $table->boolean('loyalty_enabled')->default(false)->after('loyalty_points_per_dollar');
            $table->decimal('loyalty_redemption_value', 8, 2)->default(5.00)->after('loyalty_enabled');
            $table->unsignedInteger('referral_bonus_points')->default(50)->after('loyalty_redemption_value');
        });

        // Add referral tracking to customers
        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('referred_by_id')->nullable()->after('preferred_stylist_id')
                ->constrained('customers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['referred_by_id']);
            $table->dropColumn('referred_by_id');
        });

        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn(['loyalty_enabled', 'loyalty_redemption_value', 'referral_bonus_points']);
        });

        Schema::dropIfExists('email_campaigns');
        Schema::dropIfExists('loyalty_rewards');
        Schema::dropIfExists('reviews');
    }
};
