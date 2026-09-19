@extends('layouts.admin')
@section('title', 'Residential ads plan')
@section('content')
<div class="panel">
    <p>Houston suburbs only. Conservative model: CPC ~$24, 6.5% CVR, $370–$450 CPL, 70% qualified, 18% close, $14k average job. Austin, Dallas, and downtown off.</p>
</div>
<div class="cards">
    <div class="stat"><span>$2k / mo · north ring</span><b>5–8 leads</b><p>7–11 jobs / year · 90/10 search/remarketing</p></div>
    <div class="stat"><span>$5k / mo · recommended</span><b>12–18 leads</b><p>16–24 jobs / year · 70/10/20 search/remarketing/Meta</p></div>
    <div class="stat"><span>$10k / mo · full ring + CTV</span><b>22–32 leads</b><p>28–40 jobs / year · 65/10/15/10</p></div>
</div>
<div class="panel">
    <h3>Where the dollars go</h3>
    <table>
        <tr><th></th><th>$2,000</th><th>$5,000</th><th>$10,000</th></tr>
        <tr><td>Google Search</td><td>90% north ring</td><td>70% north + west</td><td>65% full suburb ring</td></tr>
        <tr><td>Google remarketing</td><td>10%</td><td>10%</td><td>10%</td></tr>
        <tr><td>Meta</td><td>off</td><td>20%</td><td>15%</td></tr>
        <tr><td>CTV / streaming</td><td>off</td><td>off</td><td>10%</td></tr>
    </table>
    <p>North ring first: Tomball, The Woodlands, Cypress, Spring. Then Katy / Fulshear / Sugar Land. South/east only at $10k.</p>
</div>
<div class="panel">
    <h3>Rollout</h3>
    <ol>
        <li>Days 1–14 — conversion tracking live before spend.</li>
        <li>Days 15–45 — north ring search only. Expect CPL ~25% worse than model.</li>
        <li>Days 45–90 — add west + Meta at $5k if CPL is in range.</li>
        <li>After 90 — CTV only if follow-up is tight and CPL is under ~$450.</li>
    </ol>
    <p>Recommendation: <strong>$5,000/month</strong>, 70% Google Search to residential city pages, 10% remarketing, 20% Meta in those zips.</p>
</div>
@endsection
