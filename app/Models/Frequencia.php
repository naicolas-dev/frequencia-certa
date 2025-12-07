<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Frequencia extends Model
{
    protected $table = 'frequencias';

    protected $fillable = [
        'user_id',
        'disciplina_id',
        'data_aula',
        'faltou',
    ];

    protected $casts = [
        'faltou' => 'boolean',
        'data_aula' => 'date',
    ];
    
    /**
     * Relacionamento N:1: A Frequência pertence a um Aluno.
     */
    public function aluno(): BelongsTo
    {
        // Usa a foreign key 'user_id'
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relacionamento N:1: A Frequência pertence a uma Disciplina.
     */
    public function disciplina(): BelongsTo
    {
        // Usa a foreign key 'disciplina_id'
        return $this->belongsTo(Disciplina::class);
    }
}