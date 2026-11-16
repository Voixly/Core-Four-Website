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
        <a href="#press">Press</a>
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
                <b>1,122</b>
                <em>12 posts · 29 clicks</em>
                <small>Oct 19 – Nov 14 · Meta post export</small>
            </article>
            <article class="perf-gain">
                <span>Website visitors gained</span>
                <b data-count="84846">84,846</b>
                <em>84,080 first visits from t.co</em>
                <small>Active users · Oct 19 – Nov 15</small>
            </article>
            <article class="perf-gain">
                <span>GBP profile views</span>
                <b data-count="1674">1,674</b>
                <em>333 asked for directions</em>
                <small>May – Oct · about 1 in 3 viewers acted</small>
            </article>
            <article class="perf-gain">
                <span>Brand query clicks</span>
                <b>34</b>
                <em>+42% vs Sep 21 – Oct 18</em>
                <small>“core four roofing” · Oct 19 – Nov 14</small>
            </article>
        </div>
        <div class="perf-callout">
            <strong>Client takeaway:</strong>
            Search clicks rose 35% and impressions slipped 4%. The homepage still takes almost every click. Analytics jumped again, and about 99% of sessions still came from t.co. Facebook’s 12 posts drew 1,122 views, down from 1,608, while Instagram rose to 529 views.
        </div>
        <div class="perf-stories">
            <article>
                <h3>Social discovery</h3>
                <p><strong>1,122</strong> Facebook views and <strong>29 clicks</strong> across 12 posts. That is quieter than the 1,608 views and 58 clicks in the Sep 21 – Oct 18 window. Instagram rose to <strong>529 views</strong>.</p>
            </article>
            <article>
                <h3>Local presence</h3>
                <p>From May through October the Tomball listing was viewed <strong>1,674 times</strong>. <strong>514 people acted</strong>, and <strong>333 of those actions were direction requests</strong> — someone asking Google how to get to the shop.</p>
            </article>
            <article>
                <h3>Brand ownership</h3>
                <p><em>core four roofing</em> led search with <strong>34 clicks, up from 24</strong>. The average position for that query moved from <strong>1.6 to 3.3</strong>.</p>
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
                    About 1 in 3 people who saw the profile took an action, and most of those actions were direction requests. “roofers cypress tx” showed the listing 24 times — one shy of the brand search. People are finding Core Four when they look for a roofer, not only when they already know the name. This section is still the May–October pull. November is not in it.
                </div>
            </div>
        </div>
    </section>

    <section class="perf-section" id="website">
        <p class="perf-kicker">Website · Google Analytics 4</p>
        <h2>A spike, mostly from t.co</h2>
        <p class="perf-lead">From Oct 19 to Nov 15, Analytics recorded <strong>84,846 active users</strong>, <strong>97,835 new users</strong>, and <strong>90,305 sessions</strong>. <strong>89,455 sessions — about 99% — came from t.co</strong>, the Twitter link shortener. Average engagement was <strong>2.3 seconds</strong>. Direct sessions were <strong>724</strong>, down from 916. Google organic was <strong>88</strong>, up from 67.</p>
        <div class="perf-compare">
            <div><span>Active users</span><b>84,846</b><em>Oct 19 – Nov 15</em></div>
            <div><span>Sessions from t.co</span><b>89,455</b><em>About 99% of 90,305 sessions</em></div>
            <div><span>Avg. engagement</span><b>2.3s</b><em>Per active user</em></div>
        </div>
        <div class="perf-split">
            <div class="panel">
                <h3>Active users · this window</h3>
                <p class="perf-sub">{{ $perf['meta']['ga_window'] }} · daily active users · Source: GA4</p>
                <div class="perf-chart tall"><canvas id="gaChart"></canvas></div>
            </div>
            <div class="panel">
                <h3>This window</h3>
                <p class="perf-sub">{{ $perf['meta']['ga_window'] }} · Source: GA4 snapshot</p>
                <div class="perf-metrics">
                    <div><span>Active users</span><strong>84,846</strong></div>
                    <div><span>New users</span><strong>97,835</strong></div>
                    <div><span>Sessions</span><strong>90,305</strong></div>
                    <div><span>First visits from t.co</span><strong>84,080</strong></div>
                    <div><span>Prior window active users</span><strong>8,741 <small>Sep 21 – Oct 18</small></strong></div>
                </div>
                <div class="perf-callout" style="margin-top:1.1rem">
                    <strong>Read this carefully:</strong>
                    The jump is not a wave of homeowners. t.co sent about 99% of the sessions, and the average visit lasted about 2.3 seconds. Key events are still 0. One session came from chatgpt.com.
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
                <p class="perf-sub">90,305 sessions · Source: GA4</p>
                <div class="perf-metrics">
                    <div><span>t.co / referral</span><strong>89,455 <small>Twitter links</small></strong></div>
                    <div><span>Direct</span><strong>724 <small>was 916</small></strong></div>
                    <div><span>Google organic</span><strong>88 <small>was 67</small></strong></div>
                    <div><span>Bing organic</span><strong>16 <small>was 6</small></strong></div>
                    <div><span>Key events tracked</span><strong>0</strong></div>
                </div>
            </div>
        </div>
    </section>

    <section class="perf-section" id="facebook">
        <p class="perf-kicker">Facebook &amp; Instagram · Meta</p>
        <h2>Twelve posts, one commercial plan out in front</h2>
        <p class="perf-lead">From Oct 19 to Nov 14, Core Four published <strong>12 Facebook posts</strong> with <strong>1,122 views</strong>, <strong>608 reach</strong>, and <strong>29 clicks</strong>. That is below the prior window’s 1,608 views and 58 clicks. The Oct 30 post — a commercial roof treated as a long-term property asset — did the heavy lifting.</p>
        <div class="perf-compare">
            <div><span>Post views</span><b>1,122</b><em>12 photo posts · Oct 19 – Nov 14</em></div>
            <div><span>Reach</span><b>608</b><em>Lifetime reach on those posts</em></div>
            <div><span>Clicks on posts</span><b>29</b><em>10 of them on the Oct 30 post</em></div>
        </div>
        <div class="perf-callout">
            <strong>Why this matters:</strong>
            The page kept a photo cadence of about every other day. The Oct 30 commercial-planning post earned 347 views. The next post, on Oct 25, earned 112.
        </div>
        <div class="perf-split">
            <div class="panel">
                <h3>Views by post</h3>
                <p class="perf-sub">Oct 19 – Nov 14 · Lifetime views · Source: Meta post export</p>
                <div class="perf-chart tall"><canvas id="fbViewsChart"></canvas></div>
            </div>
            <div class="panel">
                <h3>Latest window snapshot</h3>
                <p class="perf-sub">Facebook posts published Oct 19 – Nov 14</p>
                <div class="perf-metrics">
                    <div><span>Posts published</span><strong>12</strong></div>
                    <div><span>Post reach</span><strong>608</strong></div>
                    <div><span>Reactions</span><strong>26</strong></div>
                    <div><span>Comments</span><strong>3</strong></div>
                    <div><span>Shares</span><strong>2</strong></div>
                </div>
            </div>
        </div>
        <div class="perf-split">
            <div class="panel">
                <h3>Engagement mix</h3>
                <p class="perf-sub">Oct 19 – Nov 14 Facebook posts</p>
                <div class="perf-metrics">
                    <div><span>Reactions</span><strong>26</strong></div>
                    <div><span>Comments</span><strong>3</strong></div>
                    <div><span>Shares</span><strong>2</strong></div>
                    <div><span>Link / photo clicks</span><strong>29</strong></div>
                    <div><span>Prior window post views</span><strong>1,608 <small>Sep 21 – Oct 18</small></strong></div>
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
                    The Oct 30 commercial-planning post is the standout — 347 views and 10 clicks. The Oct 25 repair post was next, at 112 views.
                </div>
            </div>
        </div>
        <div class="perf-split">
            <div class="panel">
                <h3>Instagram · same window</h3>
                <p class="perf-sub">Oct 19 – Nov 14 · 12 posts · Source: Meta IG insights</p>
                <div class="perf-chart"><canvas id="igReachChart"></canvas></div>
            </div>
            <div class="panel">
                <h3>Instagram snapshot</h3>
                <p class="perf-sub">Cross-posted project content picking up a second audience</p>
                <div class="perf-metrics">
                    <div><span>IG views</span><strong>529</strong></div>
                    <div><span>IG reach</span><strong>345</strong></div>
                    <div><span>Likes</span><strong>25</strong></div>
                    <div><span>Comments</span><strong>4</strong></div>
                    <div><span>Top IG post</span><strong>199 <small>Oct 30 commercial plan</small></strong></div>
                </div>
            </div>
        </div>
        <div class="perf-stories">
            <article>
                <h3>YTD awareness</h3>
                <p>Through Sep 21 the page total stood at <strong>35.1K</strong> views. This export is lifetime views on the 12 new posts, <strong>1,122</strong>, not a new page-wide year-to-date.</p>
            </article>
            <article>
                <h3>Clicks, not just likes</h3>
                <p><strong>29 clicks</strong> still beat the 26 reactions. Ten of those clicks sat on the Oct 30 post.</p>
            </article>
            <article>
                <h3>Photos still win</h3>
                <p>All 12 Facebook posts were photos. The matching Instagram posts added 529 views, led by the same Oct 30 post at 199. Veterans Day was next on Instagram, at 125.</p>
            </article>
        </div>
    </section>

    <section class="perf-section" id="search">
        <p class="perf-kicker">Google Search Console</p>
        <h2>Clicks up. Impressions slipped. The homepage still takes the click.</h2>
        <p class="perf-lead">Final Search Console data runs Oct 19 – Nov 14, one day short of yesterday. It shows <strong>62 clicks, up 35%</strong> from 46, and <strong>9,097 impressions, down 4%</strong> from 9,491. That is about <strong>337 impressions a day</strong> and <strong>2.3 clicks a day</strong>. <em>core four roofing</em> accounted for <strong>34</strong> of the clicks, up from 24.</p>
        <div class="perf-compare">
            <div><span>Impressions</span><b>9,097</b><em>↓ 4% vs Sep 21 – Oct 18</em></div>
            <div><span>Clicks</span><b>62</b><em>↑ 35% · about 2.3 a day</em></div>
            <div><span>Homepage clicks</span><b>54</b><em>46 on the main URL · 5 on www · 3 on http www</em></div>
        </div>
        <div class="perf-tabs" role="tablist" aria-label="Search charts">
            <button class="perf-tab active" type="button" data-gsc="position">Avg. position</button>
            <button class="perf-tab" type="button" data-gsc="ctr">CTR</button>
            <button class="perf-tab" type="button" data-gsc="clicks">Clicks</button>
            <button class="perf-tab" type="button" data-gsc="impressions">Impressions</button>
        </div>
        <div class="panel">
            <h3 id="gscTitle">Daily average position</h3>
            <p class="perf-sub" id="gscSub">Oct 19 – Nov 14, 2026 · final data · Source: Google Search Console</p>
            <div class="perf-chart tall"><canvas id="gscChart"></canvas></div>
        </div>
        <div class="perf-split">
            <div class="panel">
                <h3>Latest window snapshot</h3>
                <p class="perf-sub">Oct 19 – Nov 14 · domain property · final data</p>
                <div class="perf-metrics">
                    <div><span>Clicks</span><strong>62 <small>↑ 35%</small></strong></div>
                    <div><span>Impressions</span><strong>9,097 <small>↓ 4%</small></strong></div>
                    <div><span>Brand query clicks</span><strong>34 <small>↑ 42%</small></strong></div>
                    <div><span>Image search</span><strong>0 clicks <small>78 impressions</small></strong></div>
                    <div><span>Prior window clicks</span><strong>46 <small>Sep 21 – Oct 18</small></strong></div>
                </div>
            </div>
            <div class="panel">
                <h3>Where the clicks came from</h3>
                <p class="perf-sub">Clicks by country · Oct 19 – Nov 14 · 62 clicks</p>
                <div class="perf-chart"><canvas id="deviceChart"></canvas></div>
            </div>
        </div>
    </section>

    <section class="perf-section" id="keywords">
        <p class="perf-kicker">Queries &amp; pages</p>
        <h2>The brand query, then the homepage</h2>
        <p class="perf-lead">Search Console attached 43 of the 62 clicks to a query. <em>core four roofing</em> led with 34. The homepage addresses took 54 clicks. Sugar Land, financing, service areas, and Conroe also recorded clicks. Two of the single clicks were job queries, not roofing searches.</p>
        <div class="perf-split">
            <div class="panel">
                <h3>Top search queries</h3>
                <p class="perf-sub">{{ $perf['meta']['window'] }} · Google Search Console</p>
                <table>
                    <tr><th>Query</th><th>Clicks</th><th>Vs Sep 21 – Oct 18</th></tr>
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
                    <tr><th>Page</th><th>Clicks</th><th>Vs Sep 21 – Oct 18</th></tr>
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

    <section class="perf-section" id="press">
        <p class="perf-kicker">Press</p>
        <h2>Seven articles this month name Core Four</h2>
        <p class="perf-lead">Two ran in the Texas Gazette on Oct 19. Five ran in Dock Line Magazine between Oct 2 and Oct 13. The Gazette labels its pieces as partner articles. Each link opens the story.</p>
        <div class="panel">
            <table>
                <tr><th>Date</th><th>Publication</th><th>Article</th><th></th></tr>
                @foreach($perf['press'] as $story)
                    <tr>
                        <td>{{ $story['date'] }}</td>
                        <td>{{ $story['publication'] }}</td>
                        <td>{{ $story['title'] }}</td>
                        <td><a href="{{ $story['url'] }}" target="_blank" rel="noopener">Read</a></td>
                    </tr>
                @endforeach
            </table>
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
      { label: 'Oct 19 – Nov 15', data: data.ga.current, borderColor: brand, backgroundColor: 'rgba(191,232,102,0.22)', fill: true, tension: 0.3, pointRadius: 2.5, borderWidth: 2.5 }
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
  data: { labels: data.sources.labels, datasets: [{ data: data.sources.data, backgroundColor: [brand, brandMid, lime, '#45664f', '#8aa890', '#dbd7cf'], borderWidth: 0 }] },
  options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true, font: { size: 11 } } } }, cutout: '58%' }
});

let gscChart;
const gscMeta = {
  position: { title: 'Daily average position', sub: 'Oct 19 – Nov 14, 2026 · final data · Lower is better', y: 'Avg. position', reverse: true, color: brandMid, key: 'position' },
  ctr: { title: 'Daily click-through rate', sub: 'Oct 19 – Nov 14, 2026 · final data', y: 'CTR (%)', reverse: false, color: brand, key: 'ctr' },
  clicks: { title: 'Daily clicks', sub: 'Oct 19 – Nov 14, 2026 · final data', y: 'Clicks', reverse: false, color: brand, key: 'clicks' },
  impressions: { title: 'Daily impressions', sub: 'Oct 19 – Nov 14, 2026 · final data', y: 'Impressions', reverse: false, color: muted, key: 'impressions' }
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
  data: { labels: data.countries.labels, datasets: [{ data: data.countries.data, backgroundColor: [brand, brandMid, lime, '#45664f', '#8aa890', '#dbd7cf'], borderWidth: 0 }] },
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
