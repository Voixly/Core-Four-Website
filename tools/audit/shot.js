/**
 * Screenshot the live site and the preview for visual comparison. Captures the
 * viewport by default, the full page with --full, or a single element with
 * --selector.
 *
 * Usage:  node shot.js <slug> [--width 1440] [--full] [--selector ".site-footer"]
 * Writes: output/<slug>-<width>-live.png and -prev.png
 */
const path = require('path');
const puppeteer = require('puppeteer-core');
const { OUTPUT, LAUNCH, resolve } = require('./config');

function parseArgs(argv) {
  const opts = { width: 1440, full: false, selector: null };
  const rest = [];
  for (let i = 0; i < argv.length; i += 1) {
    if (argv[i] === '--full') opts.full = true;
    else if (argv[i] === '--width') opts.width = Number(argv[++i]);
    else if (argv[i] === '--selector') opts.selector = argv[++i];
    else rest.push(argv[i]);
  }
  return { opts, target: rest[0] };
}

(async () => {
  const { opts, target } = parseArgs(process.argv.slice(2));
  if (!target) {
    console.error('usage: node shot.js <slug|url> [--width 1440] [--full] [--selector sel]');
    process.exit(1);
  }
  const urls = resolve(target);
  const browser = await puppeteer.launch(LAUNCH);

  for (const which of ['live', 'prev']) {
    const page = await browser.newPage();
    await page.setViewport({ width: opts.width, height: 1000 });
    await page.goto(urls[which], { waitUntil: 'networkidle2', timeout: 60000 });
    // Scroll through once so lazy images and widgets paint before capture.
    await page.evaluate(() => window.scrollTo(0, document.body.scrollHeight));
    await new Promise((r) => setTimeout(r, 1500));
    await page.evaluate(() => window.scrollTo(0, 0));
    await new Promise((r) => setTimeout(r, 500));

    const file = path.join(OUTPUT, `${target}-${opts.width}-${which}.png`);
    if (opts.selector) {
      const el = await page.$(opts.selector);
      if (!el) {
        console.log(`  ${which}: selector "${opts.selector}" not found`);
        await page.close();
        continue;
      }
      await el.screenshot({ path: file });
    } else {
      await page.screenshot({ path: file, fullPage: opts.full });
    }
    console.log(`  wrote ${path.relative(process.cwd(), file)}`);
    await page.close();
  }

  await browser.close();
})();
