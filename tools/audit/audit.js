/**
 * Full layout audit: renders every page on the live site and in the local
 * preview at each breakpoint, then reports text that lands in the wrong place
 * and pages whose total height drifts.
 *
 * Usage:  node audit.js [slug ...]
 */
const fs = require('fs');
const path = require('path');
const puppeteer = require('puppeteer-core');
const { OUTPUT, PAGES, WIDTHS, LAUNCH } = require('./config');

// Tolerance before a matched text node counts as misplaced, in CSS pixels.
const X_TOLERANCE = 24;

// One section rendering taller shifts everything below it, so absolute Y says
// little. Flag a node only when its vertical offset from live changes relative
// to the node before it, which points at the section that introduced the drift.
const DRIFT_TOLERANCE = 40;

// Third-party embeds are domain-locked to the live site, so they render there
// and not in the local preview. Their text is not ours to match.
const THIRD_PARTY = [
  /keyboard shortcuts/i,
  /^terms$/i,
  /^map data/i,
  /report a map error/i,
  /^\u2605+/,
  /powered by elfsight/i,
  /google reviews/i,
];

const isThirdParty = (t) => THIRD_PARTY.some((re) => re.test(t.trim()));

// Collect every visible text-bearing leaf element with its box, keyed by text.
const probe = () => {
  const out = [];
  const walk = (el) => {
    const own = Array.from(el.childNodes)
      .filter((n) => n.nodeType === 3)
      .map((n) => n.textContent.replace(/\s+/g, ' ').trim())
      .join(' ')
      .trim();
    if (own.length > 3) {
      const r = el.getBoundingClientRect();
      const cs = getComputedStyle(el);
      if (r.width > 0 && r.height > 0 && cs.visibility !== 'hidden' && cs.display !== 'none') {
        out.push({
          t: own.slice(0, 120),
          x: Math.round(r.x + window.scrollX),
          y: Math.round(r.y + window.scrollY),
          w: Math.round(r.width),
          h: Math.round(r.height),
          fs: Math.round(parseFloat(cs.fontSize)),
          fw: cs.fontWeight,
          ta: cs.textAlign,
          color: cs.color,
        });
      }
    }
    Array.from(el.children).forEach(walk);
  };
  walk(document.body);

  const imgs = Array.from(document.images)
    .filter((i) => i.getBoundingClientRect().width > 40)
    .map((i) => {
      const r = i.getBoundingClientRect();
      return {
        src: (i.currentSrc || i.src).split('/').pop().split('?')[0],
        x: Math.round(r.x + window.scrollX),
        y: Math.round(r.y + window.scrollY),
        w: Math.round(r.width),
        h: Math.round(r.height),
        radius: getComputedStyle(i).borderRadius,
      };
    });

  return { nodes: out, imgs, docHeight: document.documentElement.scrollHeight };
};

/** Load a page, settle lazy content by scrolling to the bottom, then measure. */
async function capture(browser, url, width) {
  const page = await browser.newPage();
  await page.setViewport({ width, height: 900 });
  try {
    await page.goto(url, { waitUntil: 'networkidle2', timeout: 60000 });
    await page.evaluate(() => window.scrollTo(0, document.body.scrollHeight));
    await new Promise((r) => setTimeout(r, 1200));
    await page.evaluate(() => window.scrollTo(0, 0));
    return await page.evaluate(probe);
  } catch (e) {
    return { error: String(e).slice(0, 160) };
  } finally {
    await page.close();
  }
}

/** Compare one live/preview pair, matching text nodes by their content. */
function compare(live, prev) {
  if (live.error || prev.error) return { error: live.error || prev.error };

  const index = new Map();
  for (const n of prev.nodes) {
    if (!index.has(n.t)) index.set(n.t, []);
    index.get(n.t).push(n);
  }

  let matched = 0;
  let prevDrift = null;
  const misplaced = [];
  for (const l of live.nodes) {
    if (isThirdParty(l.t)) continue;
    const candidates = index.get(l.t);
    if (!candidates || !candidates.length) continue;
    const p = candidates.shift();
    matched += 1;

    const dx = Math.abs(l.x - p.x);
    const drift = l.y - p.y;
    const jump = prevDrift === null ? 0 : Math.abs(drift - prevDrift);
    prevDrift = drift;

    if (dx > X_TOLERANCE || jump > DRIFT_TOLERANCE || l.fs !== p.fs) {
      misplaced.push({
        t: l.t.slice(0, 60),
        dx,
        jump,
        drift,
        liveFs: l.fs,
        prevFs: p.fs,
      });
    }
  }

  const heightDelta = prev.docHeight
    ? Math.abs(live.docHeight - prev.docHeight) / live.docHeight
    : 1;

  return {
    liveNodes: live.nodes.length,
    matched,
    unmatched: live.nodes.length - matched,
    misplaced: misplaced.length,
    misplacedPct: matched ? +((misplaced.length / matched) * 100).toFixed(1) : 0,
    heightPct: +(heightDelta * 100).toFixed(1),
    worst: misplaced.sort((a, b) => b.jump + b.dx - (a.jump + a.dx)).slice(0, 5),
  };
}

(async () => {
  const only = process.argv.slice(2);
  const slugs = only.length ? only : Object.keys(PAGES);
  const browser = await puppeteer.launch(LAUNCH);
  const raw = {};
  const summary = {};

  for (const slug of slugs) {
    raw[slug] = {};
    summary[slug] = {};
    for (const width of WIDTHS) {
      const live = await capture(browser, PAGES[slug].live, width);
      const prev = await capture(browser, PAGES[slug].prev, width);
      raw[slug][width] = { live, prev };
      summary[slug][width] = compare(live, prev);
    }
    const row = WIDTHS.map((w) => {
      const s = summary[slug][w];
      return s.error ? `${w}:ERR` : `${w}: ${s.misplacedPct}% off, h${s.heightPct}%`;
    }).join('   ');
    console.log(`${slug.padEnd(52)} ${row}`);
  }

  fs.writeFileSync(path.join(OUTPUT, 'layout.json'), JSON.stringify(raw));
  fs.writeFileSync(path.join(OUTPUT, 'summary.json'), JSON.stringify(summary, null, 1));

  // Roll the per-page numbers up so regressions are visible at a glance.
  console.log('\n--- totals ---');
  for (const w of WIDTHS) {
    const rows = slugs.map((s) => summary[s][w]).filter((s) => !s.error);
    const mis = rows.reduce((a, s) => a + s.misplaced, 0);
    const mat = rows.reduce((a, s) => a + s.matched, 0);
    const heights = rows.map((s) => s.heightPct).sort((a, b) => a - b);
    const median = heights[Math.floor(heights.length / 2)] ?? 0;
    console.log(`${w}px: ${((mis / mat) * 100).toFixed(1)}% misplaced, median height delta ${median}%`);
  }

  await browser.close();
})();
