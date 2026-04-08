<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_add_ons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->decimal('price', 8, 2);
            $table->integer('duration_minutes');
            $table->timestamps();
        });

        Schema::create('service_add_on_mappings', function (Blueprint $table) {
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_add_on_id')->constrained('service_add_ons')->cascadeOnDelete();
            $table->primary(['service_id', 'service_add_on_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_add_on_mappings');
        Schema::dropIfExists('service_add_ons');
    }
};
