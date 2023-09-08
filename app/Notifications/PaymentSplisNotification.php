<?php

namespace App\Notifications;

use App\Http\Controllers\PayrollController;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PaymentSplisNotification extends Notification
{
    use Queueable;
 
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
    public $idPayrollDetail;
    public $employeeFirstName;
    public $employeeLastName;
    public function __construct($idPayrollDetail, $employeeFirstName, $employeeLastName)
    {
        $this->idPayrollDetail = $idPayrollDetail;
        $this->employeeFirstName = $employeeFirstName;
        $this->employeeLastName = $employeeLastName;
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
            ->subject('Boleta de pago - Planilla')
            ->greeting('Hola '.$this->employeeFirstName.' '.$this->employeeLastName.'')
            ->line('Por este medio le hacemos llegar su boleta de pago del mes de Agosgo 2023'.$this->employeeFirstName.' '.$this->employeeLastName.'.')
            ->action('Ver boleta de pago', url(config('app.url').'payroll/'.$this->idPayrollDetail.'/generatePaymentSlips'));
            // ->attachData(config('app.url').'/payroll/'.$this->idPayrollDetail.'/generatePaymentSlips', 'name.pdf', [
            //     'mime' => 'application/pdf',
            // ]);
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
