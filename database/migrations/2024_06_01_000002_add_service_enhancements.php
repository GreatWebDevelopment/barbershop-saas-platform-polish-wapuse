<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('skill_level')->default('intermediate');
            $table->string('image_path')->nullable();
            $table->integer('sort_order')->default(0);
            $table->integer('booking_count')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['skill_level', 'image_path', 'sort_order', 'booking_count']);
        });
    }
};
