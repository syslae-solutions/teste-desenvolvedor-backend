<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // Permite relacionamentos HasMany

class Veiculo extends Model
{
    use HasFactory;

    protected $table = 'veiculos';

    // campos que podem ser editados da tabela veiculos
    protected $fillable = [
        'placa',
        'modelo',
        'cor',
        'tipo',
    ];

    // Created_at e Updated_at só podem ser atulizados pelo tipo datetime 
    protected $casts = [
        'updated_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    // um veículo pode ter muitos registros em um estacionamento ao longo do tempo
    public function estacionamentos() : HasMany {
        return $this->hasMany(Estacionamento::class);
    }
}