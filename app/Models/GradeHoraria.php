<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradeHoraria extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'disciplina_id', 'dia_semana', 'horario_inicio', 'horario_fim'];

    public function disciplina()
    {
        return $this->belongsTo(Disciplina::class);
    }
}