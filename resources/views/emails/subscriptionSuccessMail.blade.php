@component('mail::message')
# Subscription successful!

@component('mail::panel')
You can now receive the latest updates about AANR-related S&T outputs from different Philipine research institutions, agencies, and state colleges within this knowledge management portal. By subscribing to this portal, you will be given quarterly digests on everything you need to know about agriculture, aquatic and natural resources sector straight from the source.
@endcomponent

@component('mail::button', ['url' => config('app.url').'/about'])
About Us
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
