<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class VerificationMail extends Mailable
{
    use Queueable, SerializesModels;
    
    protected $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
    */
    public function build()
    {
        return $this->view('emails.verification')
        ->from('noreply@set-up-my-business.com.au', 'SUMB verification link')
        ->with([

            'link' => "verification/{$this->data->encId}",
            
        ])
        ->subject('Email Verification');
    }
}