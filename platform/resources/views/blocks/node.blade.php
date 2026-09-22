@php
    use App\Support\PageLayout;
    $classes = $node['classes'] ?? [];
    $box = $node['box'] ?? [];
    if (!empty($heroPair) && !empty($box['bgImage']) && in_array($box['bgImage'], [$heroPair['before'] ?? '', $heroPair['after'] ?? ''], true)) {
        unset($box['bgImage']);
    }
    $style = PageLayout::boxStyle($box ?: null);
    $isWrap = ($node['kind'] ?? 'column') === 'wrap';
    $valign = $node['valign'] ?? null;
    if ($isWrap && $valign && $valign !== 'normal') {
        $style .= ($style ? ';' : '').'align-content:'.($valign === 'center' ? 'center' : 'start');
    }
    $hasSurface = !empty($box['bg']) || !empty($box['bgImage']) || !empty($box['radius']);
    $isPhoto = !empty($box['bgImage']) && !str_contains((string) $box['bgImage'], '.svg');
    $pad = $box['padding'] ?? [];
    $flush = $isWrap && $hasSurface && $pad && (int) ($pad[1] ?? 0) === 0 && (int) ($pad[3] ?? 0) === 0;
    $lightBg = PageLayout::needsLightText($box);
    $light = (in_array('light-text', $classes, true) || $isPhoto || $lightBg) ? ' light-text' : '';
    $media = PageLayout::mediaClass($node, $parent ?? null);
    $row = PageLayout::rowColumns($node);
    $logos = $isWrap && PageLayout::isLogoRow($node);
    $logoStrip = $logos && PageLayout::isLogoStrip($node);
    $pill = ! $isWrap && PageLayout::isPill($node);
    $end = ! $isWrap && ($node['item']['type'] ?? '') === 'button' && ($node['item']['align'] ?? '') === 'right';
    $system = false;
    $systemHead = false;
    if ($isWrap) {
        $labels = [];
        foreach ($node['columns'] ?? [] as $col) {
            $html = $col['item']['html'] ?? '';
            if (is_string($html) && str_contains($html, 'Roofing System:')) {
                $system = true;
            }
            $labels[] = trim(strip_tags((string) $html));
        }
        $systemHead = $labels === ['', 'Roofing System', 'Best Suited For', 'Key Benefits', 'Learn More'];
    }
    $vars = PageLayout::spanVars($node);
@endphp

<div class="{{ $isWrap ? 'blk-wrap' : 'blk-col' }}{{ $hasSurface ? ' blk-surface' : '' }}{{ $flush ? ' blk-surface--flush' : '' }}{{ $isPhoto ? ' blk-photo' : '' }}{{ $pill ? ' blk-pill' : '' }}{{ $logos ? ' blk-logos' : '' }}{{ $logoStrip ? ' blk-logos--strip' : '' }}{{ $end ? ' blk-col--end' : '' }}{{ $system ? ' blk-system' : '' }}{{ $systemHead ? ' blk-system-head' : '' }}{{ $light }}{{ $media }}{{ $row ? ' blk-row' : '' }}"
     style="{{ $vars }}{{ $style ? ';'.$style : '' }}{{ $row ? ';grid-template-columns:'.$row : '' }}">
    {{-- A node can carry children and an item at once, as the blog listing does. --}}
    @foreach($node['columns'] ?? [] as $child)
        @include('blocks.node', ['node' => $child, 'parent' => $node, 'heroPair' => $heroPair ?? null])
    @endforeach
    @foreach($node['wraps'] ?? [] as $child)
        @include('blocks.node', ['node' => $child, 'parent' => $node, 'heroPair' => $heroPair ?? null])
    @endforeach
    @if(!empty($node['item']))
        @include('blocks.item', ['item' => $node['item']])
    @endif
</div>
