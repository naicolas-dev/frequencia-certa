<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Notifications\LembreteAdicionarDisciplina;

class VerificarNovosUsuarios extends Command
{
    // O nome que você usará no terminal
    protected $signature = 'app:verificar-novos-usuarios';

    protected $description = 'Envia notificação para usuários verificados sem disciplinas cadastradas';

    public function handle()
    {
        $this->info('Iniciando verificação...');

        // 1. Filtra usuários que:
        // - Têm email verificado (whereNotNull)
        // - NÃO têm disciplinas criadas (doesntHave)
        $users = User::whereNotNull('email_verified_at')
                     ->doesntHave('disciplinas')
                     ->get();

        $enviados = 0;

        foreach ($users as $user) {
            // Verifica se o usuário aceitou receber notificações (tem inscrição push)
            if ($user->pushSubscriptions()->count() > 0) {
                $user->notify(new LembreteAdicionarDisciplina());
                $enviados++;
                $this->info("Notificação enviada para: {$user->email}");
            }
        }

        $this->info("Processo finalizado. Total de notificações: {$enviados}");
    }
}