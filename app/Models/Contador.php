<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contador extends Model
{
    use HasFactory;

    public const STATE_ACTIVE = 'activo';

    public const STATE_INACTIVE = 'inactivo';

    protected $table = 'contadores';

    public $timestamps = false;

    protected $dateFormat = 'Y-m-d';

    protected $fillable = [
        'utente_id',
        'numero_serie',
        'localizacao',
        'data_instalacao',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'data_instalacao' => 'date',
        ];
    }

    public function utente(): BelongsTo
    {
        return $this->belongsTo(Utente::class, 'utente_id');
    }

    public function leituras(): HasMany
    {
        return $this->hasMany(Leitura::class, 'contador_id');
    }
}
