<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InterviewRequested extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        // dd($data);
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
        return ['mail', 'database'];
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
                   
                    ->line('Hello!')
                     ->line('Interview requested candidate('.$this->data->cand_email.' Name :'.$this->data->cand_name.' Candidate No.:'.$this->data->cand_id.').')
                    ->line('The employer '.env('APP_NAME').' has made an Interview request for the role '.$this->data->job_name.'.')
                    // ->action('Notification Action', url('/'))
                    ->line('User requesting the interview : '.$this->data->user_name.'.')
                    ->line('Thank you for using our platform!');
    }
    
    public function toDatabase($notifiable)
    {
        return [
            'type' => $this->data->type,
            'email' => $this->data->email,
            'message' => $this->data->message,
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
