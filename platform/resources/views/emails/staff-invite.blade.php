@extends('emails.layout')

@section('content')
    <p style="margin:0 0 8px;font-size:13px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#269B48">Staff portal</p>
    <h1 style="margin:0 0 16px;font-size:28px;line-height:1.2;color:#0f2418;font-family:Arial,Helvetica,sans-serif">Hi {{ $first }}, set your password</h1>
    <p style="margin:0 0 16px">You have a Core Four Roofing login as <strong>{{ $role }}</strong>. Use the button to choose a password, then sign in at the staff portal.</p>
    <p style="margin:0">This link lasts 60 minutes. If it expires, ask the office to send it again.</p>
@endsection
