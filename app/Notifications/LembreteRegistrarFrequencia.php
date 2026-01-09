<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class LembreteRegistrarFrequencia extends Notification
{
    use Queueable;

    public function via($notifiable)
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title('Chamada! 📢')
            ->body('Você tem aulas hoje! Não se esqueça de registrar sua frequência para manter a média.')
            ->action('Registrar Agora', 'frequencia_hoje')
            ->data(['url' => '/dashboard']);
    }
}