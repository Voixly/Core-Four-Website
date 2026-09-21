/**
 * Renders every page at every breakpoint and checks the shared components
 * still hold together: slider arrows clear of the review text, chip labels
 * hugging their own text, and accordion answers indented under their title in
 * the same type as it, in a colour you can read off the card.
 *
 *   node components.js            # all pages
 *   node components.js index      # one page
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

  // Accordion answers. Open each one so the closed state cannot hide a fault.
  const luminance = (rgb) => {
    const [r, g, b] = rgb.match(/[\d.]+/g).slice(0, 3).map((n) => {
      const c = n / 255;
      return c <= 0.03928 ? c / 12.92 : ((c + 0.055) / 1.055) ** 2.4;
    });
    return 0.2126 * r + 0.7152 * g + 0.0722 * b;
  };
  const contrast = (a, b) => {
    const [x, y] = [luminance(a), luminance(b)].sort((m, n) => n - m);
    return (x + 0.05) / (y + 0.05);
  };

  for (const item of document.querySelectorAll('.faq details')) {
    const wasOpen = item.open;
    item.open = true;
    const summary = item.querySelector('summary');
    const answer = item.querySelector(':scope > p, :scope > .faq-body');
    const label = (summary?.textContent || '').replace(/\s+/g, ' ').trim().slice(0, 34);
    if (!answer) {
      hits.push({ kind: 'accordion has no answer', text: label, by: 0 });
      item.open = wasOpen;
      continue;
    }
    const acs = getComputedStyle(answer);
    const scs = getComputedStyle(summary);
    const ib = boxes(item);
    const ab = boxes(answer);

    // The answer lines up under the title, not against the edge of the card.
    const indent = parseFloat(acs.paddingLeft);
    if (indent < 1) hits.push({ kind: 'answer not indented', text: label, by: Math.round(indent) });
    if (parseFloat(acs.paddingBottom) < 1) {
      hits.push({ kind: 'answer has no room below it', text: label, by: 0 });
    }
    // An answer set larger than its own title reads as a mistake.
    const dfs = parseFloat(acs.fontSize) - parseFloat(scs.fontSize);
    if (Math.abs(dfs) > 0.5) {
      hits.push({ kind: 'answer type differs from title', text: label, by: Math.round(dfs * 10) / 10 });
    }
    if (ab.b > ib.b + 0.5 || ab.r > ib.r + 0.5) {
      hits.push({ kind: 'answer spills out of its card', text: label, by: Math.round(ab.b - ib.b) });
    }
    const ratio = contrast(acs.color, getComputedStyle(item).backgroundColor);
    if (ratio < 4.5) {
      hits.push({ kind: 'answer unreadable on its card', text: label, by: Math.round(ratio * 10) / 10 });
    }
    const titleRatio = contrast(scs.color, getComputedStyle(item).backgroundColor);
    if (titleRatio < 4.5) {
      hits.push({ kind: 'title unreadable on its card', text: label, by: Math.round(titleRatio * 10) / 10 });
    }
    item.open = wasOpen;
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
  console.log(bad ? `\n${bad} page(s) with faults` : `\nClean on ${pages.length} pages x ${WIDTHS.length} widths`);
  process.exit(bad ? 1 : 0);
})();
