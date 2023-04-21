<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Lang;
use Illuminate\Notifications\Messages\MailMessage;

use App\Events\SendingEmailEvent;
use App\Notifications\EmailSendNotification as EmailSendNotification;

use Notification;
use App\Models\User;


class StoreUserLoginOTP
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\SendingEmailEvent  $event
     * @return void
     */
    public function handle(SendingEmailEvent $event)
    {
        $userinfo = $event->data;
        $sixRandomDigit = $this->generateRand();

        User::where('email', $userinfo->email)->update(['otp'=> $sixRandomDigit, 'otp_generated_at' => now()]);

        $notifiData = [
            'subject' => 'Login Authentication for  Portal',
            'greeting' =>  'Hi ' . $userinfo->name .',',
            'contentLine' => 'Please use the verification code below to sign in to the Portal',
            'afterActionContentLine' =>  "<h2 style='text-align:center'> $sixRandomDigit </h2>",
            'email' => $userinfo->email
            ];

        Notification::send($userinfo, new EmailSendNotification($notifiData));

    }

    /** Generate Random 6 digit number */
    public function generateRand() {
       $sixRandomDigit = random_int(100000, 999999);
       if (strlen((string)$sixRandomDigit) != 6) {
            $this->generateRand();
       }

       return $sixRandomDigit;

    }
}
