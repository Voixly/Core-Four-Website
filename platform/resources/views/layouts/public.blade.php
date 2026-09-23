@php
    $seoDecode = static fn (string $value): string => html_entity_decode(trim($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $seoTitle = $seoDecode($__env->yieldContent('title')) ?: \App\Support\SiteSeo::DEFAULT_TITLE;
    $seoDescription = $seoDecode($__env->yieldContent('description')) ?: \App\Support\SiteSeo::DEFAULT_DESCRIPTION;
    $seoCanonical = $seoDecode($__env->yieldContent('canonical')) ?: \App\Support\SiteSeo::current();
    $seoOgTitle = $seoDecode($__env->yieldContent('og_title')) ?: $seoTitle;
    $seoImage = $seoDecode($__env->yieldContent('og_image')) ?: \App\Support\SiteSeo::shareImage();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $seoTitle }}</title>
    <meta name="description" content="{{ $seoDescription }}">
    <meta name="robots" content="@yield('robots', 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1')">
    <link rel="canonical" href="{{ $seoCanonical }}">
    <link rel="alternate" type="text/plain" href="{{ \App\Support\SiteSeo::url('/llms.txt') }}" title="LLM source">
    @include('partials.favicons')
    <meta property="og:site_name" content="Core Four Roofing">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="{{ $seoOgTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:url" content="{{ $seoCanonical }}">
    <meta property="og:image" content="{{ $seoImage }}">
    <meta property="og:image:secure_url" content="{{ $seoImage }}">
    @if($seoImage === \App\Support\SiteSeo::shareImage())
    <meta property="og:image:type" content="{{ \App\Support\SiteSeo::SHARE_TYPE }}">
    <meta property="og:image:width" content="{{ \App\Support\SiteSeo::SHARE_WIDTH }}">
    <meta property="og:image:height" content="{{ \App\Support\SiteSeo::SHARE_HEIGHT }}">
    <meta property="og:image:alt" content="{{ \App\Support\SiteSeo::SHARE_ALT }}">
    @endif
    <meta property="og:locale" content="en_US">
    <meta property="article:publisher" content="{{ \App\Support\SiteSeo::FACEBOOK }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoOgTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    <meta name="twitter:image" content="{{ $seoImage }}">
    <meta name="twitter:image:alt" content="{{ $seoImage === \App\Support\SiteSeo::shareImage() ? \App\Support\SiteSeo::SHARE_ALT : $seoTitle }}">
    <link rel="preconnect" href="https://use.typekit.net" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css?family=Reddit+Sans:500,500italic,600,600italic,700,700italic&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://use.typekit.net/wci4ksj.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="@assetv('/css/site.css')">
    <link rel="stylesheet" href="@assetv('/css/blocks.css')">
    <script src="@assetv('/js/map-pins.js')"></script>
    <script src="https://elfsightcdn.com/platform.js" async></script>
    @hasSection('schema')
        @yield('schema')
    @else
        <script type="application/ld+json">
            {!! json_encode(\App\Support\SiteSeo::pageGraph($seoTitle, $seoDescription), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
        </script>
    @endif
</head>
<body class="page-{{ trim(request()->path(), '/') === '' ? 'index' : str_replace('/', '--', trim(request()->path(), '/')) }}">
<header class="site-header">
    <div class="header-inner">
        <a class="logo" href="/">
            <img src="/images/logo-live.svg" alt="Core Four Roofing">
        </a>
        <button class="menu-toggle" type="button" aria-label="Open menu" aria-expanded="false" aria-controls="site-nav">
            <i class="fas fa-bars" aria-hidden="true"></i>
        </button>
        <div class="nav-scrim" data-nav-close></div>
        <nav class="nav" id="site-nav" aria-label="Main">
            <div class="nav-head">
                <span class="nav-head-title">Menu</span>
                <button class="nav-close" type="button" aria-label="Close menu" data-nav-close>
                    <i class="fas fa-xmark" aria-hidden="true"></i>
                </button>
            </div>
            <div class="has-sub">
                <a href="{{ url('/commercial-roofing/') }}">Commercial</a>
                <button class="sub-toggle" type="button" aria-expanded="false" aria-label="Show Commercial pages">
                    <i class="fas fa-chevron-down" aria-hidden="true"></i>
                </button>
                <div class="sub"><div class="sub-inner">
                    <a href="{{ url('/commercial-roofing/roof-replacement-installation/') }}">Roof Replacement &amp; Installation</a>
                    <a href="{{ url('/commercial-roofing/repair-preventative-maintenance/') }}">Repair &amp; Preventative Maintenance</a>
                    <a href="{{ url('/commercial-roofing/coatings-restoration/') }}">Coatings &amp; Restoration</a>
                    <a href="{{ url('/commercial-roofing/inspections-condition-reports/') }}">Inspections &amp; Condition Reports</a>
                    <a href="/guides/commercial-roof-condition-scorecard/">Free roof scorecard</a>
                    <a href="{{ url('/commercial-roofing-in-tx/') }}">Cities we serve</a>
                </div></div>
            </div>
            <div class="has-sub">
                <a href="{{ url('/residential-roofing/') }}">Residential</a>
                <button class="sub-toggle" type="button" aria-expanded="false" aria-label="Show Residential pages">
                    <i class="fas fa-chevron-down" aria-hidden="true"></i>
                </button>
                <div class="sub"><div class="sub-inner">
                    <a href="{{ url('/residential-roofing/asphalt-shingles/') }}">Asphalt Shingles</a>
                    <a href="{{ url('/residential-roofing/metal-roofs/') }}">Metal Roofs</a>
                    <a href="{{ url('/residential-roofing/synthetic-roofs/') }}">Synthetic Roofs</a>
                    <a href="{{ url('/residential-roofing/stone-coated-steel/') }}">Stone-Coated Steel</a>
                    <a href="/guides/suburb-replacement-timeline/">Free replacement timeline</a>
                    <a href="{{ url('/residential-roofing-in-tx/') }}">Cities we serve</a>
                </div></div>
            </div>
            <a href="{{ url('/storm-emergency/') }}">Storm &amp; Emergency</a>
            <a href="{{ url('/about-core-four-roofing/') }}">About</a>
            <a href="{{ url('/careers/') }}">Careers</a>
            <a class="nav-cta" href="{{ url('/contact-core-four-roofing/') }}">Get a Free Inspection</a>
        </nav>
    </div>
</header>

<main>@yield('content')</main>

<footer class="site-footer">
    <div class="wrap">
        <div class="footer-grid">
            <div class="footer-col">
                <h6>Residential Services</h6>
                <ul class="footer-links">
                    <li><a href="{{ url('/residential-roofing/stone-coated-steel/') }}">Stone-Coated Steel</a></li>
                    <li><a href="{{ url('/residential-roofing/synthetic-roofs/') }}">Synthetic Roofs</a></li>
                    <li><a href="{{ url('/residential-roofing/metal-roofs/') }}">Metal Roofs</a></li>
                    <li><a href="{{ url('/residential-roofing/asphalt-shingles/') }}">Asphalt Shingles</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h6>Commercial Services</h6>
                <ul class="footer-links">
                    <li><a href="{{ url('/commercial-roofing/inspections-condition-reports/') }}">Inspections &amp; Condition Reports</a></li>
                    <li><a href="{{ url('/commercial-roofing/coatings-restoration/') }}">Coatings &amp; Restoration</a></li>
                    <li><a href="{{ url('/commercial-roofing/repair-preventative-maintenance/') }}">Repair &amp; Preventative Maintenance</a></li>
                    <li><a href="{{ url('/commercial-roofing/roof-replacement-installation/') }}">Roof Replacement &amp; Installation</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h6>Company</h6>
                <ul class="footer-links">
                    <li><a href="{{ url('/blog/') }}">Blog</a></li>
                    <li><a href="{{ url('/service-areas/') }}">Service Areas</a></li>
                    <li><a href="{{ url('/contact-core-four-roofing/') }}">Contact Core Four Roofing</a></li>
                    <li><a href="{{ url('/about-core-four-roofing/') }}">About Core Four Roofing</a></li>
                    <li><a href="{{ url('/careers/') }}">Careers</a></li>
                    <li><a href="{{ url('/financing/') }}">Financing</a></li>
                    <li><a href="{{ url('/insurance-claims/') }}">Insurance</a></li>
                    <li><a href="/guides/">Free Guides</a></li>
                    <li><a href="/reviews/">Leave a Review</a></li>
                    <li><a href="{{ url('/residential-roofing-in-tx/') }}">Texas city pages</a></li>
                </ul>
                <div class="socials">
                    <a href="https://www.facebook.com/corefourroofing/" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com/corefourroofing/" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="https://www.linkedin.com/company/core-four-roofing/" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            <div class="footer-cta">
                <h4>Protect your property. Don't wait.</h4>
                <p>Get a free, no-obligation inspection from our Tomball crew.</p>
                <a class="btn" href="tel:+1{{ $officePhoneTel }}">Call Today <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
        @if(!empty($footerCities) && $footerCities->isNotEmpty())
            <nav class="footer-cities" aria-label="Cities we serve">
                <h6>Residential roofing near Houston</h6>
                <div class="city-chips city-chips--links">
                    @foreach($footerCities as $footerCity)
                        <a href="{{ url($footerCity->path()) }}">{{ $footerCity->name }}</a>
                    @endforeach
                    <a href="{{ url('/residential-roofing-in-tx/') }}">All Texas cities</a>
                </div>
            </nav>
        @endif
        <div class="footer-bottom">
            <div>© Copyright {{ date('Y') }} Core Four Roofing &amp; Construction | Website by <a href="https://voixly.com/" target="_blank" rel="noopener">Voixly</a></div>
            <div>22955 State Highway 249 Suite 26<br>Tomball, TX 77375</div>
        </div>
    </div>
</footer>

<div id="cfr-chat" data-audience="@yield('audience', 'residential')">
    <div class="chat-panel">
        <div class="chat-head">Core Four chat</div>
        <div class="chat-log"></div>
        <form class="chat-compose">
            <input name="body" maxlength="2000" placeholder="Ask about a leak or replacement…" autocomplete="off">
            <button class="btn" type="submit">Send</button>
        </form>
    </div>
    <button class="chat-launcher" type="button">Chat</button>
</div>
<script src="@assetv('/js/site.js')"></script>
<script src="@assetv('/js/chat.js')"></script>
</body>
</html>
