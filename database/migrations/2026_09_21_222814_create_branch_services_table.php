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
        Schema::create('branch_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('provider')->nullable();
            $table->string('account')->nullable();
            $table->string('service_number')->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('frequency')->default('Mensual');
            $table->unsignedTinyInteger('billing_day')->nullable();
            $table->unsignedTinyInteger('due_day')->nullable();
            $table->string('status')->default('active');
            $table->string('contact_name')->nullable();
            $table->string('contact_phone')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_services');
    }
};
