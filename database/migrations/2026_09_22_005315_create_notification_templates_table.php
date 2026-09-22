<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('title');
            $table->text('body')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('notification_templates')->insert([
            ['code' => 'orderReceived', 'title' => 'Pedido recibido en sucursal', 'body' => 'Recibimos tu pedido {{folio}} y pronto comenzaremos a prepararlo.', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'orderReady', 'title' => 'Tu pedido está listo', 'body' => 'Tu pedido {{folio}} está listo para recoger.', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'outOfStock', 'title' => 'Producto agotado temporalmente', 'body' => 'Un producto de tu selección no está disponible por el momento.', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_templates');
    }
};
