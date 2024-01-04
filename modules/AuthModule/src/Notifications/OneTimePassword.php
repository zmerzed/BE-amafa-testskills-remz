<?php

namespace Boilerplate\Auth\Notifications;

use Boilerplate\Sms\Notifications\Channels\SmsChannel;
use Boilerplate\Sms\SmsMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class OneTimePassword extends Notification implements ShouldQueue
{
    use Queueable;

    public bool $viaEmail;

    protected string $otp;

    public function __construct(string $otp, bool $viaEmail = false)
    {
        $this->otp = $otp;
        $this->viaEmail = $viaEmail;
    }

    public function via(mixed $notifiable): array
    {
        return $this->viaEmail ? ['mail'] : [SmsChannel::class];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line($this->getMessageContent($notifiable));
    }

    public function toSms($notifiable): SmsMessage
    {
        return new SmsMessage($this->getMessageContent($notifiable));
    }

    protected function getMessageContent(mixed $notifiable): string
    {
        return Lang::get(
            'Your one time password is :otp',
            ['otp' => $this->otp]
        );
    }
}
