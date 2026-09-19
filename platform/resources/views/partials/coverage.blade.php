<section class="section section--flush map-section">
    <div class="map-embed">
        @include('partials.elfsight-map')
    </div>
    <div class="map-card">
        <h4>Protecting Texas,<br>One Roof at a Time</h4>
        <p>Core Four Roofing Service Coverage</p>
        <ul class="coverage-list">
            <li>
                <i class="fas fa-map-pin" aria-hidden="true"></i>
                <h6>Service Hubs</h6>
                <p>Austin, Dallas, Houston</p>
            </li>
            <li>
                <i class="fas fa-clock" aria-hidden="true"></i>
                <h6>Storm Response</h6>
                <p>24/7 Emergency Tarping &amp; Repair</p>
            </li>
            <li>
                <i class="fas fa-phone" aria-hidden="true"></i>
                <h6>Local Office</h6>
                <p>{{ $officePhone }}</p>
            </li>
        </ul>
    </div>
</section>
