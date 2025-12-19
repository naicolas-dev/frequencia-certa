<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Frequencia extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'disciplina_id',
        'data',
        'presente', // true = veio, false = faltou
        'observacao',
    ];

    protected $casts = [
        'presente' => 'boolean',
        'data' => 'date',
    ];

    public function disciplina()
    {
        return $this->belongsTo(Disciplina::class);
    }
}