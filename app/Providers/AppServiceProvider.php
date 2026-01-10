<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use App\Gamification\BadgeEvaluator;
use App\Gamification\Rules\Fire7Rule;
use App\Gamification\Rules\EarlyBirdRule;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(BadgeEvaluator::class, function ($app) {
            return new BadgeEvaluator([
                Fire7Rule::class,
                EarlyBirdRule::class,
            ]);
        });
    }

    public function boot(): void
    {
        if (
            $this->app->environment('production') ||
            (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
        ) {
            URL::forceScheme('https');
        }

        // Personaliza o e-mail de verificação
        VerifyEmail::toMailUsing(function ($notifiable, string $url) {
            $appName = config('app.name');
            $nome = $notifiable->name ?: 'tudo bem?';
            $expiraEm = config('auth.verification.expire', 60);

            return (new MailMessage)
                ->subject("Confirme seu e-mail no {$appName}")
                ->greeting("Olá, {$nome}!")
                ->line("Recebemos seu cadastro no {$appName}. Para ativar sua conta, confirme seu e-mail clicando no botão abaixo.")
                ->action('Confirmar e-mail', $url)
                ->line("Esse link expira em {$expiraEm} minutos.")
                ->line("Se você não criou uma conta, pode ignorar esta mensagem com segurança.");
        });
    }
}
