@component('mail::message')
# Initial set of Newsletter for new subsciber.

Here are some of our latest publication that are inline with your interests: <br>

@component('mail::panel')
@foreach($details as $key=>$value)
## {{$value}} 
@component('mail::button', ['url' => config('app.url').'/dashboard/manage/content/'.$key.'/view']) 
Check this article
@endcomponent

@endforeach
@endcomponent
Thanks,<br>
{{ config('app.name') }}
@endcomponent
