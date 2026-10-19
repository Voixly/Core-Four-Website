<nav class="ads-options" aria-label="Ads plans">
    <a href="{{ route('admin.ads') }}" @class(['is-on' => request()->routeIs('admin.ads')])>Specialty search</a>
    <a href="{{ route('admin.ads.chatgpt') }}" @class(['is-on' => request()->routeIs('admin.ads.chatgpt')])>ChatGPT · $1,000</a>
</nav>
