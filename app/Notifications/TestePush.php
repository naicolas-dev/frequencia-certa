<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class TestePush extends Notification
{
    public function via($notifiable)
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title('Olá!')
            ->body('Isto é um teste do Frequência Certa.')
            ->action('Ver Dashboard', 'ver_app')
            ->data(['url' => '/dashboard']); // O nosso sw.js usa isto para redirecionar
    }
}
