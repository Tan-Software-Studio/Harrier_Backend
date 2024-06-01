<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmpolerWelcomeEmailNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($data)
    {
       
        $this->data = $data;
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
       
            return (new MailMessage)
			->greeting(' ')
            ->subject('Employer Sign Up')
            ->line('Thank you for signing up to Harrier Candidates. We will send Commercial Terms to your Invoice Contact and login details will be provided to you.')
			->line('Kind regards,')
			->salutation('Harrier Candidates');
            
         

       
    }

    public function toDatabase($notifiable)
    {
        return [
            'email' => $this->data,
            'type' => config('constants.notification_type.employsignup_welcome_log.key')
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
