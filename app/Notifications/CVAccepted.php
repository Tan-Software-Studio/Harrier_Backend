<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CVAccepted extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
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
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
                    // ->attach(storage_path() . "/app/library/code-conduct-2014.pdf");

        
        // $file = attach_file_show($file);
        
        // Mail::send('emails.slip_csv_format', $data, function($message)use($data, $file) {
        //     $message->to($data["email"], $data["email"])
        //             ->subject($data["subject"]);
        //     $message->attach($file);
        // });
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => config('constants.is_cv.accepted.name'),
            'email' => $this->data->email,
            'message' => config('constants.notification_type.cv_acc.message'),
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
