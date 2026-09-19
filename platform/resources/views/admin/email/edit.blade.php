@extends('layouts.admin')
@section('title', 'Edit email step')
@section('content')
<div class="panel">
    <p>{{ $step->sequence->name }} · step {{ $step->position }}</p>
    <form method="post" action="{{ route('admin.email.update', $step) }}">
        @csrf @method('PATCH')
        <label>Delay (days) <input type="number" name="delay_days" value="{{ $step->delay_days }}" min="0"></label>
        <label>Subject <input name="subject" value="{{ $step->subject }}" required></label>
        <label>Body <textarea name="body" rows="12" required>{{ $step->body }}</textarea></label>
        <label><input type="checkbox" name="is_active" value="1" style="width:auto" @checked($step->is_active)> Active</label>
        <p>Tokens: <code>{{ '{{first_name}}' }}</code> <code>{{ '{{name}}' }}</code> <code>{{ '{{city}}' }}</code></p>
        <button class="btn" type="submit">Save</button>
        <a class="btn" href="{{ route('admin.email.preview', $step) }}" target="_blank">Preview</a>
    </form>
    <form method="post" action="{{ route('admin.email.test', $step) }}" style="margin-top:1rem">
        @csrf
        <label>Test send to <input type="email" name="email" required placeholder="you@agency.com"></label>
        <button class="btn" type="submit">Send test</button>
    </form>
</div>
@endsection
