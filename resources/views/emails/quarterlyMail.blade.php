@component('mail::message')

# Quarterly set of digest from {{now()->subMonths(1)->firstOfQuarter()->format('F')}} to {{now()->subMonths(1)->endOfQuarter()->format('F')}}.

Here is what we got for you: 

@foreach($details['months'] as $currMonth)
## {{$currMonth->month}}

@component('mail::panel')
@foreach($details['contents'] as $content)
@if($currMonth->month === $content->month)
## {{$content->title}} 

@component('mail::button', ['url' => config('app.url').'/dashboard/manage/content/'.$content->id.'/view']) 
Check this article
@endcomponent

@endif
@endforeach
@endcomponent

@endforeach

Thank you have fun reading!<br>
{{ config('app.name') }}
@endcomponent