<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Mail\InvoiceMail;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class InvoiceNotification extends Notification implements ShouldQueue
{
    use Queueable;
    

    protected $invoice, $emails;
    // protected $generated;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($invoice, $emails)
    {
        $this->invoice = $invoice;
        $this->emails = $emails;
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
        return (new InvoiceMail($this->invoice))
            ->to($this->emails)
            ->attach($this->invoice['path'].'/'.$this->invoice['email_file_name'],
                [
                    'as' => $this->invoice['file_name'],
                    'mime' => 'application/pdf',
                ]
            );
    }

}
