<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Auth;
use App\Gamification\BadgeEvaluator;
use App\Gamification\Rules\Fire1Rule;
use App\Gamification\Rules\Fire7Rule;
use App\Gamification\Rules\EarlyBirdRule;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // 1. Gamification (Mantém igual)
        $this->app->singleton(BadgeEvaluator::class, function ($app) {
            return new BadgeEvaluator([
                Fire1Rule::class,
                Fire7Rule::class,
                EarlyBirdRule::class,
            ]);
        });

        // 2. Firebase Auth (Agora com suporte a Base64)
        $this->app->singleton(Auth::class, function ($app) {
            $factory = (new Factory);
            $credentials = null;

            // Tenta pegar da Variável de Ambiente (Render)
            $envCreds = env('FIREBASE_CREDENTIALS');

            if (!empty($envCreds)) {
                // TENTATIVA A: Verificar se é uma string Base64
                // O parâmetro 'true' faz o decode falhar se tiver caracteres inválidos (como { ou })
                $decoded = base64_decode($envCreds, true);
                
                if ($decoded !== false) {
                    // Se o decode funcionou, tenta parsear o JSON resultante
                    $json = json_decode($decoded, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $credentials = $json;
                    }
                }

                // TENTATIVA B: Se não for Base64 (ou falhou), tenta ler como JSON puro (caso você esqueça e cole o JSON direto)
                if (!$credentials) {
                    $json = json_decode($envCreds, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $credentials = $json;
                    }
                }

                // Se achou credenciais válidas na ENV, usa elas
                if ($credentials) {
                    return $factory->withServiceAccount($credentials)->createAuth();
                }
            }

            // 3. Fallback: Se não tem ENV válida, tenta arquivo físico (Localhost)
            $credentialsPath = storage_path('app/firebase-credentials.json');
            if (file_exists($credentialsPath)) {
                return $factory->withServiceAccount($credentialsPath)->createAuth();
            }

            // Se chegou aqui, não tem nada configurado
            throw new \Exception('CRÍTICO: Credenciais Firebase não encontradas. Configure a ENV FIREBASE_CREDENTIALS (Base64 ou JSON) no Render.');
        });
    }

    public function boot(): void
    {
        // Força HTTPS em produção (Essencial para o Render)
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        VerifyEmail::toMailUsing(function ($notifiable, string $url) {
            // ... (seu código de email continua igual) ...
             $appName = config('app.name');
             $nome = $notifiable->name ?: 'Estudante';
             
             return (new MailMessage)
                ->subject("Confirme seu e-mail no {$appName}")
                ->greeting("Olá, {$nome}!")
                ->line("Clique abaixo para validar sua conta.")
                ->action('Confirmar E-mail', $url);
        });
    }
}