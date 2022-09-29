@component('mail::message')
# Email confirmation successful!

@component('mail::panel')
You can now check the latest updates about AANR-related S&T outputs from different Philipine research institutions, agencies, and state colleges within this knowledge management portal. 
@endcomponent

@component('mail::button', ['url' => config('app.url').'/about'])
About Us
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
