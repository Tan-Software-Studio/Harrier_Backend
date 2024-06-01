<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CandidateSignupEmail extends Notification
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
            ->subject('Sign Up complete')
            ->line(' Dear ' . $this->data['name'])
            ->line('Thank you for signing up to the Harrier Candidates.')
            ->line('Your new password is  ' . $this->data['password'] . '  and can be reset within the My Profile section after logging in at www.harriercandidates.com/login/emp. ')
            ->line('The more people who join the platform the more useful salary and skills data will be available to you. We would like to encourage you to take advantage of our referral scheme: each and every time someone who you refer lands a job via the platform, we will give you 1% of their new base salary as a thank you. This can quickly stack up if you bring multiple friends and current/former colleagues into Harrier Candidates. Anyone who works in the legal/legaltech industry in Western Europe and the Anglosphere is eligible to join.')
            
            ->line('Thank you again for joining us. Kind regards,')
            ->line('Henry Venmore-Rowland, CEO'); 

       
    }

    public function toDatabase($notifiable)
    {
        return [
            'email' => $this->data['email'],
            'type' => config('constants.notification_type.candidate_signup_log.key')
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
