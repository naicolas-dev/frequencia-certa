<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\Evento;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CalendarioService
{
    // Mantido apenas por compatibilidade histórica
    // NÃO é mais utilizado diretamente
    const CACHE_TTL = 60 * 60 * 24 * 30;

    /**
     * Calcula dinamicamente o TTL do cache
     * para expirar exatamente no final do ano atual
     */
    private function ttlAteFimDoAno(): int
    {
        return now()->diffInSeconds(
            now()->copy()->endOfYear()
        );
    }

    /**
     * Busca feriados nacionais + estaduais (Invertexto)
     */
    public function obterFeriados(string $estado, ?int $ano = null): array
    {
        $ano = $ano ?? date('Y');

        // Cache depende de ano + estado
        $cacheKey = "feriados_{$ano}_{$estado}";

        return Cache::remember(
            $cacheKey,
            $this->ttlAteFimDoAno(),
            function () use ($estado, $ano) {
                return $this->buscarNaApi($estado, $ano);
            }
        );
    }

    /**
     * Chamada real à API da Invertexto
     */
    private function buscarNaApi(string $estado, int $ano): array
    {
        $url = "https://api.invertexto.com/v1/holidays/{$ano}";

        try {
            Log::info("Consultando Invertexto Holidays: {$estado} ({$ano})");

            $response = Http::timeout(5)
                ->retry(2, 100)
                ->get($url, [
                    'token' => config('services.invertexto.key'),
                    'state' => $estado,
                ]);

            if ($response->successful()) {
                return $this->normalizarDados($response->json());
            }

            Log::warning('Invertexto retornou erro', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return [];

        } catch (\Exception $e) {
            Log::error('Erro ao consultar Invertexto', [
                'message' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Normaliza o JSON da Invertexto para o padrão do sistema
     */
    private function normalizarDados(array $dadosApi): array
    {
        $feriados = [];

        // Invertexto retorna diretamente um array de feriados
        if (!is_array($dadosApi)) {
            return [];
        }

        foreach ($dadosApi as $feriado) {
            $feriados[] = [
                'nome'      => $feriado['name'] ?? 'Feriado',
                'data'      => $feriado['date'] ?? null,
                // Normaliza o tipo aqui para evitar inconsistência depois
                'tipo'      => $this->normalizarTipo($feriado['level'] ?? null),
                'descricao' => $feriado['type'] ?? null, // feriado | facultativo
            ];
        }

        return $feriados;
    }

    /**
     * Normaliza tipo retornado pela Invertexto
     */
    private function normalizarTipo(?string $type): string
    {
        return match ($type) {
            'national' => 'nacional',
            'state'    => 'estadual',
            default    => 'outros',
        };
    }

    /**
     * Verifica se um dia é livre (manual ou feriado)
     */
    public function verificarDiaLivre(string $data): ?array
    {
        // 1. Prioridade: Verifica se o usuário marcou algo no banco
        $eventoManual = Evento::where('user_id', Auth::id())
            ->whereDate('data', $data)
            ->whereIn('tipo', ['feriado', 'sem_aula'])
            ->first();

        if ($eventoManual) {
            return [
                'titulo' => $eventoManual->titulo,
                'tipo'   => 'manual',
                'cor'    => 'bg-purple-100 text-purple-700',
            ];
        }

        // 2. Verifica na API (usando método cacheado)
        $ano = Carbon::parse($data)->year;
        $estado = Auth::user()->estado ?? 'BR'; // fallback seguro

        // Busca a lista completa do ano/estado
        $feriados = $this->obterFeriados($estado, $ano);

        // Filtra para ver se a data bate com algum feriado
        foreach ($feriados as $feriado) {
            if ($feriado['data'] === $data) {
                return [
                    'titulo' => $feriado['nome'],
                    'tipo'   => $feriado['tipo'],
                    'cor'    => 'bg-red-100 text-red-700',
                ];
            }
        }

        return null;
    }
}
