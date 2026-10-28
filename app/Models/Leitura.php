<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Leitura extends Model
{
    use HasFactory;

    protected $table = 'leituras';

    public $timestamps = false;

    protected $dateFormat = 'Y-m-d';

    protected $fillable = [
        'contador_id',
        'agente_id',
        'leitura_anterior',
        'leitura_actual',
        'consumo_m3',
        'data_leitura',
    ];

    protected function casts(): array
    {
        return [
            'leitura_anterior' => 'decimal:3',
            'leitura_actual' => 'decimal:3',
            'consumo_m3' => 'decimal:3',
            'data_leitura' => 'date',
        ];
    }

    public function contador(): BelongsTo
    {
        return $this->belongsTo(Contador::class, 'contador_id');
    }

    public function agente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agente_id');
    }

    public function factura(): HasOne
    {
        return $this->hasOne(Factura::class, 'leitura_id');
    }
}
