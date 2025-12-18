<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Disciplina extends Model
{
    protected $table = 'disciplinas';

    protected $fillable = [
        'user_id',
        'nome',
        'carga_horaria_total',
        'porcentagem_minima',
    ];

    /**
     * Relacionamento N:1: Uma Disciplina pertence a um Aluno.
     */
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
        return $this->hasMany(HorarioAula::class);
    }
    
    /**
     * Relacionamento 1:N: Uma Disciplina tem vários registros de Frequencia.
     */
    public function frequencias(): HasMany
    {
        // Usa a foreign key 'disciplina_id' na tabela 'frequencias'
        return $this->hasMany(Frequencia::class);
    }
}