<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Reset password · Core Four Roofing</title>
    @include('partials.favicons')
    <link rel="preconnect" href="https://use.typekit.net" crossorigin>
    <link rel="stylesheet" href="https://use.typekit.net/wci4ksj.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="@assetv('/css/admin.css')">
</head>
<body class="login-page">
    <div class="login-card">
        <img src="/images/logo-color.svg" alt="Core Four Roofing">
        <h1>Reset password</h1>
        <p class="lede">Enter the email on your staff login. We’ll send a link.</p>
        @if(session('status'))
            <div class="flash">{{ session('status') }}</div>
        @endif
        @if($errors->any())
            <div class="flash is-error">{{ $errors->first() }}</div>
        @endif
        <form method="post" action="{{ route('password.email') }}">
            @csrf
            <label>Email <input type="email" name="email" value="{{ old('email') }}" required autocomplete="username"></label>
            <button class="btn" type="submit">Email me a link</button>
        </form>
        <a class="alt-link" href="{{ route('login') }}">Back to login</a>
    </div>
</body>
</html>
