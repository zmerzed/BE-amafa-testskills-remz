<?php

namespace Boilerplate\Auth\Models\Traits;

use Boilerplate\Auth\Support\OneTimePassword\InteractsWithOneTimePassword;

trait ManagesOneTimePassword
{
    use InteractsWithOneTimePassword {
        sendOneTimePassword as sendOtp;
        hasValidOneTimePassword as hasValidOtp;
    }

    public function sendOneTimePassword(): void
    {
        $this->sendOtp($this->otpDestination(), $this->otpChannel());
    }

    public function hasValidOneTimePassword(string $otp): bool
    {
        return $this->hasValidOtp($this->otpDestination(), $otp);
    }

    public function invalidateOneTimePassword(): void
    {
        $this->invalidateOneTimePasswordFor($this->otpDestination());
    }

    abstract protected function otpChannel(): string;

    abstract protected function otpDestination(): string;
}
