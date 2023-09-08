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
    public $payroll;
    public $idPayrollDetail;
    public $employeeFirstName;
    public $employeeLastName;
    public function __construct($payroll, $idPayrollDetail, $employeeFirstName, $employeeLastName)
    {
        $this->payroll = $payroll;
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
        $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
        $mes = $meses[$this->payroll->month - 1];
        return (new MailMessage)
            ->subject('Boleta de pago - Planilla')
            ->greeting('Hola, '.$this->employeeFirstName.' '.$this->employeeLastName.'')
            ->line('Por este medio le hacemos llegar su boleta de pago que corresponde al mes de '.$mes.' de '.$this->payroll->year.' - '.$this->payroll->paymentPeriod->name.'.')
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
