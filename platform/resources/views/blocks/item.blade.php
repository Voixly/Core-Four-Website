@php
    use App\Support\PageLayout;
    $type = $item['type'] ?? 'unknown';
@endphp

@switch($type)
    @case('heading')
        @php $level = min(6, max(1, $item['level'] ?? 2)); @endphp
        <h{{ $level }} class="blk-h blk-h{{ $level }}{{ !empty($item['light']) ? ' light-text' : '' }}" style="{{ PageLayout::typeStyle($item) }}">{!! $item['html'] !!}</h{{ $level }}>
        @break

    @case('text')
        @php $textHtml = PageLayout::restoreIcons($item['html'] ?? ''); @endphp
        @if(str_contains($textHtml, 'Roofing Partners') || str_contains($textHtml, 'Associate Memberships'))
            @include('partials.logo-tracks')
        @else
            <div class="blk-text{{ !empty($item['light']) ? ' light-text' : '' }}" style="{{ PageLayout::typeStyle($item) }}">{!! $textHtml !!}</div>
        @endif
        @break

    @case('image')
        @php
            $img = $item['img'] ?? [];
            $isLogo = PageLayout::isLogoImage($item);
            $imgW = $img['dw'] ?? $img['w'] ?? null;
            $imgH = $img['dh'] ?? $img['h'] ?? null;
        @endphp
        <img class="blk-img{{ $isLogo ? ' blk-img--logo' : '' }}" src="{{ $img['src'] ?? '' }}" alt="{{ \App\Support\SiteSeo::imageAlt($img['src'] ?? '', $img['alt'] ?? '') }}"
             @if($imgW) width="{{ $imgW }}" height="{{ $imgH }}" style="--logo-w:{{ (int) $imgW }}px" @endif
             loading="lazy" decoding="async">
        @break

    @case('button')
        @php $href = PageLayout::href($item['href'] ?? '#'); @endphp
        <a class="{{ PageLayout::buttonClass($item) }}" href="{{ $href }}">
            {{ $item['text'] }}
            @if(!empty($item['icon']))<i class="{{ PageLayout::iconClass($item['icon']) }}" aria-hidden="true"></i>@endif
        </a>
        @break

    @case('faq')
        <div class="faq blk-faq">
            @foreach((!empty($item['items']) ? $item['items'] : PageLayout::defaultFaq()) as $i => $q)
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
            $icon = PageLayout::iconForTitle($boxTitle);
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
            $rawHtml = \App\Support\SiteSeo::fillEmptyAlts(PageLayout::restoreIcons($item['html'] ?? ''));
            $isArchive = PageLayout::blogPosts($rawHtml) !== '';
            $archive = $isArchive
                ? collect(\App\Support\BlogPost::all())->sortByDesc('date')->values()
                : collect();
        @endphp
        @if($isArchive)
            <div class="blk-blog">
                @foreach($archive as $post)
                    <article class="blk-post">
                        <h3><a href="{{ url('/'.$post['slug'].'/') }}">{{ $post['title'] }}</a></h3>
                        @if(! empty($post['image']))
                            <a href="{{ url('/'.$post['slug'].'/') }}">
                                <img src="{{ $post['image'] }}" alt="{{ $post['title'] }}" loading="lazy">
                            </a>
                        @endif
                        <p>{{ $post['description'] }}</p>
                        <a href="{{ url('/'.$post['slug'].'/') }}">Read More</a>
                    </article>
                @endforeach
            </div>
        @else
            <div class="blk-raw">{!! $rawHtml !!}</div>
        @endif
        @break

    @case('before_after')
        @if(!empty($item['before']['src']) && !empty($item['after']['src']))
            @include('partials.before-after', [
                'before' => $item['before']['src'],
                'after' => $item['after']['src'],
                'beforeAlt' => \App\Support\SiteSeo::imageAlt($item['before']['src'], $item['before']['alt'] ?? '', 'Roof before Core Four Roofing work'),
                'afterAlt' => \App\Support\SiteSeo::imageAlt($item['after']['src'], $item['after']['alt'] ?? '', 'Roof after Core Four Roofing work'),
            ])
        @endif
        @break

    @case('gallery')
        <div class="blk-gallery" style="--cols:{{ PageLayout::galleryColumns($item) }}">
            @foreach($item['images'] as $img)
                <img src="{{ $img['src'] }}" alt="{{ \App\Support\SiteSeo::imageAlt($img['src'] ?? '', $img['alt'] ?? '') }}"
                     @if(!empty($img['w']) && !empty($img['h'])) style="aspect-ratio:{{ $img['w'] }}/{{ $img['h'] }}" @endif
                     loading="lazy" decoding="async">
            @endforeach
        </div>
        @break

    @case('elfsight')
        <div class="elfsight-app-{{ $item['widget'] }}" data-elfsight-app-lazy></div>
        @break

    @case('icon')
        @php
            $iconClass = PageLayout::iconClass($item['icon'] ?? '');
            $social = PageLayout::socialForIcon($iconClass ?: ($item['icon'] ?? ''));
        @endphp
        @if($social)
            <a class="blk-icon" href="{{ $social['href'] }}" target="_blank" rel="noopener">
                <i class="{{ $iconClass }}" aria-hidden="true"></i>
                <span>{{ $social['label'] }}</span>
            </a>
        @else
            <span class="blk-icon">
                @if(Str::startsWith($iconClass, '/'))
                    <img src="{{ $iconClass }}" alt="">
                @else
                    <i class="{{ $iconClass }}" aria-hidden="true"></i>
                @endif
            </span>
        @endif
        @break

    @case('list')
        <ul class="blk-list">
            @foreach($item['items'] as $li)
                <li>{!! $li !!}</li>
            @endforeach
        </ul>
        @break

    @case('divider')
        <hr class="blk-divider">
        @break

    @case('group')
        <div class="blk-group" style="{{ PageLayout::boxStyle($item['box'] ?? null) }}">
            @foreach($item['children'] ?? [] as $child)
                @include('blocks.node', ['node' => $child])
            @endforeach
        </div>
        @break

    @case('form')
        @include('partials.lead-form', [
            'source' => $slug ?? 'website',
            'type' => str_starts_with($slug ?? '', 'commercial') ? 'commercial' : 'residential',
        ])
        @break

    @default
        @if(!empty($item['html']))
            <div class="blk-raw">{!! $item['html'] !!}</div>
        @endif
@endswitch
