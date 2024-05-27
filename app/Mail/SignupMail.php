<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SignupMail extends Mailable
{
    use Queueable, SerializesModels;
    
    protected $mydata;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mydata)
    {
        $this->mydata = $mydata;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->from('noreply@set-up-my-business.com.au', 'SUMB Signup Details')
            ->subject('You are signed in to SUMB, Welcome to SUMB.')
            ->view('emails.signup')
            ->with(['mydata' => $this->mydata]);
    }
}
