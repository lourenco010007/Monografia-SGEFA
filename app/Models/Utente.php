<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Utente extends Model
{
    use HasFactory;

    public const STATE_ACTIVE = 'activo';

    public const STATE_INACTIVE = 'inactivo';

    protected $table = 'utentes';

    public $timestamps = false;

    protected $fillable = [
        'utilizador_id',
        'nome',
        'telefone',
        'bairro',
        'morada',
        'estado',
    ];

    public function utilizador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'utilizador_id');
    }

    public function contadores(): HasMany
    {
        return $this->hasMany(Contador::class, 'utente_id');
    }

    public function facturas(): HasMany
    {
        return $this->hasMany(Factura::class, 'utente_id');
    }
}
