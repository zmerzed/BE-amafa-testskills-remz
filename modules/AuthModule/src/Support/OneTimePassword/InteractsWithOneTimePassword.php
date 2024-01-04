<?php

namespace Boilerplate\Auth\Support\OneTimePassword;

use Boilerplate\Auth\Notifications\OneTimePassword;
use Boilerplate\Sms\Notifications\Channels\SmsChannel;
use Illuminate\Notifications\AnonymousNotifiable;

trait InteractsWithOneTimePassword
{
    protected function sendOneTimePassword(string $destination, string $channel = SmsChannel::class): void
    {
        $anonymousNotifiable = new AnonymousNotifiable();
        $anonymousNotifiable->route($channel, $destination);

        $otp = OneTimePasswordManager::for($destination)
            ->generate();

        $anonymousNotifiable->notify(new OneTimePassword($otp['code']));
    }

    protected function hasValidOneTimePassword(string $destination, string $otp): bool
    {
        return OneTimePasswordManager::for($destination)
            ->hasValidOtp($otp);
    }

    protected function invalidateOneTimePasswordFor(string $destination): void
    {
        OneTimePasswordManager::for($destination)
            ->invalidate();
    }
}
