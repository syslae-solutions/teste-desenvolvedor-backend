<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // Permite relacionamentos HasMany

class Vaga extends Model
{
    use HasFactory; // Factory serve para poder popular a tabela com dados aleatórios

    protected $table = 'vagas';

    // Definir os campos da tabela vagas que podem ser preenchidos pelo usuário
    protected $fillable = [
        'codigo',
        'rua',
        'bairro',
        'numero',
        'status',
    ];

    // Created_at e Updated_at só podem ser atulizados pelo tipo datetime 
    protected $casts = [
        'updated_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    // Uma vaga pode ter muitos registros de estacionamentos, isso é uma relação de um para muitos
    public function estacionamentos() : HasMany {
        return $this->hasMany(Estacionamento::class);
    }
}