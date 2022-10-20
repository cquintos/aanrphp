<table width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation">
<thead>
<tr>
@isset($title)
<th style="width:20%"></th>
@endisset
<th style="width:5%"></th>
<th style="width:75%"></th>
</tr>
</thead>
<tr>
<td max-width="100%" align="center">
<a href="{{ $url }}" class="button button-{{ $color ?? 'primary' }}" target="_blank" rel="noopener">{{ $slot }}</a>
</td>
<td>

</td>
@isset($title)
<td>
<h3>
{{$title ?? ''}}
</h3>
</td>
@endisset
</tr>
</table>
