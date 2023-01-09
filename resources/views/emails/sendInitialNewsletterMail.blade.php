@component('mail::message')
# EMAIL VERIFICATION SUCCESSFUL!

@component('mail::panel')
You can now check the latest updates about AANR-related S&T outputs from different Philippine research institutions, agencies, and state colleges within this knowledge management portal. 
@component('mail::button2', ['url' => config('app.url').'/about'])
About Us
@endcomponent
<br><br>
<p>You are now also registered in our community portal.</p>
<br>
@component('mail::button2', ['url' => 'http://community.pcaarrd.dost.gov.ph/moLogin']) 
Community
@endcomponent
@endcomponent

# ALSO, THANK YOU FOR SUBSCRIBING!

### Here are some of our latest publication that are inline with your interests: 

@component('mail::panel')
@foreach($details as $key=>$value)
@component('mail::button', ['url' => config('app.url').'/dashboard/admin/content/'.$key.'/view', 'title' => $value]) 
view
@endcomponent

@endforeach
@endcomponent
{{-- Thanks,<br>
{{ config('app.name') }} --}}
@endcomponent
