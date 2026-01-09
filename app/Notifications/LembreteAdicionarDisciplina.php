<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class LembreteAdicionarDisciplina extends Notification
{
    use Queueable;

    public function via($notifiable)
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title('Comece seus estudos! 📚')
            ->body('Percebemos que você ainda não cadastrou nenhuma matéria. Vamos resolver isso?')
            ->data(['url' => '/disciplinas/criar']); // Redireciona direto para o form
    }
}