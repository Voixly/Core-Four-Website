<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Choose a password · Core Four Roofing</title>
    @include('partials.favicons')
    <link rel="preconnect" href="https://use.typekit.net" crossorigin>
    <link rel="stylesheet" href="https://use.typekit.net/wci4ksj.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="@assetv('/css/admin.css')">
</head>
<body class="login-page">
    <div class="login-card">
        <img src="/images/logo-color.svg" alt="Core Four Roofing">
        <h1>Choose a password</h1>
        @if($errors->any())
            <div class="flash is-error">{{ $errors->first() }}</div>
        @endif
        <form method="post" action="{{ route('password.store') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <label>Email <input type="email" name="email" value="{{ old('email', $email) }}" required autocomplete="username"></label>
            <label>New password <input type="password" name="password" required minlength="8" autocomplete="new-password"></label>
            <label>Confirm password <input type="password" name="password_confirmation" required minlength="8" autocomplete="new-password"></label>
            <button class="btn" type="submit">Save password</button>
        </form>
    </div>
</body>
</html>
