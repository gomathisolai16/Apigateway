<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class AccountActivation extends Notification
{
    use Queueable;

    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * The callback that should be used to create the reset password URL.
     *
     * @var (\Closure(mixed, string): string)|null
     */
    public static $createUrlCallback;

    /**
     * Create a notification instance.
     *
     * @param  string  $token
     * @return void
     */
    public function __construct($data)
    {
        $this->token = $data['token'];
        $this->email = $data['email'];
        $this->username = $data['username'];
        $this->subject = $data['subject'];
        $this->description = $data['description'];
        $this->actionText = $data['actionText'];
        $this->actionType = $data['actionType'];
        $this->expiryText = $data['expiryText'];
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

        return $this->buildMailMessage($this->resetUrl($notifiable));
    }

    /**
     * Get the reset password notification mail message for the given URL.
     *
     * @param  string  $url
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    protected function buildMailMessage($url)
    {
        return (new MailMessage)
            ->subject(Lang::get($this->subject))
            ->greeting(Lang::get('Hi '. $this->username.','))
            ->line(Lang::get($this->description))
            ->action(Lang::get($this->actionText), $url)
            ->line(Lang::get($this->expiryText))
            ->salutation (Lang::get('Thanks,'))
            ->markdown('mail.template.index', ['image_url' =>url("/images/logo.jpg") ,'company_name'=>"Team"]);

    }

    /**
     * Get the reset URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function resetUrl($notifiable)
    {
        if (static::$createUrlCallback) {
            return call_user_func(static::$createUrlCallback, $notifiable, $this->token);
        }
        //Generate url from creds
        return $this->activationUrl($this->token, $this->email,$this->actionType);
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

    public function activationUrl($token, $email,$actionType)
    {
        
        return config('app.mailurl')."/account/activation/".$token."?email=".$email."&actionType=".$actionType;
   
    }
}
