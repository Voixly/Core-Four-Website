<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') · Core Four</title>
    <link rel="icon" href="/images/favicon.svg" type="image/svg+xml">
    <link href="https://fonts.googleapis.com/css2?family=Reddit+Sans:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/admin.css">
</head>
<body>
<div class="admin">
    <aside class="side">
        <a class="brand" href="{{ route('admin.dashboard') }}">Core Four Admin</a>
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
        <a href="{{ route('admin.leads.index') }}" class="{{ request()->routeIs('admin.leads.*') ? 'active' : '' }}">Leads</a>
        <a href="{{ route('admin.chat.index') }}" class="{{ request()->routeIs('admin.chat.*') ? 'active' : '' }}">Chat</a>
        @if(auth()->user()->canManageReports())
            <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">Reports</a>
            <a href="{{ route('admin.email.index') }}" class="{{ request()->routeIs('admin.email.*') ? 'active' : '' }}">Email</a>
            <a href="{{ route('admin.guides.index') }}" class="{{ request()->routeIs('admin.guides.*') ? 'active' : '' }}">Guides</a>
        @endif
        @if(auth()->user()->canManageUsers())
            <a href="{{ route('admin.users.index') }}">Users</a>
            <a href="{{ route('admin.settings.edit') }}">Settings</a>
        @endif
        <form method="post" action="{{ route('logout') }}" style="margin-top:1.5rem">
            @csrf
            <button class="btn" type="submit">Log out</button>
        </form>
    </aside>
    <div class="main">
        <div class="top">
            <div>
                <strong>@yield('title', 'Dashboard')</strong>
                <div style="color:#45664f;font-size:0.9rem">{{ auth()->user()->name }} · {{ auth()->user()->role }}</div>
            </div>
            <a class="btn" href="tel:+1{{ $officePhoneTel }}">{{ $officePhone }}</a>
        </div>
        @if(session('success'))
            <div class="flash">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="flash" style="background:#fae9e8">{{ $errors->first() }}</div>
        @endif
        @yield('content')
    </div>
</div>
<nav class="bottom-nav">
    <a href="{{ route('admin.dashboard') }}">Home</a>
    <a href="{{ route('admin.leads.index') }}">Leads</a>
    <a href="{{ route('admin.chat.index') }}">Chat</a>
    @if(auth()->user()->canManageReports())
        <a href="{{ route('admin.reports.index') }}">Reports</a>
        <a href="{{ route('admin.email.index') }}">Email</a>
    @else
        <a href="{{ route('admin.leads.index') }}">Call</a>
        <a href="{{ url('/') }}">Site</a>
    @endif
</nav>
</body>
</html>
