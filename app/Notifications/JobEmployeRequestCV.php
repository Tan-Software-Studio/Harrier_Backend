<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JobEmployeRequestCV extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($datas)
    {
       
        $this->data = $datas;
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
            ->subject('New job alert')
			->greeting(' ')
            ->line($this->data['name'] . ' has created new Job: ' . $this->data['job_title'])
			->line('Kind regards,')
			->salutation('Harrier Candidates');
    }

    public function toDatabase($notifiable)
    {
        return [
            'email' => env('CONTACT_MAIL'),
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
