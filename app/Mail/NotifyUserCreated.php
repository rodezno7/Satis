<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyUserCreated extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $user;
    public $password;
    public $type;
    public function __construct($user, $password, $type)
    {
        $this->user = $user;
        $this->password = $password;
        $this->type = $type; // new user, reset pass
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if ($this->type == 'new_user') {
            return $this->markdown('emails.credentials.password')->subject(__('mail.welcome') . config('app.name'));
        } else {
            return $this->markdown('emails.credentials.reset_password')->subject(__('mail.password_reset') . config('app.name'));
        }
    }
}
