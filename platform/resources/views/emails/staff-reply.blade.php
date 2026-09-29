<!DOCTYPE html>
<html lang="en">
<body style="margin:0;padding:24px;font-family:Georgia,serif;color:#0f2418;font-size:16px;line-height:1.5">
    @foreach(preg_split("/\n\s*\n/", trim($text)) ?: [] as $block)
        <p style="margin:0 0 16px">{!! nl2br(e($block)) !!}</p>
    @endforeach
    <p style="margin:24px 0 0">— {{ $staffName }}<br>Core Four Roofing<br>{{ $phone }}</p>
</body>
</html>
