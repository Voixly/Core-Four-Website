@extends('emails.layout')

@section('content')
    <p style="margin:0 0 8px;font-size:13px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#269B48">Held — not sent to Google</p>
    <h1 style="margin:0 0 16px;font-size:28px;line-height:1.2;color:#0f2418;font-family:Arial,Helvetica,sans-serif">{{ $review->stars }}★ from {{ $review->name ?: 'a customer' }}</h1>
    <p style="margin:0 0 16px">Review Shield hid Google and Yelp. Call them today and fix the issue in-house.</p>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3f0e9;border-radius:16px">
        <tr><td style="padding:10px 16px;font-weight:700;color:#3d6b4a;width:120px">Stars</td><td style="padding:10px 16px">{{ $review->stars }} / 5</td></tr>
        <tr><td style="padding:10px 16px;font-weight:700;color:#3d6b4a">Phone</td><td style="padding:10px 16px">{{ $review->phone ?: '—' }}</td></tr>
        <tr><td style="padding:10px 16px;font-weight:700;color:#3d6b4a">Email</td><td style="padding:10px 16px">{{ $review->email ?: '—' }}</td></tr>
        <tr><td style="padding:10px 16px;font-weight:700;color:#3d6b4a">City</td><td style="padding:10px 16px">{{ $review->city ?: '—' }}</td></tr>
        <tr><td style="padding:10px 16px;font-weight:700;color:#3d6b4a">Job</td><td style="padding:10px 16px">{{ $review->job ?: '—' }}</td></tr>
    </table>
    @if($review->comment)
        <p style="margin:20px 0 0;padding:16px;background:#f3f0e9;border-radius:12px">{{ $review->comment }}</p>
    @endif
@endsection
