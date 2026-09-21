<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Core Four Roofing — Residential Ads Plan 2026</title>
    <style>
        @page { margin: 28px 32px 36px; }
        body { font-family: DejaVu Sans, sans-serif; color: #144b24; font-size: 11px; line-height: 1.45; }
        h1 { font-size: 22px; margin: 0 0 4px; }
        h2 { font-size: 16px; margin: 0 0 6px; page-break-after: avoid; }
        h3 { font-size: 12px; margin: 0 0 6px; }
        p { margin: 0 0 8px; color: #45664f; }
        .kicker { font-size: 9px; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: #269a47; margin: 0 0 4px; }
        .cover { background: #0f2418; color: #fff; padding: 22px 20px; margin-bottom: 16px; }
        .cover h1, .cover .lime { color: #bfe866; }
        .cover h1 { color: #fff; }
        .cover p { color: #d7e4d4; margin: 0; }
        .meta { color: #bfe866; font-size: 10px; margin-bottom: 10px; }
        .section { margin: 0 0 16px; page-break-inside: avoid; }
        .cards { width: 100%; border-collapse: collapse; margin: 8px 0 10px; }
        .cards td { width: 25%; background: #f7f6f2; border: 1px solid #dbd7cf; padding: 8px 9px; vertical-align: top; }
        .cards .k { font-size: 8px; text-transform: uppercase; letter-spacing: 0.06em; color: #3d6b4a; }
        .cards .v { font-size: 16px; font-weight: 700; color: #0f2418; line-height: 1.15; padding: 3px 0; }
        .cards .d { font-size: 9px; color: #144b24; font-weight: 700; }
        .callout { background: #0f2418; color: #fff; padding: 10px 12px; margin: 8px 0 10px; border-left: 4px solid #bfe866; }
        table.data { width: 100%; border-collapse: collapse; margin: 6px 0 10px; }
        table.data th { text-align: left; font-size: 8px; text-transform: uppercase; letter-spacing: 0.08em; color: #3d6b4a; border-bottom: 1px solid #dbd7cf; padding: 5px 4px; }
        table.data td { border-bottom: 1px solid #ece8e0; padding: 5px 4px; }
        .box { border: 1px solid #dbd7cf; padding: 8px 10px; background: #fff; margin-bottom: 8px; }
        .split { width: 100%; border-collapse: collapse; }
        .split > td { width: 50%; vertical-align: top; padding: 0 8px 0 0; }
        .split > td + td { padding: 0 0 0 8px; }
        .chip { display: inline-block; padding: 2px 7px; margin: 0 3px 4px 0; border: 1px solid #dbd7cf; background: #f3f0e9; font-size: 9px; }
        .foot { margin-top: 12px; padding-top: 8px; border-top: 1px solid #dbd7cf; font-size: 8px; color: #3d6b4a; }
        .page-break { page-break-before: always; }
        ul { margin: 0 0 8px 16px; color: #45664f; }
        li { margin-bottom: 3px; }
    </style>
</head>
<body>
    <div class="cover">
        <div class="meta">Confidential · Prepared {{ $ads['meta']['prepared'] }}</div>
        <h1>Residential ads plan <span class="lime">2026</span></h1>
        <p>Four homeowner campaigns: tile, stone-coated steel, metal, and slate. Houston suburb ring only. Generic shingle and commercial stay off.</p>
    </div>

    <div class="section">
        <div class="kicker">The four campaigns</div>
        <h2>Specialty materials. Homeowners only.</h2>
        @foreach($ads['campaigns'] as $campaign)
            <div class="box">
                <h3>{{ $campaign['name'] }} · {{ $campaign['share'] }} of Search</h3>
                <p>{{ $campaign['why'] }}</p>
                <p><strong>Volume:</strong> {{ $campaign['volume'] }} · <strong>Ticket:</strong> {{ $campaign['ticket'] }}</p>
                <p><strong>Where:</strong> {{ $campaign['where'] }}</p>
                <p><strong>Queries:</strong> {{ implode(' · ', $campaign['queries']) }}</p>
                <p><strong>Landing:</strong> {{ $campaign['landing'] }} — {{ $campaign['landing_note'] }}</p>
            </div>
        @endforeach
        <div class="callout"><strong>Why not “roofers near me”:</strong> That auction is cheap-shingle lead-gen. These four materials raise qualification and ticket. Click volume is lower on purpose.</div>
    </div>

    <div class="section page-break">
        <div class="kicker">Budgets</div>
        <h2>Modeled $2k / $5k / $10k</h2>
        <p>Conservative month-3+ run rate. CPC ~$18 · 7.5% click to lead · 80% qualified · 20% close · $22k average job.</p>
        <table class="cards">
            <tr>
                @foreach($ads['tiers'] as $tier)
                    <td>
                        <div class="k">{{ $tier['label'] }}{{ !empty($tier['featured']) ? ' · recommended' : '' }}</div>
                        <div class="v">{{ $tier['amount'] }}</div>
                        <div class="d">{{ $tier['leads'] }} leads/mo · {{ $tier['jobs_year'] }} jobs/yr</div>
                        <p style="margin-top:6px;font-size:9px">{{ $tier['on'] }}</p>
                    </td>
                @endforeach
            </tr>
        </table>
        <table class="data">
            <tr>
                <th>Metric</th>
                @foreach($ads['tiers'] as $tier)<th>{{ $tier['amount'] }}</th>@endforeach
            </tr>
            <tr><td>Clicks / month</td>@foreach($ads['tiers'] as $tier)<td>{{ $tier['clicks'] }}</td>@endforeach</tr>
            <tr><td>Leads / month</td>@foreach($ads['tiers'] as $tier)<td>{{ $tier['leads'] }}</td>@endforeach</tr>
            <tr><td>Jobs / year</td>@foreach($ads['tiers'] as $tier)<td>{{ $tier['jobs_year'] }}</td>@endforeach</tr>
            <tr><td>Media / job</td>@foreach($ads['tiers'] as $tier)<td>{{ $tier['media_job'] }}</td>@endforeach</tr>
            <tr><td>Revenue / year</td>@foreach($ads['tiers'] as $tier)<td>{{ $tier['revenue'] }}</td>@endforeach</tr>
        </table>
    </div>

    <div class="section">
        <div class="kicker">Markets &amp; build</div>
        <h2>Suburb homeowners. Tile-heavy zips first.</h2>
        <table class="split">
            <tr>
                <td>
                    <div class="box">
                        <h3>Geos</h3>
                        <p><strong>$2k:</strong> North ring — Metal + Tile only.</p>
                        <p><strong>$5k:</strong> North + west (Katy / Fulshear / Sugar Land) — all four campaigns.</p>
                        <p><strong>$10k:</strong> Full suburb ring + CTV. Cut south/east first if CPL slips.</p>
                    </div>
                </td>
                <td>
                    <div class="box">
                        <h3>Search split at $5k</h3>
                        <p>Metal 40% · Tile 30% · Stone-coated steel 20% · Slate 10%.</p>
                        <p>Phrase + exact. No broad until 50+ conversions on that material. Brand campaign 5–8%.</p>
                    </div>
                </td>
            </tr>
        </table>
        <div class="box">
            <h3>Negatives from day one</h3>
            <p>
                @foreach($ads['negatives'] as $neg)
                    <span class="chip">- {{ $neg }}</span>
                @endforeach
            </p>
        </div>
        <div class="box">
            <h3>Do not</h3>
            <ul>
                @foreach($ads['dont'] as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="section">
        <div class="kicker">90 days</div>
        <h2>How we turn it on</h2>
        <p><strong>1–14:</strong> Tracking, negatives, brand, Metal + Tile RSAs, north-ring geos. Draft tile and slate pages.</p>
        <p><strong>15–45:</strong> Metal + Tile only. Expect CPL +25%. Weekly search-term review.</p>
        <p><strong>46–75:</strong> Add SCS, Slate, west tile belt, and Meta (tile-repair story + metal/SCS photos).</p>
        <p><strong>76–90:</strong> Scale toward $10k only if specialty CPL is under ~$400 and follow-up is tight.</p>
        <div class="callout"><strong>Best campaign in one line:</strong> $5,000/month, 70% Search split Metal 40 / Tile 30 / SCS 20 / Slate 10 in the north and west suburb ring, 10% remarketing, 20% Meta, homeowners only, generic shingle and commercial off — tracking live before spend.</div>
    </div>

    <div class="foot">
        Core Four Roofing · Confidential residential ads plan · Prepared {{ $ads['meta']['prepared'] }}<br>
        Modeled outcomes · not historical ROAS · Specialty materials · Houston suburb homeowners
    </div>
</body>
</html>
