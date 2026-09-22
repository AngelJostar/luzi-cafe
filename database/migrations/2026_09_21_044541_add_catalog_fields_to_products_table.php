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
            $table->string('short_name')->nullable()->after('name');
            $table->text('commercial_description')->nullable()->after('description');
            $table->decimal('promo_price', 10, 2)->nullable()->after('base_price');
            $table->decimal('estimated_cost', 10, 2)->default(0)->after('promo_price');
            $table->string('status')->default('active')->after('estimated_cost');
            $table->string('barcode')->nullable()->unique()->after('sku');
            $table->string('internal_code')->nullable()->unique()->after('barcode');
            $table->json('tags')->nullable()->after('internal_code');
            $table->unsignedSmallInteger('estimated_prep_minutes')->default(8)->after('tags');
            $table->unsignedSmallInteger('max_per_order')->default(12)->after('estimated_prep_minutes');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropUnique(['barcode']);
            $table->dropUnique(['internal_code']);
            $table->dropColumn(['short_name', 'commercial_description', 'promo_price', 'estimated_cost', 'status', 'barcode', 'internal_code', 'tags', 'estimated_prep_minutes', 'max_per_order']);
        });
    }
};
