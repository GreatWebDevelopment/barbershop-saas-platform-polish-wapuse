<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('preferred_stylist_id')->nullable()->constrained('staff')->nullOnDelete();
            $table->decimal('total_spent', 10, 2)->default(0);
            $table->timestamp('last_visit_at')->nullable();
        });

        Schema::table('shops', function (Blueprint $table) {
            $table->integer('loyalty_points_per_dollar')->default(1);
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('preferred_stylist_id');
            $table->dropColumn(['total_spent', 'last_visit_at']);
        });

        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn('loyalty_points_per_dollar');
        });
    }
};
