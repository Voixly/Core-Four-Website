@if(!empty($reads))
<section class="section">
    <div class="wrap">
        <h2>{{ $heading ?? 'Related reading' }}</h2>
        <div class="city-services">
            @foreach($reads as $read)
                <a class="card" href="{{ url($read['href']) }}">
                    <h3>{{ $read['title'] }}</h3>
                    <p>{{ $read['body'] }}</p>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif
