<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewNotification extends Notification
{
    use Queueable;
    protected $password;
 
    /**
     * Create a new notification instance.
     *
     * @return void
     */

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(string $password)
    {
        $this->password = $password;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
        ->subject('Credenciales de acceso a Envex ERP')
        ->greeting('Hola '.$notifiable->first_name.' '.$notifiable->last_name.'')
        ->line('Estas son tus credenciales de acceso al sistema!')
        ->line('Usuario: '. $notifiable->username)
        ->line('Contraseña: '. $this->password)
        ->line('Te invitamos a que ingreses al sistema')
        ->action('Iniciar sesión', url(config('app.url')))
        ->line('Gracias por usar nuestra aplicación!');
    }
    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
