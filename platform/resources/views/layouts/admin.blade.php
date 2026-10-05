<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') · Core Four Roofing</title>
    @include('partials.favicons')
    <link rel="preconnect" href="https://use.typekit.net" crossorigin>
    <link rel="stylesheet" href="https://use.typekit.net/wci4ksj.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="@assetv('/css/admin.css')">
</head>
<body>
@php
    $emailWaiting = \Illuminate\Support\Facades\Schema::hasColumn('conversations', 'awaiting_staff')
        ? \App\Models\Conversation::query()->where('channel', 'email')->where('awaiting_staff', true)->count()
        : 0;
@endphp
<div class="admin">
    <div class="nav-backdrop" hidden></div>
    <aside class="side" id="admin-menu">
        <button class="nav-close" type="button">Close</button>
        <a class="side-brand" href="{{ route('admin.dashboard') }}">
            <img src="/images/logo-live.svg" alt="Core Four Roofing">
        </a>
        <div class="side-label">Workspace</div>
        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><i class="fas fa-chart-line"></i> Dashboard</a>
        <a class="nav-link {{ request()->routeIs('admin.leads.*') ? 'active' : '' }}" href="{{ route('admin.leads.index') }}"><i class="fas fa-users"></i> Leads</a>
        <a class="nav-link {{ request()->routeIs('admin.prospects.*') ? 'active' : '' }}" href="{{ route('admin.prospects.index') }}"><i class="fas fa-user-plus"></i> Prospects</a>
        <a class="nav-link {{ request()->routeIs('admin.jobs.*') ? 'active' : '' }}" href="{{ route('admin.jobs.index') }}"><i class="fas fa-diagram-project"></i> Jobs</a>
        <a class="nav-link {{ request()->routeIs('admin.schedule') ? 'active' : '' }}" href="{{ route('admin.schedule') }}"><i class="fas fa-calendar-days"></i> Schedule</a>
        <a class="nav-link {{ request()->routeIs('admin.chat.*') ? 'active' : '' }}" href="{{ route('admin.chat.index') }}"><i class="fas fa-comments"></i> Chat
            @if($emailWaiting > 0)<span class="nav-count">{{ $emailWaiting }}</span>@endif
        </a>
        <a class="nav-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}" href="{{ route('admin.reviews.index') }}"><i class="fas fa-shield-heart"></i> Review Shield</a>
        @if(auth()->user()->canManageReports())
            <div class="side-label">Growth</div>
            <a class="nav-link {{ request()->routeIs('admin.ads') ? 'active' : '' }}" href="{{ route('admin.ads') }}"><i class="fas fa-bullhorn"></i> Ads</a>
            <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}"><i class="fas fa-file-lines"></i> Reports</a>
            <a class="nav-link {{ request()->routeIs('admin.email.*') ? 'active' : '' }}" href="{{ route('admin.email.index') }}"><i class="fas fa-envelope"></i> Email</a>
            <a class="nav-link {{ request()->routeIs('admin.guides.*') ? 'active' : '' }}" href="{{ route('admin.guides.index') }}"><i class="fas fa-book"></i> Guides</a>
        @endif
        @if(auth()->user()->canManageUsers() || auth()->user()->canManageSettings())
            <div class="side-label">Admin</div>
            @if(auth()->user()->canManageUsers())
                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}"><i class="fas fa-user-gear"></i> Users</a>
            @endif
            @if(auth()->user()->canManageSettings())
                <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.edit') }}"><i class="fas fa-sliders"></i> Settings</a>
            @endif
        @endif
        <div class="side-user">
            <strong>{{ auth()->user()->name }}</strong>
            <small>{{ auth()->user()->role }}</small>
            <a class="account-link" href="{{ route('admin.password.edit') }}">Change password</a>
            <form method="post" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-logout" type="submit">Log out</button>
            </form>
        </div>
    </aside>
    <div class="main">
        <button class="nav-toggle" type="button" aria-controls="admin-menu" aria-expanded="false">
            <i class="fas fa-bars" aria-hidden="true"></i>
            <span>Menu</span>
        </button>
        <div class="top">
            <div class="top-copy">
                <strong class="page-title">@yield('title', 'Dashboard')</strong>
                <div class="page-meta">@yield('meta', 'Core Four Roofing · Tomball HQ')</div>
            </div>
            <div class="top-actions">
                @yield('actions')
            </div>
        </div>
        @if(session('success'))
            <div class="flash">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="flash is-error">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="flash is-error">{{ $errors->first() }}</div>
        @endif
        @yield('content')
    </div>
</div>
<nav class="bottom-nav">
    <a class="{{ request()->routeIs('admin.dashboard') ? 'is-on' : '' }}" href="{{ route('admin.dashboard') }}"><i class="fas fa-house"></i><span>Home</span></a>
    <a class="{{ request()->routeIs('admin.jobs.*') ? 'is-on' : '' }}" href="{{ route('admin.jobs.index') }}"><i class="fas fa-diagram-project"></i><span>Jobs</span></a>
    <a class="{{ request()->routeIs('admin.schedule') ? 'is-on' : '' }}" href="{{ route('admin.schedule') }}"><i class="fas fa-calendar-days"></i><span>Days</span></a>
    <a class="{{ request()->routeIs('admin.chat.*') ? 'is-on' : '' }}" href="{{ route('admin.chat.index') }}"><i class="fas fa-comments"></i><span>Chat</span>
        @if($emailWaiting > 0)<em class="bottom-count">{{ $emailWaiting }}</em>@endif
    </a>
</nav>
@stack('scripts')
<script>
document.querySelectorAll('table').forEach(function (table) {
    var heads = Array.from(table.querySelectorAll('tr:first-child th')).map(function (cell) {
        return cell.textContent.trim();
    });
    table.classList.add('is-stacked');
    table.querySelectorAll('tr').forEach(function (row) {
        if (row.querySelector('th') && row === table.querySelector('tr')) return;
        Array.from(row.children).forEach(function (cell, index) {
            if (heads[index]) cell.setAttribute('data-label', heads[index]);
            var text = cell.textContent.replace(/\s+/g, '').length;
            if (!text && !cell.querySelector('a, button, input, select, img')) cell.classList.add('is-empty');
        });
    });
});
(function () {
    var menu = document.getElementById('admin-menu');
    var toggle = document.querySelector('.nav-toggle');
    var backdrop = document.querySelector('.nav-backdrop');
    var closer = document.querySelector('.nav-close');
    if (!menu || !toggle || !backdrop) return;
    var setOpen = function (open) {
        menu.classList.toggle('is-open', open);
        document.body.classList.toggle('nav-open', open);
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        backdrop.hidden = !open;
    };
    toggle.addEventListener('click', function () { setOpen(!menu.classList.contains('is-open')); });
    backdrop.addEventListener('click', function () { setOpen(false); });
    if (closer) closer.addEventListener('click', function () { setOpen(false); });
    menu.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', function () { setOpen(false); });
    });
})();
</script>
</body>
</html>
