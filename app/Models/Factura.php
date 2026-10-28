<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Factura extends Model
{
    use HasFactory;

    protected $table = 'facturas';

    public $timestamps = false;

    protected $dateFormat = 'Y-m-d';

    protected $fillable = [
        'leitura_id',
        'utente_id',
        'tarifa_id',
        'numero',
        'valor_total',
        'data_emissao',
        'data_vencimento',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'valor_total' => 'decimal:2',
            'data_emissao' => 'date',
            'data_vencimento' => 'date',
        ];
    }

    public function leitura(): BelongsTo
    {
        return $this->belongsTo(Leitura::class, 'leitura_id');
    }

    public function utente(): BelongsTo
    {
        return $this->belongsTo(Utente::class, 'utente_id');
    }

    public function tarifa(): BelongsTo
    {
        return $this->belongsTo(Tarifa::class, 'tarifa_id');
    }
}
