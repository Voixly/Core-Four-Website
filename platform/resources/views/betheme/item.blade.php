@php
    use App\Support\Betheme;
    $type = $item['type'] ?? 'unknown';
@endphp

@switch($type)
    @case('heading')
        @php $level = min(6, max(1, $item['level'] ?? 2)); @endphp
        <h{{ $level }} class="bt-h bt-h{{ $level }}{{ !empty($item['light']) ? ' light-text' : '' }}" style="{{ Betheme::typeStyle($item) }}">{!! $item['html'] !!}</h{{ $level }}>
        @break

    @case('text')
        @php $textHtml = Betheme::restoreIcons($item['html'] ?? ''); @endphp
        @if(str_contains($textHtml, 'Roofing Partners') || str_contains($textHtml, 'Associate Memberships'))
            @include('partials.logo-tracks')
        @else
            <div class="bt-text{{ !empty($item['light']) ? ' light-text' : '' }}" style="{{ Betheme::typeStyle($item) }}">{!! $textHtml !!}</div>
        @endif
        @break

    @case('image')
        <img class="bt-img" src="{{ $item['img']['src'] }}" alt="{{ $item['img']['alt'] }}"
             @if(!empty($item['img']['w'])) width="{{ $item['img']['w'] }}" height="{{ $item['img']['h'] }}" @endif
             loading="lazy" decoding="async">
        @break

    @case('button')
        @php $href = Betheme::href($item['href'] ?? '#'); @endphp
        <a class="{{ Betheme::buttonClass($item) }}" href="{{ $href }}">
            {{ $item['text'] }}
            @if(!empty($item['icon']))<i class="{{ $item['icon'] }}" aria-hidden="true"></i>@endif
        </a>
        @break

    @case('faq')
        <div class="faq bt-faq">
            @foreach((!empty($item['items']) ? $item['items'] : Betheme::defaultFaq()) as $i => $q)
                <details @if($i === 0) open @endif>
                    <summary><span class="step-num">{{ $i + 1 }}</span> {{ $q['q'] }}</summary>
                    <div class="faq-body">{!! $q['a'] !!}</div>
                </details>
            @endforeach
        </div>
        @break

    @case('icon_box_2')
        @php
            preg_match('/<h3[^>]*>(.*?)<\/h3>/si', $item['html'] ?? '', $titleMatch);
            $boxTitle = trim(html_entity_decode(strip_tags($titleMatch[1] ?? '')));
            $icon = Betheme::iconForTitle($boxTitle);
        @endphp
        <div class="icon-box">
            <div class="icon-wrapper"><i class="{{ $icon }}" aria-hidden="true"></i></div>
            {!! preg_replace('/<i(?:\s+class="")?><\/i>/', '', $item['html'] ?? '') !!}
        </div>
        @break

    @case('counter')
        <div class="counter-card">{!! $item['html'] ?? '' !!}</div>
        @break

    @case('html')
        @php
            $rawHtml = Betheme::restoreIcons($item['html'] ?? '');
            $posts = Betheme::blogPosts($rawHtml);
        @endphp
        @if($posts)
            <div class="bt-blog">{!! $posts !!}</div>
        @else
            <div class="bt-raw">{!! $rawHtml !!}</div>
        @endif
        @break

    @case('before_after')
        @if(!empty($item['before']['src']) && !empty($item['after']['src']))
            @include('partials.before-after', [
                'before' => $item['before']['src'],
                'after' => $item['after']['src'],
                'beforeAlt' => $item['before']['alt'] ?: 'Before',
                'afterAlt' => $item['after']['alt'] ?: 'After',
            ])
        @endif
        @break

    @case('gallery')
        <div class="bt-gallery" style="--cols:{{ Betheme::galleryColumns($item) }}">
            @foreach($item['images'] as $img)
                <img src="{{ $img['src'] }}" alt="{{ $img['alt'] }}"
                     @if(!empty($img['w']) && !empty($img['h'])) style="aspect-ratio:{{ $img['w'] }}/{{ $img['h'] }}" @endif
                     loading="lazy" decoding="async">
            @endforeach
        </div>
        @break

    @case('elfsight')
        <div class="elfsight-app-{{ $item['widget'] }}" data-elfsight-app-lazy></div>
        @break

    @case('icon')
        <span class="bt-icon">
            @if(Str::startsWith($item['icon'] ?? '', '/'))
                <img src="{{ $item['icon'] }}" alt="">
            @else
                <i class="{{ $item['icon'] }}" aria-hidden="true"></i>
            @endif
        </span>
        @break

    @case('list')
        <ul class="bt-list">
            @foreach($item['items'] as $li)
                <li>{!! $li !!}</li>
            @endforeach
        </ul>
        @break

    @case('divider')
        <hr class="bt-divider">
        @break

    @case('group')
        <div class="bt-group" style="{{ Betheme::boxStyle($item['box'] ?? null) }}">
            @foreach($item['children'] ?? [] as $child)
                @include('betheme.node', ['node' => $child])
            @endforeach
        </div>
        @break

    @case('form')
        @include('partials.lead-form', ['source' => $slug ?? 'website'])
        @break

    @default
        @if(!empty($item['html']))
            <div class="bt-raw">{!! $item['html'] !!}</div>
        @endif
@endswitch
