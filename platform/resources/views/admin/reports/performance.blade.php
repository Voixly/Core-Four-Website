@extends('layouts.admin')
@section('title', 'Performance report')
@section('content')
<div class="cards">
    <div class="stat"><span>GSC clicks (window)</span><b>60</b></div>
    <div class="stat"><span>Impressions</span><b>7,971</b></div>
    <div class="stat"><span>Brand CTR</span><b>28%</b></div>
    <div class="stat"><span>YTD clicks</span><b>270</b></div>
    <div class="stat"><span>GBP interactions</span><b>89 vs 76</b></div>
    <div class="stat"><span>Query clicks</span><b>40</b></div>
</div>
<div class="panel">
    <h3>Search Console traction</h3>
    <p>Clicks are moving in the right direction on branded and city queries. Landing-page clicks track the same queries — Houston / residential / insurance pages carry the weight.</p>
    <canvas id="gsc" height="110"></canvas>
</div>
<div class="panel">
    <h3>Top queries (clicks)</h3>
    <table>
        <tr><th>Query</th><th>Clicks</th><th>Impr</th></tr>
        <tr><td>core four roofing</td><td>18</td><td>64</td></tr>
        <tr><td>roofing tomball</td><td>6</td><td>210</td></tr>
        <tr><td>residential roofing houston</td><td>5</td><td>480</td></tr>
        <tr><td>roof replacement cypress</td><td>4</td><td>390</td></tr>
        <tr><td>the woodlands roofing</td><td>3</td><td>310</td></tr>
        <tr><td>insurance roof claim houston</td><td>4</td><td>520</td></tr>
    </table>
</div>
<div class="panel">
    <h3>Landing pages tied to those queries</h3>
    <table>
        <tr><th>Page</th><th>Clicks</th></tr>
        <tr><td>/</td><td>42</td></tr>
        <tr><td>/residential-roofing/</td><td>4</td></tr>
        <tr><td>/residential-roofing-in-tomball-tx/</td><td>3</td></tr>
        <tr><td>/residential-roofing-in-the-woodlands-tx/</td><td>3</td></tr>
        <tr><td>/insurance-claims/</td><td>2</td></tr>
    </table>
</div>
<div class="panel">
    <h3>Google Business Profile</h3>
    <p>Daily interactions climbed to 89 vs 76 prior (+17%). Keep review replies and photo cadence weekly.</p>
    <canvas id="gbp" height="110"></canvas>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
const green = '#144b24';
new Chart(document.getElementById('gsc'), {
  type: 'line',
  data: { labels: ['W1','W2','W3','W4','W5','W6'], datasets: [{ label: 'Clicks', data: [6,8,9,11,12,14], borderColor: green, tension: 0.3 }] },
  options: { plugins: { legend: { display: false } } }
});
new Chart(document.getElementById('gbp'), {
  type: 'bar',
  data: { labels: ['Prior','Current'], datasets: [{ data: [76, 89], backgroundColor: ['#bfe866', green] }] },
  options: { plugins: { legend: { display: false } } }
});
</script>
@endsection
