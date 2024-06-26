<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CVRequested extends Notification
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
            ->greeting(' ')
            ->subject('CV Request alert')
            ->line('Candidate CV Requested (' . $this->data->cand_email . ' Name :' . $this->data->cand_name . ' Candidate :' . $this->data->cand_id . ').')
            ->line('Employer ' . $this->data->emp_name . ' CV requested for this job ' . $this->data->job_name . '.')
            // ->action('Notification Action', url('/'))
            ->line('The user : ' . $this->data->user_name . '.')
            ->line('Kind regards,')
            ->salutation('Harrier Candidates');
            // ->line('Thank you for using our application!');
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => $this->data->type,
            'email' =>  env('CONTACT_MAIL'),
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
