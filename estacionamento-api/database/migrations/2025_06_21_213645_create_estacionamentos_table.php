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
        Schema::create('estacionamentos', function (Blueprint $table) {
            $table->id();

            // campos do estacionamento
            $table->foreignId('vaga_id')->constrained('vagas'); // a vaga é uma chave estrangeira para a tabela de vagas
            $table->foreignId('veiculo_id')->constrained('veiculos'); // veículo é uma chave estrangeira para a tabela de veículos
            $table->dateTime('entrada'); // entrada do veículo na vaga
            $table->dateTime('saida')->nullable(); // saida do veículo na vaga
            $table->integer('tempo_total')->nullable(); // tempo que o veículo ficou na vaga
            $table->decimal('valor', 8, 2)->nullable(); // valor a ser pago pelo tempo que o veículo ficou na vaga

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estacionamentos');
    }
};
