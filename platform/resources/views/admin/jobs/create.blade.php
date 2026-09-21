@extends('layouts.admin')
@section('title', 'New job')
@section('meta', 'Open a roofing job from the office. Website forms still stay as leads until you convert them.')
@section('content')
<div class="panel" style="max-width:720px">
    <form method="post" action="{{ route('admin.jobs.store') }}">
        @csrf
        <label>Pipeline
            <select name="pipeline_id" required>
                @foreach($pipelines as $pipeline)
                    <option value="{{ $pipeline->id }}">{{ $pipeline->name }}</option>
                @endforeach
            </select>
        </label>
        <label>Existing lead (optional)
            <select name="lead_id">
                <option value="">None — enter the customer below</option>
                @foreach($leads as $lead)
                    <option value="{{ $lead->id }}">{{ $lead->name }} · {{ $lead->city }} · {{ $lead->email }}</option>
                @endforeach
            </select>
        </label>
        <div class="job-inline">
            <label>Name <input name="name"></label>
            <label>Email <input type="email" name="email"></label>
            <label>Phone <input name="phone"></label>
        </div>
        <label>Type
            <select name="type">
                <option value="residential">Residential</option>
                <option value="commercial">Commercial</option>
            </select>
        </label>
        <label>Payment
            <select name="payment_path">
                @foreach(\App\Models\Job::PAYMENT_PATHS as $path)
                    <option value="{{ $path }}">{{ str_replace('_', ' ', $path) }}</option>
                @endforeach
            </select>
        </label>
        <label>Urgency
            <select name="urgency">
                <option value="standard">Standard</option>
                <option value="emergency">Emergency</option>
            </select>
        </label>
        <label>Roof
            <select name="roof_type">
                <option value="">Unknown</option>
                @foreach(\App\Models\Job::ROOF_TYPES as $type)
                    <option value="{{ $type }}">{{ str_replace('_', ' ', $type) }}</option>
                @endforeach
            </select>
        </label>
        <label>Squares <input name="squares" type="number" step="0.01"></label>
        <label>Address <input name="address"></label>
        <label>City <input name="city"></label>
        <label>ZIP <input name="zip"></label>
        <label>Assign
            <select name="assigned_to">
                <option value="">Unassigned</option>
                @foreach($staff as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </label>
        <label>Customer summary
            <textarea name="customer_summary" rows="3"></textarea>
        </label>
        <button class="btn" type="submit">Open job</button>
    </form>
</div>
@endsection
