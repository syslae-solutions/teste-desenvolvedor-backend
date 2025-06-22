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
        Schema::create('vagas', function (Blueprint $table) {
            $table->id();

            // Adicionando os campos para a vaga
            $table->string('codigo')->unique(); // cada vaga tem seu código e ele é único
            $table->string('rua'); // rua que se encontra a vaga
            $table->string('numero'); // numero que se encontra a vaga
            $table->string('bairro'); // bairro que se encontra a vaga
            $table->string('status')->default('livre'); // status da vaga, essa vaga pode estar livre, ocupada ou interditada

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vagas');
    }
};
