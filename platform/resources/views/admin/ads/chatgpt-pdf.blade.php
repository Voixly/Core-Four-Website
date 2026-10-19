<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Core Four Roofing — ChatGPT ads, $1,000</title>
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
        .section { margin: 0 0 12px; }
        h2, h3, .kicker { page-break-after: avoid; }
        .box, .callout, .cards { page-break-inside: avoid; }
        p, li, td, h3 { word-wrap: break-word; }
        .cards { width: 100%; border-collapse: collapse; margin: 8px 0 10px; }
        .cards td { width: 25%; background: #f7f6f2; border: 1px solid #dbd7cf; padding: 8px 9px; vertical-align: top; }
        .cards .k { font-size: 8px; text-transform: uppercase; letter-spacing: 0.06em; color: #3d6b4a; }
        .cards .v { font-size: 16px; font-weight: 700; color: #0f2418; line-height: 1.15; padding: 3px 0; }
        .cards .d { font-size: 9px; color: #144b24; font-weight: 700; }
        .callout { background: #0f2418; color: #fff; padding: 10px 12px; margin: 8px 0 10px; border-left: 4px solid #bfe866; }
        .box { border: 1px solid #dbd7cf; padding: 8px 10px; background: #fff; margin-bottom: 8px; }
        .url { word-break: break-all; }
        .chip { display: inline-block; padding: 2px 7px; margin: 0 3px 4px 0; border: 1px solid #dbd7cf; background: #f3f0e9; font-size: 9px; }
        .foot { margin-top: 12px; padding-top: 8px; border-top: 1px solid #dbd7cf; font-size: 8px; color: #3d6b4a; }
        ul { margin: 0 0 8px 16px; color: #45664f; }
        li { margin-bottom: 3px; }
        .ad { margin: 0 0 6px; }
    </style>
</head>
<body>
    <div class="cover">
        <div class="meta">Confidential · Prepared {{ $ads['meta']['prepared'] }}</div>
        <h1>ChatGPT ads <span class="lime">$1,000</span></h1>
        <p>One residential campaign for Houston-suburb homeowners. Two ad groups. Daily budget $33. This does not replace the specialty search plan.</p>
    </div>

    <div class="section">
        <div class="kicker">Budget</div>
        <h2>Just over the $25 daily minimum</h2>
        <table class="cards">
            <tr>
                @foreach($ads['budget'] as $item)
                    <td><div class="k">{{ $item['label'] }}</div><div class="v">{{ $item['value'] }}</div><div class="d">{{ $item['note'] }}</div></td>
                @endforeach
            </tr>
        </table>
        <div class="callout"><strong>Traffic, not a lead forecast.</strong> {{ $ads['clicks'] }} Judge the test on cost per Lead created.</div>
    </div>

    <div class="section">
        <div class="kicker">Setup</div>
        <h2>Conversions on Lead created</h2>
        <p>Objective Conversions. Billing is valid clicks (oCPC). Max bid $4, inside OpenAI’s $3–$5 starting range. The pixel on the site already records lead_created when an inspection request is saved. If that event is not selectable yet, wait. Changing the objective later means a new campaign.</p>
        @foreach($ads['groups'] as $group)
            <div class="box">
                <h3>{{ $group['name'] }}</h3>
                <p>{{ $group['job'] }}</p>
                <p><strong>{{ $group['landing'] }}</strong><br><span class="url">{{ $group['utm'] }}</span></p>
                <ul>
                    @foreach($group['hints'] as $hint)
                        <li>{{ $hint }}</li>
                    @endforeach
                </ul>
                @foreach($group['ads'] as $ad)
                    <p class="ad"><strong>{{ $ad['title'] }}</strong><br>{{ $ad['copy'] }}</p>
                @endforeach
            </div>
        @endforeach
    </div>

    <div class="section">
        <div class="kicker">Places</div>
        <h2>Cities, not the whole country</h2>
        <p>
            @foreach($ads['locations'] as $city)
                <span class="chip">{{ $city }}</span>
            @endforeach
        </p>
        <p>If those cities are not in the location search, do not launch. Negative phrases, when the account has them:</p>
        <p>
            @foreach($ads['negatives'] as $phrase)
                <span class="chip">− {{ $phrase }}</span>
            @endforeach
        </p>
    </div>

    <div class="section">
        <div class="kicker">90 days</div>
        @foreach($ads['steps'] as $index => $step)
            <p><strong>{{ $index + 1 }}. {{ $step['title'] }}</strong> {{ $step['body'] }}</p>
        @endforeach
        <div class="callout"><strong>Stop rule:</strong> One booked house pays for many months of this test. No in-area homeowner lead after $1,000 means stop.</div>
    </div>

    <div class="foot">
        Core Four Roofing · Confidential ChatGPT ads plan · Prepared {{ $ads['meta']['prepared'] }}<br>
        OpenAI’s published auction rules · not a lead forecast · Residential · Houston suburbs
    </div>
</body>
</html>
