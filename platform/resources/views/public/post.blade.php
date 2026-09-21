@php
    $postUrl = \App\Support\SiteSeo::url('/'.$post['slug'].'/');
    $postImage = ! empty($post['image']) ? \App\Support\SiteSeo::url($post['image']) : '';
@endphp
@extends('layouts.public')
@section('title', $post['title'].' | Core Four Roofing')
@section('description', $post['description'])
@section('canonical', $postUrl)
@section('og_type', 'article')
@section('og_image', $postImage)
@section('schema')
    <meta property="article:published_time" content="{{ $post['date'] }}">
    <script type="application/ld+json">
        {!! json_encode(\App\Support\SiteSeo::articleSchema($post), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endsection
@section('content')
<article class="blog-article">
    <header class="blog-hero">
        <div class="wrap blog-wrap">
            <p class="kicker"><a href="{{ url('/blog/') }}">Blog</a></p>
            <h1>{{ $post['title'] }}</h1>
            <p class="blog-date">{{ \Illuminate\Support\Carbon::parse($post['date'])->timezone(config('app.timezone'))->format('F j, Y') }}</p>
        </div>
    </header>
    @if(! empty($post['image']))
        <div class="wrap blog-wrap">
            <img class="blog-cover" src="{{ $post['image'] }}" alt="{{ $post['title'] }}">
        </div>
    @endif
    <div class="wrap blog-wrap blog-body">
        {!! $post['html'] !!}
        <p class="blog-back"><a href="{{ url('/blog/') }}">All posts</a> · <a href="{{ url('/contact-core-four-roofing/') }}">Get a free inspection</a></p>
    </div>
</article>
@endsection
