<?php

namespace Boilerplate\Auth\Mail;

use Boilerplate\Auth\Models\PasswordReset as PasswordResetModel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordReset extends Mailable
{
    use Queueable, SerializesModels;

    public PasswordResetModel $passwordReset;

    public function __construct(PasswordResetModel $passwordReset)
    {
        $this->passwordReset = $passwordReset->load('user');
    }

    public function build(): Mailable
    {
        return $this->markdown('auth::emails.password.reset');
    }
}
