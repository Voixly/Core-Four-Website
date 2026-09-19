<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url><loc>{{ url('/') }}</loc></url>
    <url><loc>{{ url('/residential-roofing/') }}</loc></url>
    <url><loc>{{ url('/commercial-roofing/') }}</loc></url>
    <url><loc>{{ url('/insurance-claims/') }}</loc></url>
    <url><loc>{{ url('/storm-emergency/') }}</loc></url>
    <url><loc>{{ url('/about-core-four-roofing/') }}</loc></url>
    <url><loc>{{ url('/service-areas/') }}</loc></url>
    <url><loc>{{ url('/contact-core-four-roofing/') }}</loc></url>
    <url><loc>{{ url('/guides/') }}</loc></url>
    @foreach(['asphalt-shingles','metal-roofing','stone-coated-steel','synthetic-roofing','tpo-roofing','epdm-roofing','modified-bitumen','built-up-roofing'] as $material)
        <url><loc>{{ url('/'.$material.'/') }}</loc></url>
    @endforeach
    @foreach($cities as $city)
        <url><loc>{{ url($city->path()) }}</loc></url>
    @endforeach
    @foreach($guides as $guide)
        <url><loc>{{ url('/guides/'.$guide->slug) }}</loc></url>
    @endforeach
</urlset>
