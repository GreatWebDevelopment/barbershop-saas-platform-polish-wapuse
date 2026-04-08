<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('logo_url')->nullable();
            $table->string('website')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->text('description')->nullable();
            $table->json('branding')->nullable(); // primary_color, secondary_color, font
            $table->json('settings')->nullable(); // notification prefs, queue config defaults
            $table->timestamps();
        });

        Schema::table('shops', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->decimal('latitude', 10, 7)->nullable()->after('zip');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->string('timezone')->default('America/Chicago')->after('longitude');
            $table->integer('queue_capacity')->default(30)->after('timezone');
            $table->json('amenities')->nullable()->after('queue_capacity'); // wifi, tv, drinks, etc
            $table->boolean('queue_enabled')->default(true)->after('amenities');
            $table->string('display_passcode', 6)->nullable()->after('queue_enabled');
        });

        Schema::table('staff', function (Blueprint $table) {
            $table->string('queue_status')->default('off_duty')->after('status'); // active, on_break, off_duty
            $table->foreignId('current_queue_entry_id')->nullable()->after('queue_status');
        });
    }

    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn(['queue_status', 'current_queue_entry_id']);
        });
        Schema::table('shops', function (Blueprint $table) {
            $table->dropConstrainedForeignId('company_id');
            $table->dropColumn(['latitude', 'longitude', 'timezone', 'queue_capacity', 'amenities', 'queue_enabled', 'display_passcode']);
        });
        Schema::dropIfExists('companies');
    }
};
