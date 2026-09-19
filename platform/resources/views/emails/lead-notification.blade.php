<p>New Core Four lead from {{ $lead->source }}.</p>
<p>
    <strong>{{ $lead->name }}</strong><br>
    {{ $lead->phone }}<br>
    {{ $lead->email }}<br>
    {{ $lead->city }} {{ $lead->zip }}<br>
    {{ $lead->type }} · {{ $lead->need }}
</p>
<p>{{ $lead->notes }}</p>
<p><a href="{{ url('/admin/leads/'.$lead->id) }}">Open in admin</a></p>
