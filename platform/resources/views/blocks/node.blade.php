@php
    use App\Support\PageLayout;
    $classes = $node['classes'] ?? [];
    $style = PageLayout::boxStyle($node['box'] ?? null);
    $isWrap = ($node['kind'] ?? 'column') === 'wrap';
    $valign = $node['valign'] ?? null;
    if ($isWrap && $valign && $valign !== 'normal') {
        $style .= ($style ? ';' : '').'align-content:'.($valign === 'center' ? 'center' : 'start');
    }
    $hasSurface = !empty($node['box']['bg']) || !empty($node['box']['bgImage']) || !empty($node['box']['radius']);
    $light = in_array('light-text', $classes, true) ? ' light-text' : '';
    $media = PageLayout::mediaClass($node, $parent ?? null);
    $row = PageLayout::rowColumns($node);
    $vars = PageLayout::spanVars($node);
@endphp

<div class="{{ $isWrap ? 'blk-wrap' : 'blk-col' }}{{ $hasSurface ? ' blk-surface' : '' }}{{ $light }}{{ $media }}{{ $row ? ' blk-row' : '' }}"
     style="{{ $vars }}{{ $style ? ';'.$style : '' }}{{ $row ? ';grid-template-columns:'.$row : '' }}">
    @if($isWrap)
        @foreach($node['columns'] ?? [] as $child)
            @include('blocks.node', ['node' => $child, 'parent' => $node])
        @endforeach
        @foreach($node['wraps'] ?? [] as $child)
            @include('blocks.node', ['node' => $child, 'parent' => $node])
        @endforeach
    @elseif(!empty($node['item']))
        @include('blocks.item', ['item' => $node['item']])
    @endif
</div>
