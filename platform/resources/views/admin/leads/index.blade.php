@extends('layouts.admin')
@section('title', 'Leads')
@section('content')
<form class="filters" method="get">
    <input name="q" value="{{ request('q') }}" placeholder="Search name, phone, city">
    <select name="status">
        <option value="">All statuses</option>
        @foreach(\App\Models\Lead::STATUSES as $status)
            <option value="{{ $status }}" @selected(request('status')===$status)>{{ $status }}</option>
        @endforeach
    </select>
    <select name="type">
        <option value="">Res + comm</option>
        <option value="residential" @selected(request('type')==='residential')>Residential</option>
        <option value="commercial" @selected(request('type')==='commercial')>Commercial</option>
    </select>
    <select name="source">
        <option value="">All sources</option>
        <option value="hiring" @selected(request('source')==='hiring')>Hiring</option>
    </select>
    <button class="btn" type="submit">Filter</button>
</form>
@if($leads->isNotEmpty())
<form id="lead-bulk" method="post" action="{{ route('admin.leads.bulk') }}">
    @csrf
    <div class="bulk-bar">
        <label class="remember"><input class="lead-check-all" type="checkbox"> Select this page</label>
        <select name="action">
            <option value="">Do this with selected</option>
            <option value="status">Set status</option>
            <option value="assign">Assign owner</option>
            <option value="delete">Delete</option>
        </select>
        <select name="status" data-for="status" hidden>
            <option value="">Choose a status</option>
            @foreach(\App\Models\Lead::STATUSES as $status)
                <option value="{{ $status }}">{{ $status }}</option>
            @endforeach
        </select>
        <select name="assigned_to" data-for="assign" hidden>
            <option value="">Choose a person</option>
            <option value="none">Unassigned</option>
            @foreach($staff as $user)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
            @endforeach
        </select>
        <button class="btn" type="submit">Apply</button>
        <span class="bulk-count"></span>
    </div>
@endif
<table class="lead-list">
    <tr><th>Select</th><th>Name</th><th>Phone</th><th>City</th><th>Type</th><th>Source</th><th>Status</th><th>Owner</th></tr>
    @foreach($leads as $lead)
        <tr>
            <td class="pick"><input class="lead-pick" type="checkbox" name="ids[]" value="{{ $lead->id }}"></td>
            <td class="name"><a href="{{ route('admin.leads.show', $lead) }}">{{ $lead->name }}</a></td>
            <td class="phone">@if($lead->phone)<a href="tel:{{ $lead->phone }}">{{ $lead->phone }}</a>@endif</td>
            <td class="city">{{ $lead->city }}</td>
            <td class="type">{{ $lead->source === 'hiring' ? 'Hiring' : $lead->type }}</td>
            <td class="source">{{ $lead->source }}</td>
            <td class="status"><span class="tag tag-{{ $lead->status }}">{{ $lead->status }}</span></td>
            <td class="owner">{{ $lead->assignee?->name }}</td>
        </tr>
    @endforeach
</table>
@if($leads->isNotEmpty())
</form>
@endif
{{ $leads->links() }}
@endsection
@push('scripts')
<script>
(function () {
    var form = document.getElementById('lead-bulk');
    if (!form) return;
    var all = form.querySelector('.lead-check-all');
    var action = form.querySelector('[name=action]');
    var count = form.querySelector('.bulk-count');
    var boxes = function () { return Array.from(form.querySelectorAll('.lead-pick')); };
    var picked = function () { return boxes().filter(function (box) { return box.checked; }).length; };
    var sync = function () {
        var total = boxes().length;
        var chosen = picked();
        all.checked = total > 0 && chosen === total;
        all.indeterminate = chosen > 0 && chosen < total;
        if (!count.dataset.hold) count.textContent = chosen ? chosen + ' selected' : '';
    };
    var showFields = function () {
        form.querySelectorAll('[data-for]').forEach(function (field) {
            var on = field.getAttribute('data-for') === action.value;
            field.hidden = !on;
            field.disabled = !on;
        });
    };
    all.addEventListener('change', function () {
        boxes().forEach(function (box) { box.checked = all.checked; });
        delete count.dataset.hold;
        sync();
    });
    form.addEventListener('change', function (event) {
        if (event.target.classList.contains('lead-pick')) {
            delete count.dataset.hold;
            sync();
        }
        if (event.target === action) showFields();
    });
    form.addEventListener('submit', function (event) {
        var chosen = picked();
        if (!chosen) {
            event.preventDefault();
            count.dataset.hold = '1';
            count.textContent = 'Select at least one lead.';
            return;
        }
        if (!action.value) {
            event.preventDefault();
            count.dataset.hold = '1';
            count.textContent = 'Choose what to do.';
            return;
        }
        if (action.value === 'delete' && !window.confirm('Delete ' + chosen + (chosen === 1 ? ' lead?' : ' leads?') + ' This cannot be undone.')) {
            event.preventDefault();
        }
    });
    showFields();
})();
</script>
@endpush
