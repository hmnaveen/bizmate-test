<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GeneratedPasswordMail extends Mailable
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
            ->subject('generated password.')
            ->view('emails.generated-pass')
            ->with([
                'generated_pass' => $this->mydata 
            ]);
    }
}
