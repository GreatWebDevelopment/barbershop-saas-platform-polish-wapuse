<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('title')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->integer('years_experience')->default(0);
            $table->decimal('hourly_rate', 8, 2)->default(0);
            $table->integer('commission_percent')->default(0);
            $table->json('specialties')->nullable();
            $table->string('status')->default('active');
            $table->string('avatar_url')->nullable();
            $table->text('bio')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
