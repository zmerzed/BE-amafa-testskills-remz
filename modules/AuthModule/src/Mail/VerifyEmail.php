<?php

namespace Boilerplate\Auth\Mail;

use Boilerplate\Auth\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public User $user)
    {
        //
    }

    public function build(): Mailable
    {
        return $this->markdown('auth::emails.user.verify_email');
    }
}
