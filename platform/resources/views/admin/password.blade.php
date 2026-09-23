@extends('layouts.admin')
@section('title', 'Change password')
@section('content')
<div class="panel" style="max-width:480px">
    <h3>Your password</h3>
    <form method="post" action="{{ route('admin.password.update') }}">
        @csrf
        @method('put')
        <label>Current password <input type="password" name="current_password" required autocomplete="current-password"></label>
        <label>New password <input type="password" name="password" required minlength="8" autocomplete="new-password"></label>
        <label>Confirm password <input type="password" name="password_confirmation" required minlength="8" autocomplete="new-password"></label>
        <button class="btn" type="submit">Update password</button>
    </form>
</div>
@endsection
