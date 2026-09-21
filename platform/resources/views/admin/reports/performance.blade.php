@extends('layouts.admin')
@section('title', 'Performance report')
@section('meta', $perf['meta']['ytd'].' · Confidential client report')
@section('actions')
    <a class="btn" href="{{ route('admin.reports.pdf', $report->slug) }}"><i class="fas fa-file-arrow-down"></i> Download PDF</a>
@endsection

@section('content')
<div class="perf">
    <div class="perf-hero">
        <p class="perf-kicker">Prepared {{ $perf['meta']['prepared'] }} · Tomball HQ</p>
        <h2>Year-to-date digital growth</h2>
        <p>Website, Google Business, search, and Meta social reach for Tomball &amp; Greater Houston — the same areas as the original client report.</p>
    </div>

    <nav class="perf-nav" aria-label="Report sections">
        <a href="#gains">Gains</a>
        <a href="#gbp">GBP</a>
        <a href="#website">Website</a>
        <a href="#facebook">Social</a>
        <a href="#search">Search</a>
        <a href="#keywords">Keywords</a>
        <a href="#outlook">Outlook</a>
        <a href="#landings">Landings</a>
        <a href="#listings">Listings</a>
        <a href="{{ route('admin.ads') }}">Ads</a>
        <a href="{{ route('admin.reviews.index') }}">Reviews</a>
    </nav>

    <section class="perf-section" id="gains">
        <p class="perf-kicker">Executive summary</p>
        <h2>What we’ve gained this year</h2>
        <p class="perf-lead">Since analytics and local optimization went live mid-May, Core Four Roofing built a measurable digital footprint — across Maps, Search, the website, and Facebook brand discovery.</p>
        <div class="perf-gains">
            <article class="perf-gain">
                <span>Facebook views YTD</span>
                <b>35.1K</b>
                <em>+13.1% vs last window</em>
                <small>Apr 24 – Sep 21 · Meta Insights + post views</small>
            </article>
            <article class="perf-gain">
                <span>Website visitors gained</span>
                <b data-count="1222">1,222</b>
                <em>+11.8% · 1,222 YTD</em>
                <small>Active users through Sep 21</small>
            </article>
            <article class="perf-gain">
                <span>GBP profile views</span>
                <b data-count="270">270</b>
                <em>+10.4% vs prior period</em>
                <small>Aug 30 – Sep 21 · Google Business Profile</small>
            </article>
            <article class="perf-gain">
                <span>Brand search position</span>
                <b>1.07</b>
                <em>Was 2.35 · now page one, slot one</em>
                <small>“core four roofing” · Aug 28 – Sep 21</small>
            </article>
        </div>
        <div class="perf-callout">
            <strong>Client takeaway:</strong>
            Brand discovery is accelerating — Facebook views are up 13% in this window, website traffic is still climbing (+11.8%), and Maps plus search keep compounding. More homeowners and commercial prospects are finding Core Four Roofing.
        </div>
        <div class="perf-stories">
            <article>
                <h3>Social discovery</h3>
                <p><strong>35.1K</strong> Facebook views YTD, plus <strong>3,212</strong> new post views and <strong>178 clicks</strong> since Aug 28 — the page is still earning attention, up 13% this window.</p>
            </article>
            <article>
                <h3>Local presence</h3>
                <p>Search mobile views on the listing are up <strong>+14.8%</strong>. This window added <strong>63 direction requests</strong> and a <strong>5.0★</strong> average rating.</p>
            </article>
            <article>
                <h3>Brand ownership</h3>
                <p>Brand query <em>core four roofing</em> now ranks at <strong>position 1.07</strong> with a <strong>28% CTR</strong> — you own the search for your name.</p>
            </article>
        </div>
    </section>

    <section class="perf-section" id="gbp">
        <p class="perf-kicker">Google Business Profile</p>
        <h2>The listing is still gaining ground</h2>
        <p class="perf-lead">From Aug 30 to Sep 21 the Tomball profile earned <strong>270 views — up 10.4%</strong> — and <strong>100 interactions, up 13.5%</strong>. Search discovery is the engine: Google Search views rose <strong>9.7%</strong>, and mobile search kept the lead. Daily actions climbed through September after a quieter start.</p>
        <div class="perf-compare">
            <div><span>Profile views</span><b>270</b><em>↑ 10.4% vs prior period</em></div>
            <div><span>Interactions</span><b>100</b><em>↑ 13.5% vs prior (88)</em></div>
            <div><span>Average rating</span><b>5.0★</b><em>Perfect score this window</em></div>
        </div>
        <div class="perf-split">
            <div class="panel">
                <h3>Daily interactions</h3>
                <p class="perf-sub">{{ $perf['meta']['gbp_window'] }} · trending up vs previous period · Source: GBP Insights</p>
                <div class="perf-chart tall"><canvas id="gbpChart"></canvas></div>
            </div>
            <div class="panel">
                <h3>Action breakdown</h3>
                <p class="perf-sub">How customers used the listing this window</p>
                <div class="perf-metrics">
                    <div><span>Direction requests</span><strong>63 <small>navigating to you</small></strong></div>
                    <div><span>Website visits from GBP</span><strong>30</strong></div>
                    <div><span>Phone calls</span><strong>7</strong></div>
                    <div><span>Messaging</span><strong>0</strong></div>
                    <div><span>YTD interactions</span><strong>522 <small>422 + 100</small></strong></div>
                </div>
            </div>
        </div>
        <div class="perf-split">
            <div class="panel">
                <h3>Where views came from</h3>
                <p class="perf-sub">270 views · Search vs Maps · mobile vs desktop</p>
                <div class="perf-chart"><canvas id="gbpViewsMixChart"></canvas></div>
            </div>
            <div class="panel">
                <h3>Discovery mix</h3>
                <p class="perf-sub">Search is carrying growth this window</p>
                <div class="perf-metrics">
                    <div><span>Search · Mobile</span><strong>68 <small class="up">↑ 14.8%</small></strong></div>
                    <div><span>Search · Desktop</span><strong>93 <small class="up">↑ 8.1%</small></strong></div>
                    <div><span>Maps · Desktop</span><strong>77 <small class="up">↑ 15.2%</small></strong></div>
                    <div><span>Maps · Mobile</span><strong>31 <small class="up">↑ 6.9%</small></strong></div>
                    <div><span>Google Search views total</span><strong>161 <small class="up">↑ 9.7%</small></strong></div>
                </div>
            </div>
        </div>
        <div class="perf-split">
            <div class="panel">
                <h3>Phone calls by weekday</h3>
                <p class="perf-sub">7 calls · Tuesday and Friday led · Source: GBP Insights</p>
                <div class="perf-chart"><canvas id="gbpCallsChart"></canvas></div>
            </div>
            <div class="panel">
                <h3>What this means</h3>
                <p class="perf-sub">High-intent local demand, not just profile glances</p>
                <div class="perf-callout" style="margin-top:0.25rem">
                    <strong>Client takeaway:</strong>
                    Most actions are direction requests (63 of 100). People aren’t just viewing the listing — they’re routing to the shop. Search mobile up 14.8% is the awareness unlock to keep feeding with photos, posts, and review replies.
                </div>
            </div>
        </div>
    </section>

    <section class="perf-section" id="website">
        <p class="perf-kicker">Website · Google Analytics 4</p>
        <h2>Traffic is still climbing</h2>
        <p class="perf-lead">From Aug 28 to Sep 21 the site added <strong>375 active users — up 11.9%</strong> versus the prior window. Views rose <strong>14.2%</strong> and events <strong>9.8%</strong>. Year-to-date, Core Four has now welcomed about <strong>1,222 active users</strong> since tracking launched mid-May.</p>
        <div class="perf-compare">
            <div><span>Active users</span><b>375</b><em>↑ 11.9% vs prior period</em></div>
            <div><span>Page views</span><b>622</b><em>↑ 14.2% vs prior period</em></div>
            <div><span>Event count</span><b>1.8K</b><em>↑ 9.8% vs prior period</em></div>
        </div>
        <div class="perf-split">
            <div class="panel">
                <h3>Active users · latest window</h3>
                <p class="perf-sub">{{ $perf['meta']['window'] }} · vs previous period · Source: GA4</p>
                <div class="perf-chart tall"><canvas id="gaChart"></canvas></div>
            </div>
            <div class="panel">
                <h3>Audience gained</h3>
                <p class="perf-sub">Latest window + year-to-date</p>
                <div class="perf-metrics">
                    <div><span>Active users (Aug 28 – Sep 21)</span><strong>375 <small class="up">↑ 11.9%</small></strong></div>
                    <div><span>New users this window</span><strong>364 <small>97% new</small></strong></div>
                    <div><span>Sessions</span><strong>421</strong></div>
                    <div><span>YTD active users</span><strong>1,222 <small class="up">since May</small></strong></div>
                    <div><span>YTD new users</span><strong>1,206</strong></div>
                </div>
                <div class="perf-callout" style="margin-top:1.1rem">
                    <strong>Improvement lens:</strong>
                    Almost every visitor in this window was new (364 of 375). The site is still expanding the audience — not just recycling the same people.
                </div>
            </div>
        </div>
        <div class="perf-split">
            <div class="panel">
                <h3>How people arrived</h3>
                <p class="perf-sub">Sessions by source / medium · Aug 28 – Sep 21</p>
                <div class="perf-chart"><canvas id="gaSourceChart"></canvas></div>
            </div>
            <div class="panel">
                <h3>Channel mix</h3>
                <p class="perf-sub">421 sessions · Source: GA4</p>
                <div class="perf-metrics">
                    <div><span>Direct</span><strong>295 <small>70%</small></strong></div>
                    <div><span>Google organic</span><strong>80 <small class="up">search demand</small></strong></div>
                    <div><span>Facebook referral</span><strong>12 <small>social → site</small></strong></div>
                    <div><span>Bing organic</span><strong>7</strong></div>
                    <div><span>Key events tracked</span><strong>0 <small>next setup</small></strong></div>
                </div>
            </div>
        </div>
    </section>

    <section class="perf-section" id="facebook">
        <p class="perf-kicker">Facebook &amp; Instagram · Meta</p>
        <h2>The awareness surge kept going</h2>
        <p class="perf-lead">Through Aug 27 the page had already delivered <strong>31,849 views</strong>. From Aug 28 to Sep 21, Core Four kept posting: <strong>13 Facebook posts</strong> added <strong>3,212 views — up 13.1%</strong>, <strong>1,728 reach</strong>, and <strong>178 clicks</strong>. Year-to-date Facebook views now sit at <strong>35.1K</strong>.</p>
        <div class="perf-compare">
            <div><span>Facebook views YTD</span><b>35.1K</b><em>31,849 through Aug 27 + 3,212 since</em></div>
            <div><span>Latest window · post views</span><b>3,212</b><em>Aug 28 – Sep 21 · 13 photo posts · ↑ 13.1%</em></div>
            <div><span>Clicks on posts</span><b>178</b><em>People acting on the content</em></div>
        </div>
        <div class="perf-callout">
            <strong>Why this matters:</strong>
            The awareness climb was not a one-off. Posting stayed on a near-daily cadence through mid-September, with zero negative feedback in the window — and the Sep 3 tile-repair story alone earned 910 views.
        </div>
        <div class="perf-split">
            <div class="panel">
                <h3>Daily unique engagements</h3>
                <p class="perf-sub">Aug 28 – Sep 21 · Unique people who engaged · Source: Meta page insights</p>
                <div class="perf-chart tall"><canvas id="fbViewsChart"></canvas></div>
            </div>
            <div class="panel">
                <h3>Latest window snapshot</h3>
                <p class="perf-sub">Facebook posts published Aug 28 – Sep 21</p>
                <div class="perf-metrics">
                    <div><span>Posts published</span><strong>13</strong></div>
                    <div><span>Post reach</span><strong>1,728</strong></div>
                    <div><span>Unique engagers</span><strong>157</strong></div>
                    <div><span>Reactions · comments · shares</span><strong>82</strong></div>
                    <div><span>Negative feedback</span><strong>0 <small class="up">clean window</small></strong></div>
                </div>
            </div>
        </div>
        <div class="perf-split">
            <div class="panel">
                <h3>Engagement mix</h3>
                <p class="perf-sub">Aug 28 – Sep 21 Facebook posts</p>
                <div class="perf-metrics">
                    <div><span>Reactions</span><strong>68</strong></div>
                    <div><span>Comments</span><strong>11 <small class="up">conversation</small></strong></div>
                    <div><span>Shares</span><strong>2</strong></div>
                    <div><span>Link / photo clicks</span><strong>178 <small class="up">highest action</small></strong></div>
                    <div><span>Prior 90-day engagement</span><strong>683 <small>+10.4%</small></strong></div>
                </div>
            </div>
            <div class="panel">
                <h3>Top posts since Aug 28</h3>
                <p class="perf-sub">Proof-of-work and “how not to repair” content leading</p>
                <table>
                    <tr><th>Posted</th><th>Theme</th><th>Views</th><th>Eng.</th></tr>
                    @foreach($perf['posts'] as $post)
                        <tr>
                            <td>{{ $post['posted'] }}</td>
                            <td>{{ $post['theme'] }}</td>
                            <td>{{ number_format($post['views']) }}</td>
                            <td>{{ $post['engagement'] }}</td>
                        </tr>
                    @endforeach
                </table>
                <div class="perf-callout" style="margin-top:1.1rem">
                    <strong>Content win:</strong>
                    The Sep 3 contractor-fail / tile repair story is the standout — 910 views and 82 clicks. Problem-solution posts plus jobsite photography are what this audience stops for.
                </div>
            </div>
        </div>
        <div class="perf-split">
            <div class="panel">
                <h3>Instagram · same window</h3>
                <p class="perf-sub">Aug 28 – Sep 21 · 13 posts · Source: Meta IG insights</p>
                <div class="perf-chart"><canvas id="igReachChart"></canvas></div>
            </div>
            <div class="panel">
                <h3>Instagram snapshot</h3>
                <p class="perf-sub">Cross-posted project content picking up a second audience</p>
                <div class="perf-metrics">
                    <div><span>IG views</span><strong>945</strong></div>
                    <div><span>IG reach</span><strong>521</strong></div>
                    <div><span>Likes</span><strong>44</strong></div>
                    <div><span>Comments · shares</span><strong>9</strong></div>
                    <div><span>Top IG post</span><strong>211 <small>tile carousel</small></strong></div>
                </div>
            </div>
        </div>
        <div class="perf-stories">
            <article>
                <h3>YTD awareness</h3>
                <p>The page kept compounding after the summer surge, then another <strong>3.2K</strong> post views landed by Sep 21 — <strong>+13.1%</strong> this window.</p>
            </article>
            <article>
                <h3>Clicks, not just likes</h3>
                <p><strong>178 clicks</strong> on the latest posts beat reactions — people are tapping through, not only scrolling past.</p>
            </article>
            <article>
                <h3>Photos still win</h3>
                <p>All 13 latest Facebook posts were photos. Project visuals remain the engagement engine; keep pairing them with a clear next step.</p>
            </article>
        </div>
    </section>

    <section class="perf-section" id="search">
        <p class="perf-kicker">Google Search Console</p>
        <h2>More visibility. Brand locked at #1.</h2>
        <p class="perf-lead">From Aug 28 to Sep 21 the site earned <strong>67 clicks</strong> and <strong>8,928 impressions</strong> — about <strong>357 impressions a day</strong>, up 11.9% from ~319 in the prior window. Brand query <em>core four roofing</em> held at <strong>1.07</strong>.</p>
        <div class="perf-compare">
            <div><span>Impressions / day</span><b>357</b><em>↑ 11.9% vs ~319 in the prior window</em></div>
            <div><span>Brand query position</span><b>1.07</b><em>Was 2.35 · 28% CTR</em></div>
            <div><span>City landings in Google</span><b>69</b><em>1,993 impressions already · 13 clicks</em></div>
        </div>
        <div class="perf-tabs" role="tablist" aria-label="Search charts">
            <button class="perf-tab active" type="button" data-gsc="position">Avg. position</button>
            <button class="perf-tab" type="button" data-gsc="ctr">CTR</button>
            <button class="perf-tab" type="button" data-gsc="clicks">Clicks</button>
            <button class="perf-tab" type="button" data-gsc="impressions">Impressions</button>
        </div>
        <div class="panel">
            <h3 id="gscTitle">Daily average position</h3>
            <p class="perf-sub" id="gscSub">{{ $perf['meta']['window'] }} · Lower is better · Source: Google Search Console</p>
            <div class="perf-chart tall"><canvas id="gscChart"></canvas></div>
        </div>
        <div class="perf-split">
            <div class="panel">
                <h3>Latest window snapshot</h3>
                <p class="perf-sub">Aug 28 – Sep 21 · Domain property</p>
                <div class="perf-metrics">
                    <div><span>Clicks</span><strong>67 <small>~2.7 / day</small></strong></div>
                    <div><span>Impressions</span><strong>8,928</strong></div>
                    <div><span>CTR</span><strong>0.75%</strong></div>
                    <div><span>Mobile avg. position</span><strong>13.7 <small class="up">stronger than desktop</small></strong></div>
                    <div><span>YTD clicks (Apr 22 – Sep 21)</span><strong>302</strong></div>
                </div>
            </div>
            <div class="panel">
                <h3>Device mix</h3>
                <p class="perf-sub">Clicks by device · Aug 28 – Sep 21</p>
                <div class="perf-chart"><canvas id="deviceChart"></canvas></div>
            </div>
        </div>
    </section>

    <section class="perf-section" id="keywords">
        <p class="perf-kicker">Queries &amp; pages</p>
        <h2>City pages are starting to rank</h2>
        <p class="perf-lead">Brand is locked. Local commercial landings are already in Google — League City and Humble both picked up clicks, and 69 city URLs have impression share.</p>
        <div class="perf-split">
            <div class="panel">
                <h3>Top search queries</h3>
                <p class="perf-sub">{{ $perf['meta']['window'] }} · Google Search Console</p>
                <table>
                    <tr><th>Query</th><th>Clicks</th><th>Impr.</th><th>Pos.</th></tr>
                    @foreach($perf['queries'] as $query)
                        <tr>
                            <td>{{ $query['query'] }}</td>
                            <td>{{ $query['clicks'] }}</td>
                            <td>{{ number_format($query['impressions']) }}</td>
                            <td @class(['perf-pos' => $query['position'] <= 10])>{{ number_format($query['position'], 1) }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
            <div class="panel">
                <h3>Top landing pages</h3>
                <p class="perf-sub">Highest click &amp; impression pages this window</p>
                <table>
                    <tr><th>Page</th><th>Clicks</th><th>Impr.</th></tr>
                    @foreach($perf['pages'] as $page)
                        <tr>
                            <td>{{ $page['page'] }}</td>
                            <td>{{ $page['clicks'] }}</td>
                            <td>{{ number_format($page['impressions']) }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </section>

    <section class="perf-section" id="outlook">
        <p class="perf-kicker">Forward look</p>
        <h2>Keep the momentum</h2>
        <p class="perf-lead">The foundation is in place. The gains below are the clearest proof points to carry into H2 — and the opportunities that will multiply them.</p>
        <div class="perf-stories">
            <article>
                <h3>Double down on Maps</h3>
                <p>Search mobile views on the listing are up 14.8%. Keep photos, posts, and review replies coming so that discovery turns into more direction requests and calls.</p>
            </article>
            <article>
                <h3>Activate city landings</h3>
                <p>71 residential &amp; commercial city pages are live. Next: internal links, GBP area posts, and local content so each market starts ranking.</p>
            </article>
            <article>
                <h3>Track conversions</h3>
                <p>Key events in GA4 are still at zero. Wiring form submits and call clicks will show not just traffic gained — but leads gained.</p>
            </article>
        </div>
        <div class="perf-callout">
            <strong>Next move:</strong>
            Paid residential leads for tile, stone-coated steel, metal, and slate homeowners are on the <a href="{{ route('admin.ads') }}">ads plan</a>. Protecting the 5.0★ listing while asking every finished job for a review is on <a href="{{ route('admin.reviews.index') }}">Review Shield</a>.
        </div>
    </section>

    <section class="perf-section" id="landings">
        <p class="perf-kicker">Local SEO expansion</p>
        <h2>Growing residential &amp; commercial landing pages</h2>
        <p class="perf-lead">We’ve published <strong>71 city landing pages</strong> — {{ $perf['landings']['residential']['count'] }} residential in Houston metro and {{ $perf['landings']['commercial']['count'] }} commercial across Houston, Austin, and Dallas–Fort Worth — so Core Four Roofing can rank for local “roofing near me” demand in every priority market.</p>
        <div class="perf-compare">
            <div><span>Residential landings</span><b>{{ $perf['landings']['residential']['count'] }}</b><em>Houston metro · updated Sep 21, 2026</em></div>
            <div><span>Commercial landings</span><b>{{ $perf['landings']['commercial']['count'] }}</b><em>Houston · Austin · DFW · updated Sep 21, 2026</em></div>
            <div><span>Texas markets covered</span><b>3</b><em>Houston · Austin · DFW metros</em></div>
        </div>
        <div class="perf-callout">
            <strong>Growth play:</strong>
            These pages are the foundation for local search scale — each city URL targets homeowners and property managers searching in that market. Click any city to open the live page.
        </div>
        <div class="perf-landings">
            @foreach(['residential' => 'Residential landing pages', 'commercial' => 'Commercial landing pages'] as $type => $title)
                <div class="panel">
                    <h3>{{ $title }}</h3>
                    <p class="perf-sub">{{ $perf['landings'][$type]['count'] }} live city pages</p>
                    @foreach($perf['landings'][$type]['metros'] as $metro)
                        <div class="perf-metro">
                            <div class="perf-metro-label">{{ $metro['label'] }}</div>
                            <div class="perf-chips">
                                @foreach($metro['cities'] as $city)
                                    <a href="{{ $city['url'] }}" target="_blank" rel="noopener noreferrer">{{ $city['city'] }}</a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </section>

    <section class="perf-section" id="listings">
        <p class="perf-kicker">Citation footprint</p>
        <h2>Business listings are live across the web</h2>
        <p class="perf-lead">The Tomball HQ listing — 22955 Texas 249 Ste 26, (281) 541-0027 — is <strong>connected on {{ $perf['listings']['count'] }} directories</strong>, including Google, Apple, Bing, Yelp, Nextdoor, MapQuest, and BBB. That’s NAP consistency at scale so customers find the same name, address, and phone wherever they search.</p>
        <div class="perf-compare">
            <div><span>Directories connected</span><b>{{ $perf['listings']['count'] }}</b><em>Active listings · Sep 21, 2026</em></div>
            <div><span>Public listing links</span><b>{{ $perf['listings']['linked'] }}</b><em>Click any chip with a link to open</em></div>
            <div><span>Location published</span><b>1</b><em>Tomball HQ on every connected directory</em></div>
        </div>
        <div class="perf-callout">
            <strong>Why this matters:</strong>
            Search engines and AI assistants pull from this network. A connected listing on Apple, Bing, Yelp, and Google is how Core Four shows up in Maps, Siri, Alexa, and local pack results — not just on the website.
        </div>
        <div class="panel">
            <h3>Priority directories</h3>
            <p class="perf-sub">The platforms customers actually open</p>
            <div class="perf-chips">
                @foreach($perf['listings']['featured'] as $listing)
                    @if($listing['url'])
                        <a href="{{ $listing['url'] }}" target="_blank" rel="noopener noreferrer">{{ $listing['name'] }}</a>
                    @else
                        <span>{{ $listing['name'] }}</span>
                    @endif
                @endforeach
            </div>
        </div>
        <div class="panel">
            <h3>All connected listings</h3>
            <p class="perf-sub">{{ $perf['listings']['count'] }} live · same NAP everywhere</p>
            <div class="perf-chips">
                @foreach($perf['listings']['all'] as $listing)
                    @if($listing['url'])
                        <a href="{{ $listing['url'] }}" target="_blank" rel="noopener noreferrer">{{ $listing['name'] }}</a>
                    @else
                        <span>{{ $listing['name'] }}</span>
                    @endif
                @endforeach
            </div>
        </div>
    </section>

    <p class="perf-foot">Core Four Roofing · Confidential client performance report · Prepared {{ $perf['meta']['prepared'] }}<br>Sources: Google Analytics 4 · Google Search Console · Google Business Profile · Meta · Directory listings (Yext)</p>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
const data = @json($perf['charts']);
const ink = '#101015';
const brand = '#144b24';
const brandMid = '#269a47';
const lime = '#bfe866';
const muted = '#45664f';

Chart.defaults.font.family = 'Poppins, sans-serif';
Chart.defaults.color = muted;
Chart.defaults.borderColor = 'rgba(219,215,207,0.9)';

function lineOpts(yTitle, reverse = false) {
  return {
    responsive: true,
    maintainAspectRatio: false,
    interaction: { mode: 'index', intersect: false },
    plugins: {
      legend: { display: false },
      tooltip: { backgroundColor: ink, titleFont: { weight: '600' }, padding: 10, cornerRadius: 8 }
    },
    scales: {
      x: { grid: { display: false }, ticks: { maxRotation: 0, autoSkipPadding: 12 } },
      y: {
        reverse,
        title: { display: true, text: yTitle, color: muted, font: { size: 11, weight: '600' } },
        grid: { color: 'rgba(219,215,207,0.7)' },
        ticks: { precision: 0 }
      }
    }
  };
}

new Chart(document.getElementById('gbpChart'), {
  type: 'line',
  data: {
    labels: data.gbp.labels,
    datasets: [
      { label: 'This period', data: data.gbp.current, borderColor: brand, backgroundColor: 'rgba(191,232,102,0.22)', fill: true, tension: 0.35, pointRadius: 3, borderWidth: 2.5 },
      { label: 'Previous period', data: data.gbp.previous, borderColor: '#8aa890', borderDash: [6, 4], fill: false, tension: 0.35, pointRadius: 0, borderWidth: 2 }
    ]
  },
  options: {
    responsive: true, maintainAspectRatio: false, interaction: { mode: 'index', intersect: false },
    plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, usePointStyle: true } } },
    scales: {
      x: { grid: { display: false } },
      y: { title: { display: true, text: 'Interactions', color: muted, font: { size: 11, weight: '600' } }, grid: { color: 'rgba(219,215,207,0.7)' }, ticks: { precision: 0 } }
    }
  }
});

new Chart(document.getElementById('gbpViewsMixChart'), {
  type: 'doughnut',
  data: { labels: data.gbp.views.labels, datasets: [{ data: data.gbp.views.data, backgroundColor: [brand, brandMid, lime, '#45664f'], borderWidth: 0 }] },
  options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true, font: { size: 11 } } } }, cutout: '58%' }
});

new Chart(document.getElementById('gbpCallsChart'), {
  type: 'bar',
  data: { labels: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'], datasets: [{ label: 'Phone calls', data: data.gbp.calls, backgroundColor: brand, borderRadius: 2 }] },
  options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { grid: { display: false } }, y: { title: { display: true, text: 'Calls', color: muted, font: { size: 11, weight: '600' } }, ticks: { stepSize: 1, precision: 0 }, grid: { color: 'rgba(219,215,207,0.7)' } } } }
});

new Chart(document.getElementById('gaChart'), {
  type: 'line',
  data: {
    labels: data.ga.labels,
    datasets: [
      { label: 'Aug 28 – Sep 21', data: data.ga.current, borderColor: brand, backgroundColor: 'rgba(191,232,102,0.22)', fill: true, tension: 0.3, pointRadius: 2.5, borderWidth: 2.5 },
      { label: 'Previous period', data: data.ga.previous, borderColor: '#8aa890', borderDash: [6, 4], fill: false, tension: 0.3, pointRadius: 0, borderWidth: 2 }
    ]
  },
  options: {
    responsive: true, maintainAspectRatio: false, interaction: { mode: 'index', intersect: false },
    plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, usePointStyle: true } } },
    scales: {
      x: { grid: { display: false }, ticks: { maxRotation: 0, autoSkipPadding: 8 } },
      y: { title: { display: true, text: 'Active users', color: muted, font: { size: 11, weight: '600' } }, grid: { color: 'rgba(219,215,207,0.7)' }, ticks: { precision: 0 } }
    }
  }
});

new Chart(document.getElementById('gaSourceChart'), {
  type: 'doughnut',
  data: { labels: data.sources.labels, datasets: [{ data: data.sources.data, backgroundColor: [brand, brandMid, lime, '#45664f', '#dbd7cf'], borderWidth: 0 }] },
  options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true, font: { size: 11 } } } }, cutout: '58%' }
});

let gscChart;
const gscMeta = {
  position: { title: 'Daily average position', sub: '{{ $perf['meta']['window'] }} · Lower is better · Source: Google Search Console', y: 'Avg. position', reverse: true, color: brandMid, key: 'position' },
  ctr: { title: 'Daily click-through rate', sub: '{{ $perf['meta']['window'] }} · Source: Google Search Console', y: 'CTR (%)', reverse: false, color: brand, key: 'ctr' },
  clicks: { title: 'Daily clicks', sub: '{{ $perf['meta']['window'] }} · Source: Google Search Console', y: 'Clicks', reverse: false, color: brand, key: 'clicks' },
  impressions: { title: 'Daily impressions', sub: '{{ $perf['meta']['window'] }} · Source: Google Search Console', y: 'Impressions', reverse: false, color: muted, key: 'impressions' }
};

function renderGsc(mode = 'position') {
  const m = gscMeta[mode];
  document.getElementById('gscTitle').textContent = m.title;
  document.getElementById('gscSub').textContent = m.sub;
  if (gscChart) gscChart.destroy();
  gscChart = new Chart(document.getElementById('gscChart'), {
    type: 'line',
    data: {
      labels: data.gsc.map((d) => d.week),
      datasets: [{ label: m.title, data: data.gsc.map((d) => d[m.key]), borderColor: m.color, backgroundColor: 'rgba(191,232,102,0.22)', fill: mode === 'position' || mode === 'ctr', tension: 0.35, pointRadius: 3, borderWidth: 2.5 }]
    },
    options: lineOpts(m.y, m.reverse)
  });
}
renderGsc('position');
document.querySelectorAll('[data-gsc]').forEach((btn) => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('[data-gsc]').forEach((b) => b.classList.remove('active'));
    btn.classList.add('active');
    renderGsc(btn.dataset.gsc);
  });
});

new Chart(document.getElementById('deviceChart'), {
  type: 'doughnut',
  data: { labels: data.devices.labels, datasets: [{ data: data.devices.data, backgroundColor: [brandMid, brand], borderWidth: 0 }] },
  options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, usePointStyle: true } } }, cutout: '62%' }
});

new Chart(document.getElementById('fbViewsChart'), {
  type: 'line',
  data: {
    labels: data.facebook.map((d) => d.d),
    datasets: [{ label: 'Unique user engagements', data: data.facebook.map((d) => d.v), borderColor: brand, backgroundColor: 'rgba(191,232,102,0.25)', fill: true, tension: 0.3, pointRadius: 2.5, borderWidth: 2.5 }]
  },
  options: lineOpts('Unique engagements')
});

new Chart(document.getElementById('igReachChart'), {
  type: 'bar',
  data: { labels: data.instagram.labels, datasets: [{ label: 'IG post views', data: data.instagram.data, backgroundColor: brandMid, borderRadius: 2 }] },
  options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { grid: { display: false }, ticks: { maxRotation: 45, autoSkip: true } }, y: { title: { display: true, text: 'Views', color: muted, font: { size: 11, weight: '600' } }, grid: { color: 'rgba(219,215,207,0.7)' } } } }
});
</script>
@endpush
