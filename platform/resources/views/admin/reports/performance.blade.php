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
                <span>Facebook post views</span>
                <b>1,608</b>
                <em>12 posts · 58 clicks</em>
                <small>Sep 21 – Oct 18 · Meta post export</small>
            </article>
            <article class="perf-gain">
                <span>Website visitors gained</span>
                <b data-count="8741">8,741</b>
                <em>7,815 first visits from t.co</em>
                <small>Active users · Sep 21 – Oct 18</small>
            </article>
            <article class="perf-gain">
                <span>GBP profile views</span>
                <b data-count="1674">1,674</b>
                <em>333 asked for directions</em>
                <small>May – Oct · about 1 in 3 viewers acted</small>
            </article>
            <article class="perf-gain">
                <span>Brand query clicks</span>
                <b>24</b>
                <em>+60% vs the prior 28 days</em>
                <small>“core four roofing” · Sep 21 – Oct 18</small>
            </article>
        </div>
        <div class="perf-callout">
            <strong>Client takeaway:</strong>
            Search impressions rose 36% and clicks rose 21% in the last 28 days. The homepage still takes almost every click. Facebook kept posting, and the Oct 2 house — open deck to finished aerial — carried the window with 424 views and 34 clicks.
        </div>
        <div class="perf-stories">
            <article>
                <h3>Social discovery</h3>
                <p><strong>1,608</strong> Facebook views and <strong>58 clicks</strong> across 12 posts. That is quieter than the 3,212 views in the Aug 28 – Sep 21 window. Instagram added <strong>367 views</strong>.</p>
            </article>
            <article>
                <h3>Local presence</h3>
                <p>From May through October the Tomball listing was viewed <strong>1,674 times</strong>. <strong>514 people acted</strong>, and <strong>333 of those actions were direction requests</strong> — someone asking Google how to get to the shop.</p>
            </article>
            <article>
                <h3>Brand ownership</h3>
                <p><em>core four roofing</em> led search with <strong>24 clicks, up 60%</strong>. Position was last measured at <strong>1.07</strong> on Sep 21. This Insights pull did not include a new position.</p>
            </article>
        </div>
    </section>

    <section class="perf-section" id="gbp">
        <p class="perf-kicker">Google Business Profile</p>
        <h2>People who find the listing try to get to the shop</h2>
        <p class="perf-lead">From May through October the Tomball profile was viewed <strong>1,674 times</strong>. <strong>514 people did something with it — about 1 in 3.</strong> Direction requests were <strong>333</strong>, about 2 in 3 of every action. Another <strong>49 called</strong> straight from the listing and <strong>127 clicked through</strong> to the website.</p>
        <div class="perf-compare">
            <div><span>Profile views</span><b>1,674</b><em>May – Oct 2026</em></div>
            <div><span>People who acted</span><b>514</b><em>About 1 in 3 viewers</em></div>
            <div><span>Direction requests</span><b>333</b><em>About 2 in 3 of all actions</em></div>
        </div>
        <div class="perf-split">
            <div class="panel">
                <h3>What people did</h3>
                <p class="perf-sub">{{ $perf['meta']['gbp_window'] }} · 514 actions · Source: Business Profile</p>
                <div class="perf-chart tall"><canvas id="gbpChart"></canvas></div>
            </div>
            <div class="panel">
                <h3>Action breakdown</h3>
                <p class="perf-sub">Directions first, then the website, then the phone</p>
                <div class="perf-metrics">
                    <div><span>Direction requests</span><strong>333 <small>65% of actions</small></strong></div>
                    <div><span>Website clicks</span><strong>127</strong></div>
                    <div><span>Phone calls</span><strong>49</strong></div>
                    <div><span>Chat clicks</span><strong>5</strong></div>
                    <div><span>Actions per 100 views</span><strong>31</strong></div>
                </div>
            </div>
        </div>
        <div class="perf-split">
            <div class="panel">
                <h3>Where views came from</h3>
                <p class="perf-sub">1,674 views · Search 872 · Maps 802</p>
                <div class="perf-chart"><canvas id="gbpViewsMixChart"></canvas></div>
            </div>
            <div class="panel">
                <h3>Discovery mix</h3>
                <p class="perf-sub">Maps is almost half of how people find the shop</p>
                <div class="perf-metrics">
                    <div><span>Search · Desktop</span><strong>589 <small>35%</small></strong></div>
                    <div><span>Maps · Desktop</span><strong>522 <small>31%</small></strong></div>
                    <div><span>Search · Mobile</span><strong>283 <small>17%</small></strong></div>
                    <div><span>Maps · Mobile</span><strong>280 <small>17%</small></strong></div>
                    <div><span>Maps, desktop and mobile</span><strong>802 <small>48% of views</small></strong></div>
                </div>
            </div>
        </div>
        <div class="perf-split">
            <div class="panel">
                <h3>Searches that showed the listing</h3>
                <p class="perf-sub">Google’s counted terms · May – Oct</p>
                <div class="perf-metrics">
                    <div><span>core four roofing</span><strong>25 <small>the brand</small></strong></div>
                    <div><span>roofers cypress tx</span><strong>24 <small>a city search, not the name</small></strong></div>
                    <div><span>Shop address in Tomball</span><strong>&lt; 15</strong></div>
                    <div><span>best roofing company 77070</span><strong>&lt; 15</strong></div>
                    <div><span>best roofing company 77373</span><strong>&lt; 15</strong></div>
                </div>
            </div>
            <div class="panel">
                <h3>What this means</h3>
                <p class="perf-sub">The listing is doing the job of a storefront</p>
                <div class="perf-callout" style="margin-top:0.25rem">
                    <strong>Client takeaway:</strong>
                    About 1 in 3 people who saw the profile took an action, and most of those actions were direction requests. “roofers cypress tx” showed the listing 24 times — one shy of the brand search. People are finding Core Four when they look for a roofer, not only when they already know the name. October is still open, so this is the season so far, not a finished month.
                </div>
            </div>
        </div>
    </section>

    <section class="perf-section" id="website">
        <p class="perf-kicker">Website · Google Analytics 4</p>
        <h2>A spike, mostly from t.co</h2>
        <p class="perf-lead">From Sep 21 to Oct 18, Analytics recorded <strong>8,741 active users</strong>, <strong>9,706 new users</strong>, and <strong>9,061 sessions</strong>. <strong>7,994 sessions — about 88% — came from t.co</strong>, the Twitter link shortener. Average engagement was <strong>4.2 seconds</strong>. Direct sessions were <strong>916</strong>, up from 295 in the prior window. Google organic was <strong>67</strong>, down from 80.</p>
        <div class="perf-compare">
            <div><span>Active users</span><b>8,741</b><em>Sep 21 – Oct 18</em></div>
            <div><span>Sessions from t.co</span><b>7,994</b><em>About 88% of 9,061 sessions</em></div>
            <div><span>Avg. engagement</span><b>4.2s</b><em>Per active user</em></div>
        </div>
        <div class="perf-split">
            <div class="panel">
                <h3>Active users · prior window</h3>
                <p class="perf-sub">Aug 28 – Sep 21 · daily series from the previous export · Source: GA4</p>
                <div class="perf-chart tall"><canvas id="gaChart"></canvas></div>
            </div>
            <div class="panel">
                <h3>This window</h3>
                <p class="perf-sub">{{ $perf['meta']['ga_window'] }} · Source: GA4 snapshot</p>
                <div class="perf-metrics">
                    <div><span>Active users</span><strong>8,741</strong></div>
                    <div><span>New users</span><strong>9,706</strong></div>
                    <div><span>Sessions</span><strong>9,061</strong></div>
                    <div><span>First visits from t.co</span><strong>7,815</strong></div>
                    <div><span>Prior window active users</span><strong>375 <small>Aug 28 – Sep 21</small></strong></div>
                </div>
                <div class="perf-callout" style="margin-top:1.1rem">
                    <strong>Read this carefully:</strong>
                    The jump is not a wave of homeowners. t.co sent most of the sessions, and the average visit lasted about 4 seconds. Key events are still 0.
                </div>
            </div>
        </div>
        <div class="perf-split">
            <div class="panel">
                <h3>How people arrived</h3>
                <p class="perf-sub">Sessions by source / medium · {{ $perf['meta']['ga_window'] }}</p>
                <div class="perf-chart"><canvas id="gaSourceChart"></canvas></div>
            </div>
            <div class="panel">
                <h3>Channel mix</h3>
                <p class="perf-sub">9,061 sessions · Source: GA4</p>
                <div class="perf-metrics">
                    <div><span>t.co / referral</span><strong>7,994 <small>Twitter links</small></strong></div>
                    <div><span>Direct</span><strong>916 <small>was 295</small></strong></div>
                    <div><span>Google organic</span><strong>67 <small>was 80</small></strong></div>
                    <div><span>Facebook referral</span><strong>13 <small>facebook.com + l.facebook.com</small></strong></div>
                    <div><span>Key events tracked</span><strong>0</strong></div>
                </div>
            </div>
        </div>
    </section>

    <section class="perf-section" id="facebook">
        <p class="perf-kicker">Facebook &amp; Instagram · Meta</p>
        <h2>Twelve posts, one house out in front</h2>
        <p class="perf-lead">From Sep 21 to Oct 18, Core Four published <strong>12 Facebook posts</strong> with <strong>1,608 views</strong>, <strong>976 reach</strong>, and <strong>58 clicks</strong>. That is below the prior window’s 3,212 views and 178 clicks. The Oct 2 residential project — open deck through the finished aerial — did the heavy lifting.</p>
        <div class="perf-compare">
            <div><span>Post views</span><b>1,608</b><em>12 photo posts · Sep 21 – Oct 18</em></div>
            <div><span>Reach</span><b>976</b><em>Lifetime reach on those posts</em></div>
            <div><span>Clicks on posts</span><b>58</b><em>34 of them on the Oct 2 house</em></div>
        </div>
        <div class="perf-callout">
            <strong>Why this matters:</strong>
            The page kept a steady photo cadence, about every other day. Views concentrated on finished work. The Oct 2 house earned 424 views. The next post, on Oct 9, earned 177.
        </div>
        <div class="perf-split">
            <div class="panel">
                <h3>Views by post</h3>
                <p class="perf-sub">Sep 21 – Oct 18 · Lifetime views · Source: Meta post export</p>
                <div class="perf-chart tall"><canvas id="fbViewsChart"></canvas></div>
            </div>
            <div class="panel">
                <h3>Latest window snapshot</h3>
                <p class="perf-sub">Facebook posts published Sep 21 – Oct 17</p>
                <div class="perf-metrics">
                    <div><span>Posts published</span><strong>12</strong></div>
                    <div><span>Post reach</span><strong>976</strong></div>
                    <div><span>Reactions</span><strong>34</strong></div>
                    <div><span>Comments</span><strong>3</strong></div>
                    <div><span>Shares</span><strong>0</strong></div>
                </div>
            </div>
        </div>
        <div class="perf-split">
            <div class="panel">
                <h3>Engagement mix</h3>
                <p class="perf-sub">Sep 21 – Oct 18 Facebook posts</p>
                <div class="perf-metrics">
                    <div><span>Reactions</span><strong>34</strong></div>
                    <div><span>Comments</span><strong>3</strong></div>
                    <div><span>Shares</span><strong>0</strong></div>
                    <div><span>Link / photo clicks</span><strong>58</strong></div>
                    <div><span>Prior window post views</span><strong>3,212 <small>Aug 28 – Sep 21</small></strong></div>
                </div>
            </div>
            <div class="panel">
                <h3>Top posts this window</h3>
                <p class="perf-sub">Engagement is reactions, comments, and shares</p>
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
                    The Oct 2 house is the standout — 424 views and 34 clicks. The in-progress deck and the finished aerial did more than the surrounding posts combined.
                </div>
            </div>
        </div>
        <div class="perf-split">
            <div class="panel">
                <h3>Instagram · same window</h3>
                <p class="perf-sub">Sep 21 – Oct 18 · 12 posts · Source: Meta IG insights</p>
                <div class="perf-chart"><canvas id="igReachChart"></canvas></div>
            </div>
            <div class="panel">
                <h3>Instagram snapshot</h3>
                <p class="perf-sub">Cross-posted project content picking up a second audience</p>
                <div class="perf-metrics">
                    <div><span>IG views</span><strong>367</strong></div>
                    <div><span>IG reach</span><strong>189</strong></div>
                    <div><span>Likes</span><strong>30</strong></div>
                    <div><span>Comments</span><strong>2</strong></div>
                    <div><span>Top IG post</span><strong>85 <small>Oct 2 carousel</small></strong></div>
                </div>
            </div>
        </div>
        <div class="perf-stories">
            <article>
                <h3>YTD awareness</h3>
                <p>Through Sep 21 the page total stood at <strong>35.1K</strong> views. This export is lifetime views on the 12 new posts, <strong>1,608</strong>, not a new page-wide year-to-date.</p>
            </article>
            <article>
                <h3>Clicks, not just likes</h3>
                <p><strong>58 clicks</strong> still beat the 34 reactions. Most of those clicks sat on the Oct 2 house.</p>
            </article>
            <article>
                <h3>Photos still win</h3>
                <p>All 12 Facebook posts were photos. The matching Instagram posts added 367 views, led by the same Oct 2 carousel at 85.</p>
            </article>
        </div>
    </section>

    <section class="perf-section" id="search">
        <p class="perf-kicker">Google Search Console</p>
        <h2>Impressions up. The homepage still takes the click.</h2>
        <p class="perf-lead">In the last 28 days, Sep 21 – Oct 18, Search Console shows <strong>46 clicks, up 21%</strong>, and <strong>9.41K impressions, up 36%</strong>. That is about <strong>336 impressions a day</strong> and <strong>1.6 clicks a day</strong>. <em>core four roofing</em> accounted for <strong>24</strong> of the clicks, up 60%.</p>
        <div class="perf-compare">
            <div><span>Impressions</span><b>9.41K</b><em>↑ 36% vs the prior 28 days</em></div>
            <div><span>Clicks</span><b>46</b><em>↑ 21% · about 1.6 a day</em></div>
            <div><span>Homepage clicks</span><b>42</b><em>38 on the main URL · 4 on www</em></div>
        </div>
        <div class="perf-tabs" role="tablist" aria-label="Search charts">
            <button class="perf-tab active" type="button" data-gsc="position">Avg. position</button>
            <button class="perf-tab" type="button" data-gsc="ctr">CTR</button>
            <button class="perf-tab" type="button" data-gsc="clicks">Clicks</button>
            <button class="perf-tab" type="button" data-gsc="impressions">Impressions</button>
        </div>
        <div class="panel">
            <h3 id="gscTitle">Daily average position</h3>
            <p class="perf-sub" id="gscSub">Aug 28 – Sep 21, 2026 · daily series from the previous export · Source: Google Search Console</p>
            <div class="perf-chart tall"><canvas id="gscChart"></canvas></div>
        </div>
        <div class="perf-split">
            <div class="panel">
                <h3>Latest window snapshot</h3>
                <p class="perf-sub">Sep 21 – Oct 18 · Search Console Insights</p>
                <div class="perf-metrics">
                    <div><span>Clicks</span><strong>46 <small>↑ 21%</small></strong></div>
                    <div><span>Impressions</span><strong>9.41K <small>↑ 36%</small></strong></div>
                    <div><span>Brand query clicks</span><strong>24 <small>↑ 60%</small></strong></div>
                    <div><span>Image search clicks</span><strong>1</strong></div>
                    <div><span>Prior window clicks</span><strong>67 <small>Aug 28 – Sep 21</small></strong></div>
                </div>
            </div>
            <div class="panel">
                <h3>Where the clicks came from</h3>
                <p class="perf-sub">Share of clicks by country · last 28 days</p>
                <div class="perf-chart"><canvas id="deviceChart"></canvas></div>
            </div>
        </div>
    </section>

    <section class="perf-section" id="keywords">
        <p class="perf-kicker">Queries &amp; pages</p>
        <h2>The brand query, then the homepage</h2>
        <p class="perf-lead">Search Console’s top list is short. <em>core four roofing</em> led with 24 clicks. The homepage took 38, and the www address took 4 more. About, commercial coatings, and contact each recorded a first click.</p>
        <div class="perf-split">
            <div class="panel">
                <h3>Top search queries</h3>
                <p class="perf-sub">{{ $perf['meta']['window'] }} · Google Search Console</p>
                <table>
                    <tr><th>Query</th><th>Clicks</th><th>Vs prior 28 days</th></tr>
                    @foreach($perf['queries'] as $query)
                        <tr>
                            <td>{{ $query['query'] }}</td>
                            <td>{{ $query['clicks'] }}</td>
                            <td>{{ $query['change'] }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
            <div class="panel">
                <h3>Top landing pages</h3>
                <p class="perf-sub">Highest click &amp; impression pages this window</p>
                <table>
                    <tr><th>Page</th><th>Clicks</th><th>Vs prior 28 days</th></tr>
                    @foreach($perf['pages'] as $page)
                        <tr>
                            <td>{{ $page['page'] }}</td>
                            <td>{{ $page['clicks'] }}</td>
                            <td>{{ $page['change'] }}</td>
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
                <p>Maps already accounts for 802 of 1,674 listing views, and direction requests are the main action at 333 since May. Photos, posts, and review replies are what keep that path full.</p>
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
            <div><span>Residential landings</span><b>{{ $perf['landings']['residential']['count'] }}</b><em>Houston metro · live list, Oct 20, 2026</em></div>
            <div><span>Commercial landings</span><b>{{ $perf['landings']['commercial']['count'] }}</b><em>Houston · Austin · DFW · live list, Oct 20, 2026</em></div>
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
  type: 'bar',
  data: {
    labels: data.gbp.actions.labels,
    datasets: [
      { label: 'Actions', data: data.gbp.actions.data, backgroundColor: [brand, brandMid, lime, '#45664f'], borderRadius: 6, maxBarThickness: 72 }
    ]
  },
  options: {
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
      x: { grid: { display: false } },
      y: { title: { display: true, text: 'Actions', color: muted, font: { size: 11, weight: '600' } }, grid: { color: 'rgba(219,215,207,0.7)' }, ticks: { precision: 0 } }
    }
  }
});

new Chart(document.getElementById('gbpViewsMixChart'), {
  type: 'doughnut',
  data: { labels: data.gbp.views.labels, datasets: [{ data: data.gbp.views.data, backgroundColor: [brand, brandMid, lime, '#45664f'], borderWidth: 0 }] },
  options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true, font: { size: 11 } } } }, cutout: '58%' }
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
  position: { title: 'Daily average position', sub: 'Aug 28 – Sep 21, 2026 · previous export · Lower is better', y: 'Avg. position', reverse: true, color: brandMid, key: 'position' },
  ctr: { title: 'Daily click-through rate', sub: 'Aug 28 – Sep 21, 2026 · previous export', y: 'CTR (%)', reverse: false, color: brand, key: 'ctr' },
  clicks: { title: 'Daily clicks', sub: 'Aug 28 – Sep 21, 2026 · previous export', y: 'Clicks', reverse: false, color: brand, key: 'clicks' },
  impressions: { title: 'Daily impressions', sub: 'Aug 28 – Sep 21, 2026 · previous export', y: 'Impressions', reverse: false, color: muted, key: 'impressions' }
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
  data: { labels: data.countries.labels, datasets: [{ data: data.countries.data, backgroundColor: [brand, brandMid, lime, '#45664f'], borderWidth: 0 }] },
  options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, usePointStyle: true } } }, cutout: '62%' }
});

new Chart(document.getElementById('fbViewsChart'), {
  type: 'line',
  data: {
    labels: data.facebook.map((d) => d.d),
    datasets: [{ label: 'Post views', data: data.facebook.map((d) => d.v), borderColor: brand, backgroundColor: 'rgba(191,232,102,0.25)', fill: true, tension: 0.3, pointRadius: 2.5, borderWidth: 2.5 }]
  },
  options: lineOpts('Post views')
});

new Chart(document.getElementById('igReachChart'), {
  type: 'bar',
  data: { labels: data.instagram.labels, datasets: [{ label: 'IG post views', data: data.instagram.data, backgroundColor: brandMid, borderRadius: 2 }] },
  options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { grid: { display: false }, ticks: { maxRotation: 45, autoSkip: true } }, y: { title: { display: true, text: 'Views', color: muted, font: { size: 11, weight: '600' } }, grid: { color: 'rgba(219,215,207,0.7)' } } } }
});
</script>
@endpush
