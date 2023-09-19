<?php

namespace App\Notifications;

use App\Business;
use App\Http\Controllers\PayrollController;
use App\PayrollDetail;
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
    public $business_id;
    public $payrollDetail;
    public $employeeFirstName;
    public $employeeLastName;
    public $employeeUtil;
    public function __construct($payroll, $business_id, $payrollDetail, $employeeFirstName, $employeeLastName, $employeeUtil)
    {
        $this->payroll = $payroll;
        $this->business_id = $business_id;
        $this->payrollDetail = $payrollDetail;
        $this->employeeFirstName = $employeeFirstName;
        $this->employeeLastName = $employeeLastName;
        $this->employeeUtil = $employeeUtil;
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

        $business = Business::find($this->business_id);
        $payrollDetail = PayrollDetail::where('id', $this->payrollDetail->id)->firstOrFail();
        $start_date = ($payrollDetail->payroll->start_date == null)? $this->employeeUtil->getDate($payrollDetail->employee->date_admission, true) : $this->employeeUtil->getDate($payrollDetail->payroll->start_date, true);
        $end_date = $this->employeeUtil->getDate($payrollDetail->payroll->end_date, true);

        if ($this->payrollDetail->payroll->payrollType->name == "Planilla de sueldos"){
            $type =  __('payroll.salary');
        }
        if ($this->payrollDetail->payroll->payrollType->name == "Planilla de honorarios"){
            $type = __('payroll.honorary');
        }
        if ($this->payrollDetail->payroll->payrollType->name == "Planilla de aguinaldos"){
            $type = __('payroll.bonus');
        }
        if ($this->payrollDetail->payroll->payrollType->name == "Planilla de vacaciones"){
            $type = __('payroll.vacation');
        }

        $pdf = \PDF::loadView('payroll.generate_pdf',compact('payrollDetail', 'business', 'start_date', 'end_date'));
        $pdf->setPaper(array(0, 0, 612, 396), 'portrait');

        if($this->payrollDetail->payroll->payrollType->name == "Planilla de aguinaldos"){
            return (new MailMessage)
            ->subject('Boleta de pago - '.$type)
            ->greeting('Hola, '.$this->employeeFirstName.' '.$this->employeeLastName.'')
            ->line('Le hacemos llegar su boleta de pago que corresponde al mes de '.$mes.' de '.$this->payroll->year.'.')
            ->attachData($pdf->output(), 'Boleta de pago.pdf', [
                'mime' => 'application/pdf',
            ]);
        }else{
            return (new MailMessage)
            ->subject('Boleta de pago - '.$type)
            ->greeting('Hola, '.$this->employeeFirstName.' '.$this->employeeLastName.'')
            ->line('Le hacemos llegar su boleta de pago que corresponde al mes de '.$mes.' de '.$this->payroll->year.' - '.$this->payroll->paymentPeriod->name.'.')
            ->attachData($pdf->output(), 'Boleta de pago.pdf', [
                'mime' => 'application/pdf',
            ]);
        }
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
