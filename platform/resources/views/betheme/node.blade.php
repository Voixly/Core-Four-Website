@php
    use App\Support\Betheme;
    $classes = $node['classes'] ?? [];
    $style = Betheme::boxStyle($node['box'] ?? null);
    $isWrap = ($node['kind'] ?? 'column') === 'wrap';
    $valign = $node['valign'] ?? null;
    if ($isWrap && $valign && $valign !== 'normal') {
        $style .= ($style ? ';' : '').'align-content:'.($valign === 'center' ? 'center' : 'start');
    }
    $hasSurface = !empty($node['box']['bg']) || !empty($node['box']['bgImage']) || !empty($node['box']['radius']);
    $light = in_array('light-text', $classes, true) ? ' light-text' : '';
    $media = Betheme::mediaClass($node, $parent ?? null);
    $row = Betheme::rowColumns($node);
    $vars = Betheme::spanVars($node);
@endphp

<div class="{{ $isWrap ? 'bt-wrap' : 'bt-col' }}{{ $hasSurface ? ' bt-surface' : '' }}{{ $light }}{{ $media }}{{ $row ? ' bt-row' : '' }}"
     style="{{ $vars }}{{ $style ? ';'.$style : '' }}{{ $row ? ';grid-template-columns:'.$row : '' }}">
    @if($isWrap)
        @foreach($node['columns'] ?? [] as $child)
            @include('betheme.node', ['node' => $child, 'parent' => $node])
        @endforeach
        @foreach($node['wraps'] ?? [] as $child)
            @include('betheme.node', ['node' => $child, 'parent' => $node])
        @endforeach
    @elseif(!empty($node['item']))
        @include('betheme.item', ['item' => $node['item']])
    @endif
</div>
