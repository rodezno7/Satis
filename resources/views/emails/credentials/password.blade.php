@component('emails.message')

@lang('mail.hello') {{ $user->first_name . ' ' . $user->last_name }}, @lang('mail.welcome_to') **{{ config('app.name') }}**!

@lang('mail.these_are_your_credentials_to_access'):

@lang('mail.username'): **{{ $user->username }}**

@lang('mail.password'): **{{ $password }}**
@component('mail::button', [ 'url' => config('app.url') ])
    @lang('mail.click_to_login')
@endcomponent

@lang('mail.best_regards'),

{{ config('app.owner') }}

@endcomponent
