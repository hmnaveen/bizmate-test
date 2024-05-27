<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RecallInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;
    
    protected $mydata, $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($recall_invoice)
    {
        $this->recall_invoice = $recall_invoice;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.recall_invoice')
            ->from('noreply@set-up-my-business.com.au', 'SUMB Invoice Recalled')
            ->with(['recall_invoice' => $this->recall_invoice])
            ->subject($this->recall_invoice['subject']);
    }
}
