@php
    $value = $value ?? 50;
    $hero = !empty($hero);
@endphp
<div class="ba{{ $hero ? ' ba--hero' : '' }}" data-ba>
    <img class="ba-after" src="{{ $after }}" alt="{{ $afterAlt ?? 'After' }}">
    <div class="ba-before-wrap">
        <img class="ba-before" src="{{ $before }}" alt="{{ $beforeAlt ?? 'Before' }}">
    </div>
    <span class="ba-tag before">Before</span>
    <span class="ba-tag after">After</span>
    <div class="ba-handle"></div>
    <input class="ba-range" type="range" min="8" max="92" value="{{ $value }}" aria-label="Compare before and after">
</div>
