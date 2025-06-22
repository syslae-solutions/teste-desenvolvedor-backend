<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void // cria as tabelas e as colunas
    {
        Schema::create('veiculos', function (Blueprint $table) {
            $table->id(); // essa é a chave primária (PK)

            // adicionar os novos campos da tabela
            $table->string('placa')->unique(); // cada veículo tem uma placa e ela é única
            $table->string('cor'); // cor do veículo
            $table->string('modelo'); // modelo do veículo
            $table->string('tipo'); // veículo pode ser uma moto ou carro

            $table->timestamps(); // cria as colunas created_at e updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void // apaga a tabela
    {
        Schema::dropIfExists('veiculos');
    }
};
