<?php

namespace Boilerplate\Auth\Notifications;

use Boilerplate\Auth\Mail\PasswordReset as PasswordResetMail;
use Boilerplate\Sms\SmsMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class PasswordReset extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public array $via = ['mail'])
    {
        //
    }

    public function via(mixed $notifiable): array
    {
        return $this->via;
    }

    public function toMail(mixed $notifiable): PasswordResetMail
    {
        return (new PasswordResetMail($notifiable->passwordReset))
            ->to($notifiable->email);
    }

    public function toSms(mixed $notifiable): SmsMessage
    {
        $content = Lang::get(
            'Your password reset code is :code',
            ['code' => $notifiable->passwordReset->token]
        );

        return new SmsMessage($content);
    }
}
