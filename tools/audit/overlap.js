/**
 * Checks that the slider arrows and the chip labels never sit on top of the
 * content beside them, on every page and at every breakpoint.
 *
 *   node overlap.js            # all pages
 *   node overlap.js index      # one page
 */
const puppeteer = require('puppeteer-core');
const { PAGES, LAUNCH } = require('./config');

const WIDTHS = [1440, 1280, 1100, 1024, 900, 768, 680, 600, 480, 390, 360];

// An arrow overlaps if its box intersects the box of any text beside it.
const probe = () => {
  const hits = [];
  const boxes = (el) => {
    const r = el.getBoundingClientRect();
    return { l: r.left, r: r.right, t: r.top, b: r.bottom, w: r.width, h: r.height };
  };
  const overlaps = (a, b) =>
    a.l < b.r - 0.5 && b.l < a.r - 0.5 && a.t < b.b - 0.5 && b.t < a.b - 0.5;

  for (const slider of document.querySelectorAll('.review-slider')) {
    const navs = Array.from(slider.querySelectorAll('.review-nav')).map((n) => ({ el: n, box: boxes(n) }));
    const slide = slider.querySelector('.featured-review.is-active');
    if (!slide) continue;
    for (const node of slide.querySelectorAll('h3, p, img')) {
      const nb = boxes(node);
      if (nb.w < 2 || nb.h < 2) continue;
      for (const nav of navs) {
        if (!overlaps(nav.box, nb)) continue;
        hits.push({
          kind: 'arrow over ' + node.tagName.toLowerCase(),
          arrow: nav.el.className,
          by: Math.round(Math.min(nav.box.r, nb.r) - Math.max(nav.box.l, nb.l)),
          text: (node.textContent || node.getAttribute('alt') || '').trim().slice(0, 40),
        });
      }
    }
  }

  // Chips should hug their text and stay inside their card.
  for (const pill of document.querySelectorAll('.pill')) {
    const pb = boxes(pill);
    const card = pill.closest('.solution-card, .guarantee-card, .c4-section-label');
    if (!card) continue;
    const cb = boxes(card);
    if (pb.l < cb.l - 0.5 || pb.r > cb.r + 0.5) {
      hits.push({ kind: 'pill outside card', text: pill.textContent.trim(), by: Math.round(Math.max(cb.l - pb.l, pb.r - cb.r)) });
    }
    // A chip that has stretched to fill its column is a layout bug.
    if (pb.w > cb.w * 0.9 && cb.w > 200) {
      hits.push({ kind: 'pill stretched', text: pill.textContent.trim(), by: Math.round(pb.w) });
    }
  }
  return hits;
};

(async () => {
  const only = process.argv.slice(2);
  const pages = Object.keys(PAGES).filter((p) => !only.length || only.includes(p));

  const browser = await puppeteer.launch(LAUNCH);
  let bad = 0;
  for (const name of pages) {
    const url = PAGES[name].prev;
    const found = [];
    for (const width of WIDTHS) {
      const page = await browser.newPage();
      await page.setViewport({ width, height: 900 });
      await page.goto(url, { waitUntil: 'domcontentloaded', timeout: 30000 });
      await new Promise((r) => setTimeout(r, 120));
      for (const hit of await page.evaluate(probe)) found.push({ width, ...hit });
      await page.close();
    }
    if (found.length) {
      bad++;
      console.log(`\n${name}`);
      for (const f of found) console.log(`   ${f.width}px  ${f.kind}  by ${f.by}px  "${f.text}"`);
    }
  }
  await browser.close();
  console.log(bad ? `\n${bad} page(s) with overlaps` : `\nNo overlaps on ${pages.length} pages x ${WIDTHS.length} widths`);
  process.exit(bad ? 1 : 0);
})();
