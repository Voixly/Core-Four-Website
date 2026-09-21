@php
    $brandForest = '#0f2418';
    $brandLime = '#bfe866';
    $brandInk = '#45664f';
    $brandPaper = '#f3f0e9';
    $brandMuted = '#3d6b4a';
    $ctaUrl = $ctaUrl ?? url('/contact-core-four-roofing/');
    $ctaLabel = $ctaLabel ?? 'Get a Free Inspection';
    $preheader = $preheader ?? '';
    $logoUrl = url('/images/email/logo-white.png');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>{{ $title ?? 'Core Four Roofing' }}</title>
</head>
<body style="margin:0;padding:0;background:{{ $brandPaper }};font-family:Poppins,Arial,Helvetica,sans-serif;color:{{ $brandInk }}">
@if($preheader)
    <div style="display:none;max-height:0;overflow:hidden;opacity:0;color:transparent">{{ $preheader }}</div>
@endif
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:{{ $brandPaper }}">
    <tr>
        <td align="center" style="padding:28px 16px">
            <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="width:100%;max-width:600px;background:#ffffff;border-radius:24px;overflow:hidden">
                <tr>
                    <td align="center" style="background:{{ $brandForest }};padding:28px 24px 24px">
                        <a href="{{ url('/') }}" style="text-decoration:none">
                            <img src="{{ $logoUrl }}" width="220" alt="Core Four Roofing" style="display:block;width:220px;max-width:80%;height:auto;border:0">
                        </a>
                    </td>
                </tr>
                <tr>
                    <td style="height:6px;background:{{ $brandLime }};font-size:0;line-height:0">&nbsp;</td>
                </tr>
                <tr>
                    <td style="padding:36px 32px 16px;font-size:16px;line-height:1.65;color:{{ $brandInk }}">
                        @yield('content')
                    </td>
                </tr>
                @hasSection('cta')
                    <tr>
                        <td align="center" style="padding:8px 32px 36px">
                            @yield('cta')
                        </td>
                    </tr>
                @else
                    <tr>
                        <td align="center" style="padding:8px 32px 36px">
                            <a href="{{ $ctaUrl }}" style="display:inline-block;background:{{ $brandLime }};color:{{ $brandForest }};text-decoration:none;font-weight:700;font-size:16px;line-height:1;padding:16px 28px;border-radius:999px">{{ $ctaLabel }}</a>
                        </td>
                    </tr>
                @endif
                <tr>
                    <td style="background:{{ $brandForest }};padding:28px 32px;text-align:center;color:#d7e4d4">
                        <p style="margin:0 0 10px;font-family:Arial,Helvetica,sans-serif;font-size:18px;font-weight:700;letter-spacing:0.4px;color:#ffffff">Protect your property. Don’t wait.</p>
                        <p style="margin:0 0 16px;font-size:14px;line-height:1.5;color:#d7e4d4">
                            {{ $officeAddress ?? '22955 State Highway 249 Suite 26, Tomball, TX 77375' }}<br>
                            <a href="tel:+1{{ $officePhoneTel ?? '2815410027' }}" style="color:{{ $brandLime }};text-decoration:none;font-weight:700">{{ $officePhone ?? '(281) 541-0027' }}</a>
                            · 24/7 emergency
                        </p>
                        <p style="margin:0;font-size:12px;line-height:1.5;color:#9bb39a">
                            <a href="{{ url('/') }}" style="color:#9bb39a;text-decoration:underline">corefourroofing.com</a>
                            · <a href="{{ url('/privacy-policy/') }}" style="color:#9bb39a;text-decoration:underline">Privacy / unsubscribe</a>
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
