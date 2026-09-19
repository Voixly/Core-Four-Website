@extends('layouts.public')

@php
    use App\Support\PageLayout;

    $reviewsWidget = '267ccdad-1b32-4c62-9394-105914e96f0f';
    $mapWidget = '058d3df7-5422-475a-ab17-f5e14c220034';
    $instagramWidget = '71bb658f-56ca-4f80-8bfc-064bde22918c';

    /** Find the elfsight widget ids used anywhere inside a section. */
    $widgetsIn = function (array $node) use (&$widgetsIn) {
        $found = [];
        foreach ($node as $key => $value) {
            if ($key === 'widget' && is_string($value)) {
                $found[] = $value;
            } elseif (is_array($value)) {
                $found = array_merge($found, $widgetsIn($value));
            }
        }
        return $found;
    };
@endphp

@section('title', $page['title'])
@section('description', $page['description'])

@section('content')
    @foreach($page['sections'] as $index => $section)
        @php
            $widgets = $widgetsIn($section);
            $isHero = $index === 0 && ! empty($section['bgImage']);
            $style = '';
            if ($bg = PageLayout::color($section['bg'] ?? null)) {
                $style .= "background-color:$bg;";
            }
            if (! empty($section['bgImage'])) {
                $style .= "background-image:url('".e($section['bgImage'])."');background-size:".e($section['bgSize'] ?? 'cover').";background-position:".e($section['bgPos'] ?? 'center').";";
            }
            if (! empty($section['padding'])) {
                $style .= 'padding:'.implode('px ', $section['padding']).'px;';
            }
            $classes = ['blk-section'];
            if ($isHero) $classes[] = 'blk-hero';
            if (in_array('full-width', $section['classes'] ?? [], true) || in_array('full-width-ex-mobile', $section['classes'] ?? [], true)) $classes[] = 'blk-section--full';
            if (in_array('full-screen', $section['classes'] ?? [], true)) $classes[] = 'blk-section--screen';
            if (in_array('dark', $section['classes'] ?? [], true)) $classes[] = 'blk-section--dark';
            // A leading section with a background but no content exists only to sit
            // behind the fixed header, so it has to carry the header's height.
            if ($index === 0 && ! empty($section['bg']) && ! PageLayout::sectionHasContent($section)) {
                $classes[] = 'blk-section--spacer';
            }
            $overlay = $section['overlay']['gradient'] ?? null;
        @endphp

        @if(! $isHero && in_array($reviewsWidget, $widgets, true) && in_array('mfn-global-section', $section['classes'] ?? [], true))
            @include('partials.reviews')
        @elseif(in_array($mapWidget, $widgets, true) && str_contains(json_encode($section), 'Protecting Texas'))
            @include('partials.coverage')
        @elseif(in_array($instagramWidget, $widgets, true))
            @include('partials.instagram')
        @else
            <section class="{{ implode(' ', $classes) }}" style="{{ $style }}">
                @if($overlay)
                    <div class="blk-hero-scrim" style="background:{{ $overlay }};opacity:{{ $section['overlay']['opacity'] ?? '1' }}"></div>
                @elseif($isHero)
                    <div class="blk-hero-scrim"></div>
                @endif
                <div class="blk-wrapper">
                    @foreach($section['wraps'] as $wrap)
                        @include('blocks.node', ['node' => $wrap])
                    @endforeach
                </div>
            </section>
        @endif
    @endforeach
@endsection
