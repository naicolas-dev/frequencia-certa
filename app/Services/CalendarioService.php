<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CalendarioService
{
    // Cache de 30 dias
    const CACHE_TTL = 60 * 60 * 24 * 30;

    /**
     * Busca feriados nacionais + estaduais (Invertexto)
     */
    public function obterFeriados(string $estado, ?int $ano = null): array
    {
        $ano = $ano ?? date('Y');

        // Cache agora depende só de ano + estado
        $cacheKey = "feriados_{$ano}_{$estado}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($estado, $ano) {
            return $this->buscarNaApi($estado, $ano);
        });
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
                'tipo'      => $feriado['level'] ?? 'outros', // nacional | estadual
                'descricao' => $feriado['type'] ?? null,      // feriado | facultativo
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
}
