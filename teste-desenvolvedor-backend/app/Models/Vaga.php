<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vaga extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'rua',
        'numero',
        'bairro',
        'status',
    ];

    /**
     * Define o relacionamento com as movimentações de estacionamento.
     */
    public function estacionamentos()
    {
        return $this->hasMany(Estacionamento::class);
    }
}
