<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'has_seen_intro',
        'has_completed_tour',
        'estado',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'has_seen_intro' => 'boolean',
            'has_completed_tour' => 'boolean',
        ];
    }


    /* Relacionamento 1:N: Um Aluno tem várias Disciplinas.
     */
    public function disciplinas(): HasMany
    {
        // Usa a foreign key 'user_id' na tabela 'disciplinas'
        return $this->hasMany(Disciplina::class);
    }

    /**
     * Relacionamento 1:N: Um Aluno tem vários registros de Frequencia.
     */
    public function frequencias(): HasMany
    {
        // Usa a foreign key 'user_id' na tabela 'frequencias'
        return $this->hasMany(Frequencia::class);
    }
}
