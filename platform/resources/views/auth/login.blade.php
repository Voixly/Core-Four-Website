<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Staff login · Core Four</title>
    <link rel="stylesheet" href="/css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Reddit+Sans:wght@500;700&display=swap" rel="stylesheet">
</head>
<body style="display:grid;place-items:center;min-height:100vh">
    <div class="panel" style="width:min(420px,92vw)">
        <h1 style="color:#144b24">Core Four admin</h1>
        <p>Agency, owner, and office logins. No shared HTML password.</p>
        @if($errors->any())
            <div class="flash" style="background:#fae9e8">{{ $errors->first() }}</div>
        @endif
        <form method="post" action="{{ route('login') }}">
            @csrf
            <label>Email <input type="email" name="email" value="{{ old('email') }}" required></label>
            <label>Password <input type="password" name="password" required></label>
            <label><input type="checkbox" name="remember" value="1" style="width:auto"> Remember me</label>
            <p><button class="btn" type="submit">Log in</button></p>
        </form>
    </div>
</body>
</html>
