<?php

namespace Boilerplate\Auth\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Boilerplate\Auth\Models\User;
use Illuminate\Support\Facades\URL;
use Illuminate\Queue\SerializesModels;

class AccountDeletion extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public string $userEmail)
    {
        //
    }

    /**
     * Get the message content definition.
     */
    public function build(): Mailable
    {
        $user = User::where('email', $this->userEmail)->firstOrFail();
        $deleteConfirmationlink = URL::temporarySignedRoute(
            'auth.account-deletion',
            now()->addDay(),
            ['email' => $user->email]
        );

        return $this->markdown(
            'auth::emails.user.account_deletion',
            [
                'user' => $user,
                'deleteConfirmationlink' => $deleteConfirmationlink,
            ],

        );
    }
}
