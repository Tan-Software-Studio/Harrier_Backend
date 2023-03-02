<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoginRequest extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->guest = $data;
        $this->afterCommit();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if($this->guest->role_login == roleGuest())
        {
            return (new MailMessage)
            ->subject('Guest Access Request')
            ->line('The introduction to the notification.')
            ->line('Guest access request received.')
            ->line('Please active email address: '.$this->guest->new_email)
            // ->action('Notification Action', url('/'))
            ->line('Thanks!'); 

        }else{        
            return (new MailMessage)
                    ->subject('Guest Access Request')
                    ->line('The introduction to the notification.')
                    ->line('Guest access request received.')
                    ->line('Please active email address: '.$this->guest->new_email)
                    // ->action('Notification Action', url('/'))
                    ->line('Thanks!');
        }
    }

    public function toDatabase($notifiable)
    {
        return [
            'email' => $this->guest->email,
            'type' => config('constants.notification_type.req_log.key')
        ];
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
