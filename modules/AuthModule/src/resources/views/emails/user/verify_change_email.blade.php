@component('mail::message')
# Hi {{ $user->first_name . ' ' . $user->last_name }}

{{ Lang::get('We need to verify your email before we can update it.') }}

{{ Lang::get('Please copy the following code to verify your new email:') }}

@component('mail::panel')
    # {{ $token }}
@endcomponent

The {{ config('app.name') }} Team. <br>
@endcomponent
