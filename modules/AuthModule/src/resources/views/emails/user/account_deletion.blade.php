<x-mail::message>
# Hi {{ $user->name }},

You've received this email because you requested to delete your account.
Please click the button below to confirm your request.

<x-mail::button :url="$deleteConfirmationlink">
Delete My Account
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
