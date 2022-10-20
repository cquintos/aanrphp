@component('mail::message')

# Quarterly set of digest from {{now()->subMonths(1)->firstOfQuarter()->format('F')}} to {{now()->subMonths(1)->endOfQuarter()->format('F')}}.

<br>

@foreach($details['interests'] as $interest)
## {{$interest}}:

@component('mail::panel')
@foreach($details['contents'][$interest]->pluck('title', 'id') as $key => $value)

@component('mail::button', ['url' => config('app.url').'/dashboard/manage/content/'.$key.'/view', 'title' => $value]) 
view
@endcomponent

@endforeach
@endcomponent

@endforeach

## Thank you and have fun reading!<br>
## {{ config('app.name') }}
@endcomponent