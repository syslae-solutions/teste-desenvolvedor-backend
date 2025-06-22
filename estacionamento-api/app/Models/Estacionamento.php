<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; 

class Estacionamento extends Model
{

    use HasFactory;

    // a tabela a qual está associado o Modelo é estacionamentos
    protected $table = 'estacionamentos';

    // campos da tabela estacionamentos que podem ser preenchidos
    protected $fillable = [
        'vaga_id',
        'veiculo_id',
        'entrada',
        'saida',
        'tempo_total',
        'valor',
    ];

    protected $casts = [
        'entrada'=>'datetime',
        'saida'=>'datetime',
        'updated_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    // A relação entre o estacionamento e o veículo é de pertencimento
    public function veiculo() : BelongsTo {
        return $this->belongsTo(Veiculo::class);
    }

    // A relação entre o estacionamento e a vaga é de pertencimento
    public function vaga() : BelongsTo {
        return $this->belongsTo(Vaga::class);
    }
}
