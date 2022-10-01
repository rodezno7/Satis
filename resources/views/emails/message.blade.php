@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.url')])
{{ config('app.name') }}
@endcomponent
@endslot

[![Logo DevTech](https://i.ibb.co/rfgzbfh/envex-erp-logo-mini.png)]({{ config('app.url') }})

{{-- Body --}}
{{ $slot }}

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
    &copy; {{ date('Y') }} {{ config('app.owner') }} @lang('mail.all_rights_reserved')
@endcomponent
@endslot

@endcomponent

