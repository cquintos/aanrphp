@component('mail::message')
# Email confirmation successful!

@component('mail::panel')
You can now check the latest updates about AANR-related S&T outputs from different Philippine research institutions, agencies, and state colleges within this knowledge management portal. 
@component('mail::button2', ['url' => config('app.url').'/about'])
About Us
@endcomponent
<br><br>
<p>You are now also registered in our community portal.</p>
<br>
@component('mail::button2', ['url' => 'https://community.pcaarrd.dost.gov.ph/moLogin']) 
Community
@endcomponent
@endcomponent
{{-- Thanks,<br>
{{ config('app.name') }} --}}
@endcomponent
