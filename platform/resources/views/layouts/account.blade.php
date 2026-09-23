<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Your job') · Core Four Roofing</title>
    @include('partials.favicons')
    <link rel="preconnect" href="https://use.typekit.net" crossorigin>
    <link rel="stylesheet" href="https://use.typekit.net/wci4ksj.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="@assetv('/css/site.css')">
    <link rel="stylesheet" href="@assetv('/css/account.css')">
</head>
<body class="account-body">
<header class="account-top">
    <a class="logo" href="{{ route('account.home') }}"><img src="/images/logo-live.svg" alt="Core Four Roofing"></a>
    <div class="account-top-meta">
        <span>{{ auth()->user()->name }}</span>
        <form method="post" action="{{ route('account.logout') }}">@csrf<button class="btn btn--ghost" type="submit">Log out</button></form>
    </div>
</header>
<main class="account-main">
    @if(session('success'))
        <div class="flash">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="flash is-error">{{ $errors->first() }}</div>
    @endif
    @yield('content')
</main>
</body>
</html>
