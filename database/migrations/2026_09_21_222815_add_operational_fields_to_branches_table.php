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
        Schema::table('branches', function (Blueprint $table) {
            $table->string('zone')->nullable()->after('name');
            $table->string('schedule')->nullable()->after('address');
            $table->string('manager_name')->nullable()->after('schedule');
            $table->unsignedSmallInteger('estimated_prep_minutes')->default(15)->after('manager_name');
            $table->boolean('allow_scheduled_orders')->default(true)->after('estimated_prep_minutes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn(['zone', 'schedule', 'manager_name', 'estimated_prep_minutes', 'allow_scheduled_orders']);
        });
    }
};
