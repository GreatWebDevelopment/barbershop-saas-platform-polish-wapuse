<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable()->after('name');
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->boolean('booked_online')->default(false)->after('is_walkin');
            $table->string('recurrence_type')->default('none')->after('notes'); // none, weekly, biweekly, monthly
            $table->date('recurrence_end_date')->nullable()->after('recurrence_type');
        });
    }

    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn('slug');
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['booked_online', 'recurrence_type', 'recurrence_end_date']);
        });
    }
};
