<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tarifa extends Model
{
    use HasFactory;

    protected $table = 'tarifas';

    public $timestamps = false;

    protected $dateFormat = 'Y-m-d';

    protected $fillable = [
        'descricao',
        'preco_m3',
        'taxa_multa_pct',
        'prazo_dias',
        'vigente_desde',
    ];

    protected function casts(): array
    {
        return [
            'preco_m3' => 'decimal:2',
            'taxa_multa_pct' => 'decimal:2',
            'prazo_dias' => 'integer',
            'vigente_desde' => 'date',
        ];
    }

    public function facturas(): HasMany
    {
        return $this->hasMany(Factura::class, 'tarifa_id');
    }
}
