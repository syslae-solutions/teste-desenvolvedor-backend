<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Veiculo extends Model
{
    use HasFactory;

    protected $fillable = [
        'placa',
        'modelo',
        'cor',
        'tipo',
    ];

    /**
     * Define o relacionamento com as movimentações de estacionamento.
     */
    public function estacionamentos()
    {
        return $this->hasMany(Estacionamento::class);
    }
}