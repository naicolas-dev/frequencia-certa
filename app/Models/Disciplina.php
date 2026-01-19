<?php

namespace App\Models;

use Carbon\Carbon;
use App\Services\CalendarioService;
use App\Models\Evento;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Disciplina extends Model
{
    use HasFactory;
    
    protected $table = 'disciplinas';

    protected $fillable = [
        'user_id',
        'nome',
        'cor',
        'data_inicio',
        'data_fim',
        'carga_horaria_total',
        'porcentagem_minima',
    ];

    //para facilitar manipulação de datas
    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date',
    ];

    public function aluno(): BelongsTo
    {
        // Usa a foreign key 'user_id'
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relacionamento 1:N: Uma Disciplina tem vários Horarios de Aula.
     */
    public function horarios(): HasMany
    {
        // Usa a foreign key 'disciplina_id' na tabela 'horario_aulas'
        return $this->hasMany(GradeHoraria::class, 'disciplina_id');
    }
    
    /**
     * Relacionamento 1:N: Uma Disciplina tem vários registros de Frequencia.
     */
// Relacionamento com Frequências (Faltas/Presenças)
    public function frequencias()
    {
        return $this->hasMany(Frequencia::class);
    }

/**
     * 🚫 QUERY-FREE ACCESSOR
     * 
     * Returns the cached/precomputed value of total projected classes.
     * This accessor NEVER queries the database.
     * 
     * Use DisciplinaStatsService::enrichWithStats() to precompute this value.
     * 
     * @return int
     */
    public function getTotalAulasPrevistasAttribute()
    {
        // Return precomputed value if available
        if ($this->getAttribute('total_aulas_previstas_cache') !== null) {
            return (int) $this->getAttribute('total_aulas_previstas_cache');
        }

        // Safe fallback: return 0 and log warning in non-production
        if (app()->environment('local')) {
            \Log::warning('total_aulas_previstas accessed without precomputation', [
                'disciplina_id' => $this->id,
                'trace' => collect(debug_backtrace())->take(3)->pluck('file', 'function')
            ]);
        }
        
        return 0;
    }

    /**
     * 🚫 QUERY-FREE ACCESSOR
     * 
     * Computes attendance rate from preloaded counts or loaded relations.
     * This accessor NEVER triggers database queries.
     * 
     * Preload using: withCount(['frequencias as total_aulas_realizadas', ...])
     * 
     * @param mixed $value
     * @return float
     */
    public function getTaxaPresencaAttribute($value): float
    {
        // 1. If explicitly set via setAttribute (e.g., from controller), use it
        if ($value !== null) {
            return (float) $value;
        }

        // 2. If preloaded via withCount, compute from attributes
        if ($this->getAttribute('total_aulas_realizadas') !== null 
            && $this->getAttribute('total_faltas') !== null) {
            $total = (int) $this->getAttribute('total_aulas_realizadas');
            $faltas = (int) $this->getAttribute('total_faltas');

            if ($total === 0) return 0.0; // Changed: return 0.0 instead of 100.0

            $presencas = $total - $faltas;
            return round(($presencas / $total) * 100, 1);
        }

        // 3. Safe in-memory path: if frequencias relation is already loaded, use it
        if ($this->relationLoaded('frequencias')) {
            $total = $this->frequencias->count();
            
            if ($total === 0) return 0.0;

            $presencas = $this->frequencias->where('presente', true)->count();
            return round(($presencas / $total) * 100, 1);
        }

        // 4. 🚨 Data not preloaded - safe fallback
        if (app()->environment('local')) {
            throw new \RuntimeException(
                "taxa_presenca accessed without preloaded data on Disciplina #{$this->id}. " .
                "Use withCount(['frequencias as total_aulas_realizadas', ...]) or eager load 'frequencias'."
            );
        }

        // Production fallback: log warning and return safe default
        \Log::warning('taxa_presenca accessed without preloaded data', [
            'disciplina_id' => $this->id,
            'trace' => collect(debug_backtrace())->take(3)->pluck('file', 'function')
        ]);
        
        return 0.0;
    }

}