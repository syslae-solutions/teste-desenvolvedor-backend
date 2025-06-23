<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estacionamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'vaga_id',
        'veiculo_id',
        'entrada_at',
        'saida_at',
        'valor',
    ];

    protected $casts = [
        'entrada_at' => 'datetime',
        'saida_at' => 'datetime',
    ];

    /**
     * Relacionamento com a Vaga.
     */
    public function vaga()
    {
        return $this->belongsTo(Vaga::class);
    }

    /**
     * Relacionamento com o Veículo.
     */
    public function veiculo()
    {
        return $this->belongsTo(Veiculo::class);
    }
}