<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class EmailSendNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->subject = $data['subject'] ?? null;
        $this->greeting = $data['greeting'] ?? null;
        $this->contentLine = $data['contentLine'] ?? null;
        $this->actionLabel = $data['actionLabel'] ?? null;
        $this->actionLink = $data['actionLink'] ?? null;
        $this->afterActionContentLine = $data['afterActionContentLine'] ?? null;
        $this->salutation = $data['salutation'] ?? 'Thanks,';
        $this->markdownTemplate = $data['markdownTemplate'] ?? 'mail.template.index';
        $this->markdownData = $data['markdownData'] ?? ['image_url' => url("/images/logo.jpg") ,
            'company_name'=> Lang::get("Team")];
        $this->email = $data['email'];
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
        return (new MailMessage)
                    ->subject($this->subject)
                    ->greeting(Lang::get($this->greeting))
                    ->line(Lang::get($this->contentLine))
                    // ->action(Lang::get($this->actionLabel), url($this->actionLink))
                    ->line(Lang::get($this->afterActionContentLine))
                    ->salutation(Lang::get($this->salutation))
                    ->markdown($this->markdownTemplate, $this->markdownData);
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
