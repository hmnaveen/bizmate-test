<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Mail\VerificationMail;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class VerificationNotification extends Notification implements ShouldQueue
{
    use Queueable;
    
    // protected $data;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    // public function __construct($data)
    // {
       // $this->data = $data;
    // }

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
        
        return (new VerificationMail($notifiable))
            ->to($notifiable->email); 

    }

}
