<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GradeHoraria extends Model
{
    protected $table = 'horario_aulas';

    protected $fillable = [
        'disciplina_id',
        'dia_semana',
        'hora_inicio',
        'hora_fim',
    ];
    
    protected $casts = [
        'dia_semana' => 'integer',
        'hora_inicio' => 'datetime',
        'hora_fim' => 'datetime',
    ];

    /**
     * Relacionamento N:1: Um Horario de Aula pertence a uma Disciplina.
     */
    public function disciplina(): BelongsTo
    {
        // Usa a foreign key 'disciplina_id'
        return $this->belongsTo(Disciplina::class);
    }
}