<?php

namespace Boilerplate\Auth\Mail;

use Boilerplate\Auth\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyChangeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public string $token)
    {
        //
    }

    public function build(): Mailable
    {
        return $this->markdown('auth::emails.user.verify_change_email');
    }
}
