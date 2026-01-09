<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Notifications\LembreteRegistrarFrequencia;
use Carbon\Carbon;

class LembreteFrequenciaDiaria extends Command
{
    protected $signature = 'app:lembrete-frequencia-diaria';
    protected $description = 'Verifica quem tem aula hoje e envia notificação de frequência';

    public function handle()
    {
        // Usa dayOfWeekIso para alinhar com o seu Model (1=Segunda ... 7=Domingo)
        $diaSemanaHoje = Carbon::now()->dayOfWeekIso;
        
        $this->info("Iniciando verificação para o dia da semana: {$diaSemanaHoje}");

        $users = User::whereNotNull('email_verified_at')
            ->whereHas('disciplinas', function ($query) use ($diaSemanaHoje) {
                // CORREÇÃO: Usar 'horarios' em vez de 'gradeHorarias'
                $query->whereHas('horarios', function ($q) use ($diaSemanaHoje) {
                    $q->where('dia_semana', $diaSemanaHoje);
                });
            })
            ->get();

        $count = 0;
        foreach ($users as $user) {
            // Verifica se o usuário aceita notificações push
            if ($user->pushSubscriptions()->count() > 0) {
                $user->notify(new LembreteRegistrarFrequencia());
                $count++;
            }
        }

        $this->info("Notificações enviadas: {$count}");
    }
}