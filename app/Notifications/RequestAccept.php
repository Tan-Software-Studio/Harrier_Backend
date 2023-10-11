<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequestAccept extends Notification
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
     
        // $this->afterCommit();
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
        $adminemail = env('CONTACT_MAIL');
        if($this->guest->role == 'emp')
        {
            $url = env_guest_url().'/login/emp';
        }else{
            $url = env_emp_url().'/login/guest';
        }
        return (new MailMessage)
        ->line('You have now been granted access to Harrier Candidates.')
        ->line('Password : '.$this->guest->password )
        ->line("Don't forgate to create your own password within the plateform. You will also want to update your Company profile with Office Locations and options for Working Arrangements (e.g Full time Office, Full time Remote, Part time Hybrid) so that these option can be chosen when creating New Job.")
        ->action('Login', $url)
        // ->line('Thank you for using our application!');
        ->line('If you have any questions, please contact ' . env('CV_REQUEST_RECIEVE_MAIL') );
    }

    public function toDatabase($notifiable)
    {
        return [
            'email' => $this->guest->email,
            'type' => config('constants.notification_type.log_accept.key'),
            'message' => config('constants.notification_type.log_accept.message')   
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
