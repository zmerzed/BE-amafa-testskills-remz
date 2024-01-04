<?php

namespace Boilerplate\Auth\Actions;

use Boilerplate\Auth\Models\PasswordReset;
use Boilerplate\Auth\Models\User;
use Boilerplate\Auth\Notifications\VerifyPhoneNumber;
use Illuminate\Auth\Notifications\VerifyEmail;

class SendResetPasswordCode
{
    public User $user;

    public PasswordReset $passwordReset;

    public function execute(PasswordReset $passwordReset, string $via = null): void
    {
        $this->passwordReset = $passwordReset;
        $this->user = $passwordReset->user;

        if ($via == 'email') {
            $this->sendCodeViaEmail();
        } elseif ($via == 'phone_number') {
            $this->sendCodeViaPhoneNumber();
        } else {
            if (!empty($this->user->email)) {
                $this->sendCodeViaEmail();
            } elseif (!empty($this->user->phone_number)) {
                $this->sendCodeViaPhoneNumber();
            }
        }
    }

    private function sendCodeViaEmail(): void
    {
        if (!$this->user->isEmailVerified()) {
            $this->user->notify(new VerifyEmail());
        }
    }

    private function sendCodeViaPhoneNumber(): void
    {
        if (!$this->user->isPhoneNumberVerified()) {
            $this->user->notify(new VerifyPhoneNumber());
        }
    }
}
