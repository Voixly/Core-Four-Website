<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Set your password · Core Four Roofing</title>
    @include('partials.favicons')
    <link rel="preconnect" href="https://use.typekit.net" crossorigin>
    <link rel="stylesheet" href="https://use.typekit.net/wci4ksj.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="@assetv('/css/admin.css')">
</head>
<body class="login-page">
    <div class="login-card">
        <img src="/images/logo-color.svg" alt="Core Four Roofing">
        <h1>Join job {{ $invite->job->number }}</h1>
        <p class="lede">Hi {{ $invite->name ?: 'there' }}. Set a password to track this job and upload files the office asked for.</p>
        @if($errors->any())
            <div class="flash is-error">{{ $errors->first() }}</div>
        @endif
        <form method="post" action="{{ route('account.invite.accept', $invite->token) }}">
            @csrf
            <label>Password <input type="password" name="password" required minlength="8" autocomplete="new-password"></label>
            <label>Confirm password <input type="password" name="password_confirmation" required minlength="8" autocomplete="new-password"></label>
            <button class="btn" type="submit">Open my job</button>
        </form>
    </div>
</body>
</html>
