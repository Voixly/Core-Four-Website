/**
 * Measure the same CSS selector on the live site and in the preview, side by
 * side, at one or more widths. Replaces the one-off probes used while matching
 * the header, footer, blog cards and type scale.
 *
 * Usage:  node probe.js <slug> <selector> [width ...]
 * Example: node probe.js index ".site-footer" 1440 390
 */
const puppeteer = require('puppeteer-core');
const { WIDTHS, LAUNCH, resolve } = require('./config');

// Properties worth comparing when a box looks wrong.
const PROPS = [
  'display', 'position', 'fontSize', 'fontWeight', 'fontFamily', 'lineHeight',
  'color', 'backgroundColor', 'backgroundImage', 'backgroundSize',
  'backgroundPosition', 'backgroundRepeat', 'borderRadius', 'border',
  'padding', 'margin', 'gap', 'gridTemplateColumns', 'textAlign', 'opacity',
];

const measure = (selector, props) => {
  const els = Array.from(document.querySelectorAll(selector));
  return els.slice(0, 8).map((el) => {
    const r = el.getBoundingClientRect();
    const cs = getComputedStyle(el);
    const style = {};
    for (const p of props) style[p] = cs[p];
    return {
      box: {
        x: Math.round(r.x + window.scrollX),
        y: Math.round(r.y + window.scrollY),
        w: Math.round(r.width),
        h: Math.round(r.height),
      },
      text: (el.textContent || '').replace(/\s+/g, ' ').trim().slice(0, 70),
      style,
    };
  });
};

(async () => {
  const [target, selector, ...widthArgs] = process.argv.slice(2);
  if (!target || !selector) {
    console.error('usage: node probe.js <slug|url> <selector> [width ...]');
    process.exit(1);
  }
  const widths = widthArgs.length ? widthArgs.map(Number) : WIDTHS;
  const urls = resolve(target);
  const browser = await puppeteer.launch(LAUNCH);

  for (const width of widths) {
    const captured = {};
    for (const which of ['live', 'prev']) {
      const page = await browser.newPage();
      await page.setViewport({ width, height: 900 });
      await page.goto(urls[which], { waitUntil: 'networkidle2', timeout: 60000 });
      await new Promise((r) => setTimeout(r, 1200));
      captured[which] = await page.evaluate(measure, selector, PROPS);
      await page.close();
    }

    console.log(`\n===== ${width}px  ${selector}`);
    const count = Math.max(captured.live.length, captured.prev.length);
    if (!count) console.log('  (no match on either side)');

    for (let i = 0; i < count; i += 1) {
      const l = captured.live[i];
      const p = captured.prev[i];
      console.log(`\n  [${i}] ${(l || p).text}`);
      if (!l || !p) {
        console.log(`      only on ${l ? 'live' : 'preview'}`);
        continue;
      }
      const boxDiff = ['x', 'y', 'w', 'h']
        .filter((k) => Math.abs(l.box[k] - p.box[k]) > 1)
        .map((k) => `${k} live=${l.box[k]} prev=${p.box[k]}`);
      console.log(`      box  ${boxDiff.length ? boxDiff.join('  ') : 'match'}`);
      for (const prop of PROPS) {
        if (l.style[prop] !== p.style[prop]) {
          console.log(`      ${prop}\n         live: ${l.style[prop]}\n         prev: ${p.style[prop]}`);
        }
      }
    }
  }

  await browser.close();
})();
