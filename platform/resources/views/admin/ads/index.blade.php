@extends('layouts.admin')
@section('title', 'Ads')
@section('meta', 'Homeowners · specialty materials · Houston suburb ring')
@section('actions')
    <a class="btn" href="{{ route('admin.ads.pdf') }}"><i class="fas fa-file-arrow-down"></i> Download PDF</a>
@endsection

@section('content')
<div class="perf ads-plan">
    <div class="perf-hero">
        <p class="perf-kicker">Prepared {{ $ads['meta']['prepared'] }} · Residential only</p>
        <h2>Four homeowner campaigns. No cheap shingle auction.</h2>
        <p>Paid search buys tile, stone-coated steel, metal, and slate — repairs and replacements for homeowners in the Houston suburb ring. Generic “roof replacement” stays off. Commercial stays off. Austin and Dallas stay off.</p>
    </div>

    <nav class="perf-nav" aria-label="Ads plan sections">
        <a href="#campaigns">Campaigns</a>
        <a href="#why">Why this</a>
        <a href="#outcomes">Budgets</a>
        <a href="#mix">Channels</a>
        <a href="#geos">Markets</a>
        <a href="#build">Build</a>
        <a href="#landings">Landings</a>
        <a href="#launch">90 days</a>
    </nav>

    <section class="perf-section" id="campaigns">
        <p class="perf-kicker">The new focus</p>
        <h2>The four campaigns we actually run</h2>
        <p class="perf-lead">Each material is its own Search campaign so bids cannot leak. Metal funds volume. Tile is the Houston story. Stone-coated steel is the upgrade. Slate is the scarce, high-ticket pocket.</p>
        <div class="ads-campaigns">
            @foreach($ads['campaigns'] as $campaign)
                <article class="ads-campaign" id="campaign-{{ $campaign['slug'] }}">
                    <span>{{ $campaign['share'] }} of Search</span>
                    <h3>{{ $campaign['name'] }}</h3>
                    <p>{{ $campaign['why'] }}</p>
                    <div class="ads-campaign-meta">
                        <div><strong>Search volume</strong>{{ $campaign['volume'] }}</div>
                        <div><strong>Typical ticket</strong>{{ $campaign['ticket'] }}</div>
                        <div><strong>Where it lives</strong>{{ $campaign['where'] }}</div>
                    </div>
                    <div class="perf-metro-label">Core queries</div>
                    <div class="perf-chips">
                        @foreach($campaign['queries'] as $query)
                            <span>{{ $query }}</span>
                        @endforeach
                    </div>
                    <p class="ads-landing-note">
                        Landing: <a href="{{ url($campaign['landing']) }}" target="_blank" rel="noopener noreferrer">{{ $campaign['landing'] }}</a>
                        · {{ $campaign['landing_note'] }}
                    </p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="perf-section" id="why">
        <p class="perf-kicker">The recommendation</p>
        <h2>Buy the roofs other companies will not do well</h2>
        <p class="perf-lead">The old plan bought generic residential replacement. That auction is crowded with shingle shops and lead vendors. These four materials are what Core Four already photographs, posts, and installs — and they pay more per job.</p>
        <div class="perf-gains">
            <article class="perf-gain">
                <span>Best starting budget</span>
                <b>$5k</b>
                <em>All four campaigns funded</em>
                <small>Metal + Tile carry volume · SCS + Slate stay tight</small>
            </article>
            <article class="perf-gain">
                <span>Who we are buying</span>
                <b>Owners</b>
                <em>Homeowners only</em>
                <small>No PMs, no warehouses, no commercial TPO</small>
            </article>
            <article class="perf-gain">
                <span>Modeled ticket</span>
                <b>$22k</b>
                <em>Repairs pull down · replacements pull up</em>
                <small>Conservative blend vs $14k generic residential</small>
            </article>
            <article class="perf-gain">
                <span>One job covers</span>
                <b>2–7 mo</b>
                <em>Media, not overhead</em>
                <small>At the conservative $22k average</small>
            </article>
        </div>
        <div class="perf-callout">
            <strong>Why not “roofers near me”:</strong>
            That query fills the calendar with $8k shingle bids and people shopping three cheap quotes. Tile, slate, metal, and stone-coated steel homeowners already know what is on the house — or they want an upgrade. Qualification goes up. Average ticket goes up. Click volume goes down, which is why the model caps clicks instead of pretending $5k buys 185 generic clicks.
        </div>
        <div class="perf-stories">
            <article>
                <h3>Tile is the Houston moment</h3>
                <p>Concrete and clay tile from the 90s and 2000s is failing in The Woodlands, Katy, Fulshear, and Sugar Land. The Jul 30 “botched tile repair” post was the year’s best social proof. Run that story in Search and Meta.</p>
            </article>
            <article>
                <h3>SCS is the replacement pitch</h3>
                <p>When the tile is too heavy or too far gone, stone-coated steel is the “same look, less weight, hail-ready” close. Bid the brand names people actually type: Decra, Gerard, metal tile.</p>
            </article>
            <article>
                <h3>Slate stays exact</h3>
                <p>Do not chase slate volume. Phrase and exact only. One replacement can be $35k–$70k. If the ad group goes quiet, leave it quiet — do not broaden into synthetic-slate junk traffic.</p>
            </article>
        </div>
    </section>

    <section class="perf-section" id="outcomes">
        <p class="perf-kicker">Modeled results</p>
        <h2>What $2k, $5k, and $10k actually buy now</h2>
        <p class="perf-lead">Conservative, month-3+ run rate. Specialty search has less volume than generic roofing, so clicks are capped on purpose. The plan still works because a closed job is modeled at $22k, not $14k.</p>
        <div class="perf-chips" style="margin-bottom:1rem">
            <span>CPC ~$18 blended</span>
            <span>7.5% click → lead</span>
            <span>$240–$360 CPL</span>
            <span>80% qualified · 20% close</span>
            <span>$22k avg job</span>
            <span>First 30 days CPL +25%</span>
        </div>
        <div class="ads-tiers">
            @foreach($ads['tiers'] as $tier)
                <article class="ads-tier{{ !empty($tier['featured']) ? ' is-featured' : '' }}">
                    @if(!empty($tier['featured']))
                        <div class="ads-tier-badge">Recommended start</div>
                    @endif
                    <p class="perf-kicker">{{ $tier['label'] }}</p>
                    <h3>{{ $tier['amount'] }} <small>{{ $tier['period'] }}</small></h3>
                    <div class="ads-mixbar" aria-hidden="true">
                        @foreach($tier['mix'] as $seg)
                            <span class="ads-seg ads-seg-{{ \Illuminate\Support\Str::slug($seg['name']) }}" style="width: {{ $seg['share'] }}%"></span>
                        @endforeach
                    </div>
                    <div class="ads-mix-legend">
                        @foreach($tier['mix'] as $seg)
                            <span><i class="ads-seg ads-seg-{{ \Illuminate\Support\Str::slug($seg['name']) }}"></i>{{ $seg['name'] }} {{ $seg['dollars'] }}</span>
                        @endforeach
                    </div>
                    <div class="perf-metrics">
                        <div><span>On</span><strong>{{ $tier['on'] }}</strong></div>
                        <div><span>Off</span><strong>{{ $tier['off'] }}</strong></div>
                        <div><span>Clicks / month</span><strong>{{ $tier['clicks'] }}</strong></div>
                        <div><span>Leads / month</span><strong>{{ $tier['leads'] }}</strong></div>
                        <div><span>Qualified</span><strong>{{ $tier['qualified'] }}</strong></div>
                        <div><span>Jobs / 90 days</span><strong>{{ $tier['jobs_90'] }}</strong></div>
                        <div><span>Jobs / year</span><strong>{{ $tier['jobs_year'] }}</strong></div>
                        <div><span>Media cost / job</span><strong>{{ $tier['media_job'] }}</strong></div>
                        <div><span>Booked revenue / year</span><strong>{{ $tier['revenue'] }}</strong></div>
                    </div>
                    <p class="perf-sub" style="margin:1rem 0 0">{{ $tier['note'] }}</p>
                </article>
            @endforeach
        </div>
        <div class="panel" style="overflow-x:auto">
            <h3>Side-by-side (steady state)</h3>
            <p class="perf-sub">Media only · not management time · conversion tracking must be live or these numbers miss</p>
            <table>
                <tr>
                    <th>Metric</th>
                    @foreach($ads['tiers'] as $tier)
                        <th>{{ $tier['amount'] }} / mo</th>
                    @endforeach
                </tr>
                <tr>
                    <td>Annual media</td>
                    <td>$24,000</td>
                    <td>$60,000</td>
                    <td>$120,000</td>
                </tr>
                <tr>
                    <td>Search campaigns live</td>
                    <td>Metal + Tile</td>
                    <td>All four</td>
                    <td>All four + full ring</td>
                </tr>
                <tr>
                    <td>Leads / month</td>
                    <td>4–6</td>
                    <td>9–14</td>
                    <td>16–24</td>
                </tr>
                <tr>
                    <td>Closed jobs / year</td>
                    <td>6–10</td>
                    <td>14–22</td>
                    <td>22–34</td>
                </tr>
                <tr>
                    <td>Conservative revenue / year</td>
                    <td>$130k–$220k</td>
                    <td>$310k–$485k</td>
                    <td>$485k–$750k</td>
                </tr>
                <tr>
                    <td>Media-to-revenue</td>
                    <td>5.4–9.2×</td>
                    <td>5.2–8.1×</td>
                    <td>4.0–6.3×</td>
                </tr>
            </table>
        </div>
        <div class="perf-callout">
            <strong>Read the ranges as lumpy.</strong>
            A $6k tile leak repair and a $38k standing-seam replacement both count as “a job.” Storm weeks spike. Quiet weeks do not. Staff to yearly job counts — not one signed roof every 30 days.
        </div>
    </section>

    <section class="perf-section" id="mix">
        <p class="perf-kicker">Channel mix</p>
        <h2>Search first. Social only after Metal and Tile are fed.</h2>
        <p class="perf-lead">Homeowners still search the material on the house when a tile cracks or a metal seam weeps. Facebook does not replace “tile roof repair The Woodlands.” It retargets the people who already looked.</p>
        <div class="perf-split">
            <div class="panel">
                <h3>What each channel is for</h3>
                <div class="perf-metrics">
                    <div><span>Google Search</span><strong>65–90% · the four campaigns</strong></div>
                    <div><span>Remarketing / YouTube</span><strong>10% · 30–90 day visitors</strong></div>
                    <div><span>Meta</span><strong>$5k+ · tile story + metal/SCS photos</strong></div>
                    <div><span>CTV</span><strong>$10k only · suburb frequency, no last-click jobs</strong></div>
                    <div><span>Local Services Ads</span><strong>Additive if Google approves roofing</strong></div>
                </div>
            </div>
            <div class="panel">
                <h3>What we will not do</h3>
                <p class="perf-sub">These look busy and waste specialty budget</p>
                <ul class="ads-dont">
                    @foreach($ads['dont'] as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    <section class="perf-section" id="geos">
        <p class="perf-kicker">Where to spend</p>
        <h2>Suburb homeowners. Tile-heavy zips first.</h2>
        <p class="perf-lead">Presence targeting — people in or regularly in — not “interested in Houston.” Expand the ring only after north-ring Metal and Tile convert. Downtown and inside-the-Loop stay out.</p>
        <div class="perf-stories">
            <article>
                <h3>North ring — $2k map</h3>
                <p>Tomball HQ, The Woodlands, Cypress, Spring, Humble, Atascocita, Conroe. Shortest drive, strongest listing. Woodlands carries most of the tile and custom-slate demand in this ring.</p>
            </article>
            <article>
                <h3>West — add at $5k</h3>
                <p>Katy, Fulshear, Cinco Ranch, Richmond, Rosenberg, Sugar Land, Missouri City. This is the concrete- and clay-tile belt. SCS ads belong here as the replacement for failing tile.</p>
            </article>
            <article>
                <h3>South / east — add at $10k</h3>
                <p>Pearland, League City, Friendswood, Pasadena, Baytown, Dickinson. Last ring on. If budget has to flex, cut this ring before Tomball Metal or Woodlands Tile.</p>
            </article>
        </div>
        <div class="perf-compare">
            <div><span>$2k geo</span><b>North</b><em>Metal + Tile only</em></div>
            <div><span>$5k geo</span><b>N + W</b><em>All four campaigns</em></div>
            <div><span>$10k geo</span><b>Full ring</b><em>All four + suburb CTV</em></div>
        </div>
    </section>

    <section class="perf-section" id="build">
        <p class="perf-kicker">Account build</p>
        <h2>How the account is structured</h2>
        <p class="perf-lead">One Google Ads account. Residential only. Brand protected. Four material campaigns with their own budgets. Phrase and exact until 50+ conversions exist on that material.</p>
        <div class="perf-split">
            <div class="panel">
                <h3>Campaign architecture</h3>
                <p class="perf-sub">Bids cannot leak from Metal into Slate, or from Tile into generic roofing</p>
                <table>
                    <tr><th>Campaign</th><th>Role</th><th>Share</th></tr>
                    <tr><td>Search — Brand</td><td>Core Four Roofing + misspellings. Cheap, defensive.</td><td>5–8%</td></tr>
                    <tr><td>Search — Metal</td><td>Standing seam + residential metal repair/replace. Volume engine.</td><td>40% of Search</td></tr>
                    <tr><td>Search — Tile</td><td>Clay / concrete / Spanish tile repair and replacement.</td><td>30% of Search</td></tr>
                    <tr><td>Search — Stone-coated steel</td><td>Decra, Gerard, metal tile, replace-tile-with-metal.</td><td>20% of Search</td></tr>
                    <tr><td>Search — Slate</td><td>Exact/phrase only. Estate pockets. Do not broaden.</td><td>10% of Search</td></tr>
                    <tr><td>Remarketing</td><td>30–90 day visitors from those four landings.</td><td>10% of media</td></tr>
                    <tr><td>Meta</td><td>$5k+. Tile-fail story, metal/SCS job photos, same zips.</td><td>15–20%</td></tr>
                    <tr><td>CTV</td><td>$10k only. Brand + phone. Zero last-click jobs in the model.</td><td>10%</td></tr>
                </table>
            </div>
            <div class="panel">
                <h3>Ads, bids, negatives</h3>
                <div class="perf-metrics">
                    <div><span>RSA</span><strong>Name the material + the suburb</strong></div>
                    <div><span>Call</span><strong>(281) 541-0027 on every ad</strong></div>
                    <div><span>Sitelinks</span><strong>Metal · SCS · Repair · Financing</strong></div>
                    <div><span>Bid mods</span><strong>+20% evenings &amp; weekends</strong></div>
                    <div><span>Match</span><strong>Phrase + exact · no broad yet</strong></div>
                </div>
                <p class="perf-metro-label" style="margin-top:1rem">Negatives from day one</p>
                <div class="perf-chips">
                    @foreach($ads['negatives'] as $neg)
                        <span>− {{ $neg }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="perf-section" id="landings">
        <p class="perf-kicker">Destinations</p>
        <h2>Paid clicks go to the material page — never home</h2>
        <p class="perf-lead">Metal and stone-coated steel already have pages. Tile and slate need their own before those campaigns scale. Form and click-to-call stay above the fold. “Free estimate” is fine for homeowners.</p>
        <div class="perf-split">
            <div class="panel">
                <h3>Ready now</h3>
                <div class="perf-metrics">
                    <div>
                        <span>Metal</span>
                        <strong><a href="{{ url('/residential-roofing/metal-roofs/') }}" target="_blank" rel="noopener noreferrer">/residential-roofing/metal-roofs/</a></strong>
                    </div>
                    <div>
                        <span>Stone-coated steel</span>
                        <strong><a href="{{ url('/residential-roofing/stone-coated-steel/') }}" target="_blank" rel="noopener noreferrer">/residential-roofing/stone-coated-steel/</a></strong>
                    </div>
                </div>
            </div>
            <div class="panel">
                <h3>Build before scaling</h3>
                <div class="perf-metrics">
                    <div><span>Tile roofs</span><strong>/residential-roofing/tile-roofs/</strong></div>
                    <div><span>Slate roofs</span><strong>/residential-roofing/slate-roofs/</strong></div>
                </div>
                <p class="perf-sub" style="margin:0.9rem 0 0">Until those exist, Tile uses the repair page and Slate uses the residential hub — with material-specific RSA so the click still matches the query. Do not scale Tile or Slate past a test without the dedicated page.</p>
            </div>
        </div>
        <div class="perf-callout">
            <strong>Tracking still comes first.</strong>
            GA4 key events were still at zero on the last performance window. Wire form submits, tap-to-call, and a call-tracking number on these four landings, then import a sold job as an offline conversion. Without that, Metal will eat budget that should have gone to Tile.
        </div>
    </section>

    <section class="perf-section" id="launch">
        <p class="perf-kicker">First 90 days</p>
        <h2>How we turn it on</h2>
        <p class="perf-lead">Even if the long-term number is $10k, start with Metal + Tile in the north ring. Prove specialty CPL, then add SCS, Slate, west, and Meta. Opening four campaigns on day one is how $2k disappears.</p>
        <div class="ads-steps">
            <article>
                <div>1</div>
                <div>
                    <h3>Days 1–14 — plumbing</h3>
                    <p>Conversion tracking, call tracking, the negative list, brand campaign, Metal + Tile RSAs, north-ring geos. QA the metal and SCS pages. Draft tile and slate landings. Same-day callback rule on (281) 541-0027. Start LSA if Google approves roofing.</p>
                </div>
            </article>
            <article>
                <div>2</div>
                <div>
                    <h3>Days 15–45 — Metal + Tile only</h3>
                    <p>85–90% of spend here. Expect CPL ~25% worse than the model. Search-term report every week — anything shingle, cheap, commercial, or barn gets negated. Remarketing on after 100 visitors. No SCS, Slate, Katy, or Meta prospecting yet.</p>
                </div>
            </article>
            <article>
                <div>3</div>
                <div>
                    <h3>Days 46–75 — add SCS, Slate, west, Meta</h3>
                    <p>If north-ring CPL is in range, turn on stone-coated steel and slate (only if their pages — or tight RSAs — are ready), add the west tile belt, and start Meta with the tile-repair story plus metal/SCS photos. Import sold jobs.</p>
                </div>
            </article>
            <article>
                <div>4</div>
                <div>
                    <h3>Days 76–90 — scale or hold</h3>
                    <p>If leads are worked and specialty CPL is under ~$400, step toward $10k (south/east + CTV). If follow-up is slow or CPL is above $500, stay at $5k north + west. More budget will not fix a leaky sales process — and it will not create slate demand that is not there.</p>
                </div>
            </article>
        </div>
        <div class="perf-callout">
            <strong>Best campaign in one line:</strong>
            $5,000/month, 70% Google Search split Metal 40 / Tile 30 / SCS 20 / Slate 10 in the north and west suburb ring, 10% remarketing, 20% Meta using the tile-repair story, homeowners only, generic shingle and commercial keywords off — conversion tracking live before the first dollar spends.
        </div>
    </section>

    <p class="perf-foot">Core Four Roofing · Confidential residential ads plan · Prepared {{ $ads['meta']['prepared'] }}<br>Modeled outcomes · not historical ROAS · Specialty materials · Houston suburb homeowners</p>
</div>
@endsection
