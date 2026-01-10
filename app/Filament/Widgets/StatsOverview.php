<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Frequencia;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    // Atualização em tempo real
    protected ?string $pollingInterval = '15s';

    protected function getStats(): array
    {
        // Helper para gerar o array do gráfico (DRY)
        $gerarGrafico = function ($query, $colunaData = 'created_at') {
            $dados = $query
                ->selectRaw("DATE($colunaData) as dia, count(*) as total")
                ->where($colunaData, '>=', now()->subDays(7))
                ->groupBy('dia')
                ->pluck('total', 'dia')
                ->toArray();

            $grafico = [];
            for ($i = 6; $i >= 0; $i--) {
                $data = now()->subDays($i)->format('Y-m-d');
                $grafico[] = $dados[$data] ?? 0;
            }

            return $grafico;
        };

        return [
            Stat::make('Total de Alunos', User::count())
                ->description('Cadastros nos últimos 7 dias')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart($gerarGrafico(User::query(), 'created_at')),

            Stat::make('Notificações Ativas', User::has('pushSubscriptions')->count())
                ->description('Dispositivos conectados')
                ->descriptionIcon('heroicon-m-device-phone-mobile')
                ->color('primary')
                ->chart($gerarGrafico(User::has('pushSubscriptions'), 'created_at')),

            Stat::make('Faltas Monitoradas', Frequencia::where('presente', false)->count())
                ->description('Ausências na última semana')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('danger')
                // Usa a data real da falta, não a data de cadastro no sistema
                ->chart($gerarGrafico(Frequencia::where('presente', false), 'data')),
        ];
    }
}