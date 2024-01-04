<?php

return [
    'verification' => [
        'email_not_found' => 'You have no email in your account.',
        'phone_number_not_found' => 'You have no phone number in your account.',
        'email_token_not_match' => 'The provided token does not match the email verification code on your account.',
        'phone_number_token_not_match' => 'The provided token does not match the phone number verification code on your account.',
    ],
    'username_not_found' => 'The given username does not exist',
    'valid_phone_number' => 'The :attribute must be a valid phone number.',
    'valid_email_or_phone_number' => 'The :attribute must be a valid email address or a valid phone number.',
    'valid_user' => [
        'non_admin_editing' => "You cannot edit other accounts unless you're an administrator.",
    ],
];
