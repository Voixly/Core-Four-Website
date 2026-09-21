<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Staff login · Core Four Roofing</title>
    @include('partials.favicons')
    <link rel="preconnect" href="https://use.typekit.net" crossorigin>
    <link rel="stylesheet" href="https://use.typekit.net/wci4ksj.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/admin.css">
</head>
<body class="login-page">
    <div class="login-card">
        <img src="/images/logo-color.svg" alt="Core Four Roofing">
        <h1>Staff portal</h1>
        <p class="lede">Agency, owner, and office logins for Core Four Roofing.</p>
        @if($errors->any())
            <div class="flash is-error">{{ $errors->first() }}</div>
        @endif
        <form method="post" action="{{ route('login') }}">
            @csrf
            <label>Email <input type="email" name="email" value="{{ old('email') }}" required autocomplete="username"></label>
            <label>Password <input type="password" name="password" required autocomplete="current-password"></label>
            <label class="remember"><input type="checkbox" name="remember" value="1"> Remember me</label>
            <button class="btn" type="submit">Log in</button>
        </form>
    </div>
</body>
</html>
