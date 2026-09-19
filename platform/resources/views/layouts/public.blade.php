<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Core Four Roofing')</title>
    <meta name="description" content="@yield('description', 'Protect your business. Secure your home. Premium commercial and residential roofing across Texas.')">
    <link rel="canonical" href="@yield('canonical', url()->current())">
    <link rel="icon" href="/images/favicon-live.svg" type="image/svg+xml">
    <meta property="og:image" content="{{ url('/images/social-share.webp') }}">
    <link rel="preconnect" href="https://use.typekit.net" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css?family=Reddit+Sans:500,500italic,600,600italic,700,700italic&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://use.typekit.net/wci4ksj.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="/css/site.css">
    <link rel="stylesheet" href="/css/blocks.css">
    <script src="https://elfsightcdn.com/platform.js" async></script>
    @hasSection('schema')
        @yield('schema')
    @else
        <script type="application/ld+json">
            {!! json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'RoofingContractor',
                'name' => 'Core Four Roofing',
                'telephone' => $officePhone,
                'address' => [
                    '@type' => 'PostalAddress',
                    'streetAddress' => '22955 State Highway 249 Suite 26',
                    'addressLocality' => 'Tomball',
                    'addressRegion' => 'TX',
                    'postalCode' => '77375',
                ],
                'areaServed' => ['Houston', 'Austin', 'Dallas'],
                'url' => url('/'),
            ], JSON_UNESCAPED_SLASHES) !!}
        </script>
    @endif
</head>
<body class="page-{{ trim(request()->path(), '/') === '' ? 'index' : str_replace('/', '--', trim(request()->path(), '/')) }}">
<header class="site-header">
    <div class="header-inner">
        <a class="logo" href="{{ url('/') }}">
            <img class="logo-light" src="/images/logo-live.svg" alt="Core Four Roofing">
            <img class="logo-dark" src="/images/logo-color.svg" alt="Core Four Roofing">
        </a>
        <button class="menu-toggle" type="button" onclick="document.querySelector('.nav').classList.toggle('open')" aria-label="Menu"><i class="fas fa-bars"></i></button>
        <nav class="nav">
            <div class="has-sub">
                <a href="{{ url('/commercial-roofing/') }}">Commercial</a>
                <div class="sub">
                    <a href="{{ url('/commercial-roofing/roof-replacement-installation/') }}">Roof Replacement &amp; Installation</a>
                    <a href="{{ url('/commercial-roofing/repair-preventative-maintenance/') }}">Repair &amp; Preventative Maintenance</a>
                    <a href="{{ url('/commercial-roofing/coatings-restoration/') }}">Coatings &amp; Restoration</a>
                    <a href="{{ url('/commercial-roofing/inspections-condition-reports/') }}">Inspections &amp; Condition Reports</a>
                </div>
            </div>
            <div class="has-sub">
                <a href="{{ url('/residential-roofing/') }}">Residential</a>
                <div class="sub">
                    <a href="{{ url('/residential-roofing/asphalt-shingles/') }}">Asphalt Shingles</a>
                    <a href="{{ url('/residential-roofing/metal-roofs/') }}">Metal Roofs</a>
                    <a href="{{ url('/residential-roofing/synthetic-roofs/') }}">Synthetic Roofs</a>
                    <a href="{{ url('/residential-roofing/stone-coated-steel/') }}">Stone-Coated Steel</a>
                </div>
            </div>
            <a href="{{ url('/storm-emergency/') }}">Storm &amp; Emergency</a>
            <a href="{{ url('/about-core-four-roofing/') }}">About</a>
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
                    <li><a href="{{ url('/service-areas/') }}">Services Areas</a></li>
                    <li><a href="{{ url('/contact-core-four-roofing/') }}">Contact Core Four Roofing</a></li>
                    <li><a href="{{ url('/about-core-four-roofing/') }}">About Core Four Roofing</a></li>
                    <li><a href="{{ url('/financing/') }}">Financing</a></li>
                    <li><a href="{{ url('/insurance-claims/') }}">Insurance</a></li>
                </ul>
                <div class="socials">
                    <a href="https://www.facebook.com/corefourroofing/" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com/corefourroofing/" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="https://www.linkedin.com/company/core-four-roofing/" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            <div class="footer-cta">
                <h4>Protect your property. Don't wait.</h4>
                <p>Get a free, no-obligation inspection from the #1 local roofing experts in Texas.</p>
                <a class="btn" href="tel:+1{{ $officePhoneTel }}">Call Today <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
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
<script src="/js/site.js"></script>
<script src="/js/chat.js"></script>
</body>
</html>
