const fs = require('fs');
const path = require('path');

// Chrome is driven through puppeteer-core so we never download a second copy.
const CHROME =
  process.env.CHROME_PATH ||
  '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome';

const OUTPUT = path.join(__dirname, 'output');
const PAGES = JSON.parse(fs.readFileSync(path.join(__dirname, 'pages.json'), 'utf8'));

// Widths the site was rebuilt against: desktop, the menu-collapse point, phone.
const WIDTHS = [1440, 1100, 390];

const LAUNCH = {
  executablePath: CHROME,
  headless: 'new',
  args: ['--no-sandbox', '--hide-scrollbars', '--force-device-scale-factor=1'],
};

/** Resolve a page argument to its {live, prev} pair, accepting a slug or a raw URL. */
function resolve(target) {
  if (PAGES[target]) return PAGES[target];
  if (/^https?:\/\//.test(target)) return { live: target, prev: target };
  throw new Error(`Unknown page "${target}". Known slugs:\n  ${Object.keys(PAGES).join('\n  ')}`);
}

fs.mkdirSync(OUTPUT, { recursive: true });

module.exports = { CHROME, OUTPUT, PAGES, WIDTHS, LAUNCH, resolve };
