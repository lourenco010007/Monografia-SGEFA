<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const PROFILE_ADMIN = 'admin';

    public const PROFILE_AGENT = 'agente';

    public const PROFILE_TENANT = 'utente';

    protected $table = 'utilizadores';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nome',
        'email',
        'password',
        'perfil',
        'activo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'activo' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->perfil === self::PROFILE_ADMIN;
    }

    public function utente(): HasOne
    {
        return $this->hasOne(Utente::class, 'utilizador_id');
    }

    public function leiturasRegistadas(): HasMany
    {
        return $this->hasMany(Leitura::class, 'agente_id');
    }
}
