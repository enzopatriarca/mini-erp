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
        Schema::create('pedidos', function (Blueprint $t) {
            $t->id();
            $t->json('itens');
            $t->decimal('subtotal', 10, 2);
            $t->decimal('frete', 10, 2);
            $t->decimal('total', 10, 2);
            $t->string('cep', 9);
            $t->string('endereco');
            $t->string('status')->default('pendente');
            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
