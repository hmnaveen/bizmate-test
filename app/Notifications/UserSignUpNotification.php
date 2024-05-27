<?php

namespace App\Notifications;

use App\Mail\SignupMail;
use Illuminate\Bus\Queueable;
use App\Mail\GeneratedPasswordMail;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;



class UserSignUpNotification extends Notification implements ShouldQueue
{
    use Queueable;
    
    protected $data;
    protected $generated;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($data,$generated)
    {
       $this->data = $data;
       $this->generated = $generated;
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
        
        if($this->generated)

            return (new GeneratedpasswordMail($this->generated))
                ->to($notifiable->email); 

        $preData = array_merge($this->data,
            [
                'URL' => "verification/".encrypt($notifiable->id),
                'created_at'=> $notifiable->created_at, 
                'updated_at'=>$notifiable->updated_at

            ] );

        return (new SignupMail($preData))
                ->to($notifiable->email); 
    }

}
