<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Builder;
use NotificationChannels\WebPush\HasPushSubscriptions;
use App\Models\Concerns\HasAiCredits;

class User extends Authenticatable implements MustVerifyEmail, FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, Prunable, HasPushSubscriptions, HasAiCredits;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'has_seen_intro',
        'has_completed_tour',
        'estado',
        'ano_letivo_inicio',
        'ano_letivo_fim',
        'current_streak',
        'max_streak',
        'last_streak_date',
        'badges',
        'ai_credits',
        'credits_reset_at',
    ];

    public function prunable(): Builder
    {
        return static::whereNull('email_verified_at')
                     ->where('created_at', '<=', now()->subDay());
    }
    

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
            'ano_letivo_inicio' => 'date',
            'ano_letivo_fim' => 'date',
            'last_streak_date' => 'date',
            'credits_reset_at' => 'datetime',
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

    public function canAccessPanel(Panel $panel): bool
    {
        // Só deixa entrar quem tiver este e-mail específico
        return $this->email === config('admin.email');
    }

    public function badges()
    {
        return $this->belongsToMany(Badge::class)->withPivot('earned_at');
    }

    // Helper para facilitar a verificação
    public function hasBadge(string $code): bool
    {
        // Usa a coleção carregada em memória para evitar N+1 queries se já estiver carregado
        return $this->badges->contains('code', $code);
    }
}
