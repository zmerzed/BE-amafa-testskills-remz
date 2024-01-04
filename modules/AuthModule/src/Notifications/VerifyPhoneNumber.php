<?php

namespace Boilerplate\Auth\Notifications;

use Boilerplate\Sms\Notifications\Channels\SmsChannel;
use Boilerplate\Sms\SmsMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class VerifyPhoneNumber extends Notification implements ShouldQueue
{
    use Queueable;

    public function via($notifiable): array
    {
        return [SmsChannel::class];
    }

    public function toSms($notifiable): SmsMessage
    {
        $content = Lang::get(
            'Your phone number verification code is :code',
            ['code' => $notifiable->phone_number_verification_code]
        );

        return new SmsMessage($content);
    }
}
