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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 30)->nullable()->after('email');
            $table->foreignId('preferred_branch_id')->nullable()->after('phone')->constrained('branches')->nullOnDelete();
            $table->unsignedInteger('loyalty_points')->default(0)->after('preferred_branch_id');
            $table->boolean('is_blocked')->default(false)->after('loyalty_points');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('preferred_branch_id');
            $table->dropColumn(['phone', 'loyalty_points', 'is_blocked']);
        });
    }
};
