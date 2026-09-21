@extends('emails.layout')

@php
    $rows = array_filter([
        'Name' => $lead->name,
        'Phone' => $lead->phone,
        'Email' => $lead->email,
        'City' => trim(($lead->city ?? '').' '.($lead->zip ?? '')),
        'Type' => $lead->type,
        'Need' => $lead->need,
        'Source' => $lead->source,
    ]);
@endphp

@section('content')
    <p style="margin:0 0 8px;font-size:13px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#269B48">New lead</p>
    <h1 style="margin:0 0 16px;font-size:28px;line-height:1.2;letter-spacing:0.4px;color:#0f2418;font-family:Arial,Helvetica,sans-serif">{{ $lead->name ?: 'Website inquiry' }}</h1>
    <p style="margin:0 0 22px">A {{ $lead->type ?: 'roofing' }} request just came in from {{ $lead->source ?: 'the website' }}. Call them while it’s still warm.</p>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;background:#f3f0e9;border-radius:16px">
        @foreach($rows as $label => $value)
            <tr>
                <td style="padding:10px 16px;width:120px;font-size:13px;font-weight:700;color:#3d6b4a;vertical-align:top">{{ $label }}</td>
                <td style="padding:10px 16px;font-size:15px;color:#0f2418">
                    @if($label === 'Phone' && $lead->phone)
                        <a href="tel:{{ $lead->phone }}" style="color:#0f2418;text-decoration:none;font-weight:700">{{ $value }}</a>
                    @elseif($label === 'Email' && $lead->email)
                        <a href="mailto:{{ $lead->email }}" style="color:#0f2418;text-decoration:none">{{ $value }}</a>
                    @else
                        {{ $value }}
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
    @if($lead->notes)
        <p style="margin:20px 0 0;padding:16px;background:#f3f0e9;border-radius:12px;font-size:15px;line-height:1.6">{{ $lead->notes }}</p>
    @endif
@endsection
