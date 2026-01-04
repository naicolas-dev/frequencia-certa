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

public function getTotalAulasPrevistasAttribute()
    {
        // 1. Se não houver datas definidas, não podemos projectar
        if (!$this->data_inicio || !$this->data_fim) {
            return 0;
        }

        $inicio = Carbon::parse($this->data_inicio);
        $fim = Carbon::parse($this->data_fim);

        // 2. Recupera os dias da semana que têm aula (ex: [1, 3] = Seg, Qua)
        // O unique() evita duplicados se tiveres 2 aulas no mesmo dia
        $diasAula = $this->horarios->pluck('dia_semana')->unique()->toArray();

        if (empty($diasAula)) {
            return 0;
        }

        // 3. OTIMIZAÇÃO DE PERFORMANCE 🚀
        // Buscamos todas as folgas manuais (Eventos) do período de uma só vez
        // para evitar fazer centenas de queries ao banco dentro do loop.
        $folgasManuais = Evento::where('user_id', $this->user_id)
            ->whereBetween('data', [$this->data_inicio, $this->data_fim])
            ->whereIn('tipo', ['feriado', 'sem_aula'])
            ->pluck('data')
            ->map(fn($d) => substr($d, 0, 10)) // Garante formato Y-m-d
            ->toArray();

        // 4. Prepara os feriados (Cache do CalendarioService)
        $calendarioService = app(CalendarioService::class);
        $estado = Auth::user()->estado ?? 'BR';
        
        // Carregamos feriados dos anos envolvidos (caso o semestre vire o ano)
        $anos = range($inicio->year, $fim->year);
        $feriados = [];
        
        foreach ($anos as $ano) {
            // O Service já usa cache, então isso é rápido
            $lista = $calendarioService->obterFeriados($estado, $ano);
            foreach ($lista as $f) {
                $feriados[] = $f['data'];
            }
        }

        // 5. O Grande Loop: Contagem dia a dia
        $totalAulas = 0;
        $atual = $inicio->copy();

        while ($atual->lte($fim)) {
            // Verifica se hoje é um dia de aula desta matéria (ex: Segunda)
            if (in_array($atual->dayOfWeekIso, $diasAula)) {
                $dataStr = $atual->format('Y-m-d');

                // Verifica se NÃO é folga manual E NÃO é feriado
                if (!in_array($dataStr, $folgasManuais) && !in_array($dataStr, $feriados)) {
                    $totalAulas++;
                }
            }
            $atual->addDay();
        }

        return $totalAulas;
    }

    public function getTaxaPresencaAttribute(): float
    {
        // Usa a coleção já carregada para evitar queries extras
        $totalAulas = $this->frequencias->count();
        
        if ($totalAulas === 0) {
            return 100.0; // Sem registros = 100% de presença
        }

        $presencas = $this->frequencias->where('presente', true)->count();

        return round(($presencas / $totalAulas) * 100, 1);
    }
}