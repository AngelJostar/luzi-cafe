<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('direct_cost', 12, 2)->default(0)->after('base_price');
            $table->decimal('gross_profit', 12, 2)->default(0)->after('direct_cost');
            $table->decimal('margin_percent', 7, 2)->default(0)->after('gross_profit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['direct_cost', 'gross_profit', 'margin_percent']);
        });
    }
};
