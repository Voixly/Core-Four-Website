@extends('layouts.admin')
@section('title', 'ChatGPT ads')
@section('meta', 'Residential · Houston suburbs · $1,000 a month')
@section('actions')
    <a class="btn" href="{{ route('admin.ads.chatgpt.pdf') }}"><i class="fas fa-file-arrow-down"></i> Download PDF</a>
@endsection

@section('content')
<div class="perf ads-plan">
    @include('admin.ads.partials.options')
    <div class="perf-hero">
        <p class="perf-kicker">Prepared {{ $ads['meta']['prepared'] }} · Residential only</p>
        <h2>$1,000 a month. One ChatGPT campaign. Houses we can drive to.</h2>
        <p>This sits next to the specialty search plan. It does not replace it. ChatGPT ads show under a reply when the conversation is about the roof. At this budget there is one campaign, two ad groups, and a daily budget of $33.</p>
    </div>

    <nav class="perf-nav" aria-label="ChatGPT ads sections">
        <a href="#budget">Budget</a>
        <a href="#setup">Setup</a>
        <a href="#ads">Ads</a>
        <a href="#places">Places</a>
        <a href="#launch">90 days</a>
    </nav>

    <section class="perf-section" id="budget">
        <p class="perf-kicker">The number</p>
        <h2>Just over the minimum, on purpose</h2>
        <p class="perf-lead">OpenAI’s minimum daily budget is $25. A $33 daily budget lands near $1,000 for the month. The platform can spend up to twice that on a single day, and it will not spend more than seven times the daily budget in a Sunday–Saturday week.</p>
        <div class="perf-gains">
            @foreach($ads['budget'] as $item)
                <article class="perf-gain">
                    <span>{{ $item['label'] }}</span>
                    <b>{{ $item['value'] }}</b>
                    <em>{{ $item['note'] }}</em>
                </article>
            @endforeach
        </div>
        <div class="perf-callout">
            <strong>What the money can buy:</strong>
            {{ $ads['clicks'] }} This plan does not forecast leads. ChatGPT has not published a roofing conversion rate, and Core Four has not run this campaign yet. Judge it on cost per Lead created, and on whether the person is a homeowner in the suburbs.
        </div>
    </section>

    <section class="perf-section" id="setup">
        <p class="perf-kicker">How it is built</p>
        <h2>Conversions, not views</h2>
        <p class="perf-lead">The pixel is already on the public site. Lead created fires when an inspection request or a guide signup is saved, and the server sends the same event. Point this campaign at that event. The ad landings are the repair and installation forms, so a conversion is someone asking for a look at a house.</p>
        <div class="perf-split">
            <div class="panel">
                <h3>Campaign</h3>
                <div class="perf-metrics">
                    <div><span>Name</span><strong>Residential — Houston suburbs</strong></div>
                    <div><span>Objective</span><strong>Conversions</strong></div>
                    <div><span>Billing</span><strong>Valid clicks · oCPC</strong></div>
                    <div><span>Conversion</span><strong>Lead created</strong></div>
                    <div><span>Max bid</span><strong>$4 · inside the $3–$5 start</strong></div>
                    <div><span>Platforms</span><strong>Desktop, iOS, and Android</strong></div>
                </div>
            </div>
            <div class="panel">
                <h3>Already on the website</h3>
                <div class="perf-metrics">
                    <div><span>Pixel</span><strong>Core Four Pixel</strong></div>
                    <div><span>Event</span><strong>lead_created</strong></div>
                    <div><span>Window</span><strong>30-day click · 1-day view</strong></div>
                    <div><span>Server</span><strong>Same event id as the pixel</strong></div>
                    <div><span>Phone on the ad</span><strong>(281) 541-0027</strong></div>
                </div>
                <p class="perf-sub" style="margin:0.9rem 0 0">If Ads Manager will not let you select Lead created yet, wait. Do not publish a Clicks campaign and plan to switch it later. OpenAI requires a new campaign to change the objective.</p>
            </div>
        </div>
    </section>

    <section class="perf-section" id="ads">
        <p class="perf-kicker">The two ad groups</p>
        <h2>A leak, or a roof that is finished</h2>
        <p class="perf-lead">Both groups share the $33. Hints describe the situation. They are not keywords, and they do not keep the ad inside a city. Each ad says something the other one does not. Use a real house photo that matches the sentence.</p>
        <div class="ads-campaigns">
            @foreach($ads['groups'] as $group)
                <article class="ads-campaign">
                    <span>{{ $group['name'] }}</span>
                    <h3>{{ $group['job'] }}</h3>
                    <p class="ads-landing-note">
                        Landing: <a href="{{ url($group['landing']) }}" target="_blank" rel="noopener noreferrer">{{ $group['landing'] }}</a><br>
                        {{ $group['utm'] }}
                    </p>
                    <div class="perf-metro-label">Context hints</div>
                    <ul class="ads-dont">
                        @foreach($group['hints'] as $hint)
                            <li>{{ $hint }}</li>
                        @endforeach
                    </ul>
                    @foreach($group['ads'] as $ad)
                        <div class="perf-callout" style="margin-top:0.75rem">
                            <strong>{{ $ad['title'] }}</strong><br>
                            {{ $ad['copy'] }}
                        </div>
                    @endforeach
                </article>
            @endforeach
        </div>
    </section>

    <section class="perf-section" id="places">
        <p class="perf-kicker">Where it can show</p>
        <h2>Houston suburbs. Not the country.</h2>
        <p class="perf-lead">Search the location field and select these cities. Include the United States only as the country they sit in. A campaign left on the United States with no city spends the $1,000 nationwide. If Houston and these cities are not in the location search, do not launch.</p>
        <div class="perf-chips" style="margin-bottom:1rem">
            @foreach($ads['locations'] as $city)
                <span>{{ $city }}</span>
            @endforeach
        </div>
        <div class="perf-split">
            <div class="panel">
                <h3>Negative phrases, if the account has them</h3>
                <p class="perf-sub">OpenAI offers this to qualifying advertisers, not to every account. Up to 100 phrases. They block the ad when the message contains the phrase.</p>
                <div class="perf-chips">
                    @foreach($ads['negatives'] as $phrase)
                        <span>− {{ $phrase }}</span>
                    @endforeach
                </div>
            </div>
            <div class="panel">
                <h3>What we will not do</h3>
                <ul class="ads-dont">
                    @foreach($ads['dont'] as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    <section class="perf-section" id="launch">
        <p class="perf-kicker">First 90 days</p>
        <h2>Spend it once. Then decide.</h2>
        <div class="ads-steps">
            @foreach($ads['steps'] as $index => $step)
                <article>
                    <div>{{ $index + 1 }}</div>
                    <div>
                        <h3>{{ $step['title'] }}</h3>
                        <p>{{ $step['body'] }}</p>
                    </div>
                </article>
            @endforeach
        </div>
        <div class="perf-callout">
            <strong>The test, in one line:</strong>
            $33 a day, Conversions on Lead created, max bid $4, two residential ad groups, Houston-suburb cities only. One booked house pays for many months of this test. No in-area lead after $1,000 means stop.
        </div>
    </section>

    <p class="perf-foot">Core Four Roofing · Confidential ChatGPT ads plan · Prepared {{ $ads['meta']['prepared'] }}<br>OpenAI’s published auction rules · not a lead forecast · Residential · Houston suburbs</p>
</div>
@endsection
