<?php

namespace App\Mail;

use App\Models\SumbUsers;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ForgotPasswordMail extends Mailable
{
    use Queueable, SerializesModels;
    
    protected $user;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(SumbUsers $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this->from('noreply@set-up-my-business.com.au', 'SUMB Change password')
            ->view('emails.forgot_password')
            ->with([
                "user" => $this->user,
                "lnk" => "forgotpass?email={$this->user->email}&id=".encrypt($this->user->id)
            ])->subject('Change Password.');


    }
}
