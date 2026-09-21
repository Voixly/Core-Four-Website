{!! '<'.'?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach($pages as $path)
    <url>
        <loc>{{ \App\Support\SiteSeo::url($path) }}</loc>
        <changefreq>{{ $path === '/' ? 'weekly' : 'monthly' }}</changefreq>
        <priority>{{ $path === '/' ? '1.0' : '0.8' }}</priority>
    </url>
@endforeach
    @foreach($cities as $city)
    <url>
        <loc>{{ \App\Support\SiteSeo::url($city->path()) }}</loc>
        <changefreq>weekly</changefreq>
        <priority>{{ $city->metro === 'Houston' && $city->slug !== 'tx' ? '0.8' : '0.65' }}</priority>
    </url>
    @endforeach
    @foreach($guides as $guide)
    <url>
        <loc>{{ \App\Support\SiteSeo::url('/guides/'.$guide->slug.'/') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
    @endforeach
    @foreach($posts as $slug)
    <url>
        <loc>{{ \App\Support\SiteSeo::url('/'.$slug.'/') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.65</priority>
    </url>
    @endforeach
</urlset>
