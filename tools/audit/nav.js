/**
 * Exercises the header navigation the way a visitor does.
 *
 * Desktop: hovers a parent item, then walks the pointer down into the panel
 * and across to a child link, checking the panel is still there and clickable.
 * Mobile: opens the drawer and checks it slides in, traps focus, locks the
 * page, expands a submenu, and closes from the button, the scrim and Escape.
 *
 *   node nav.js [slug]
 */
const puppeteer = require('puppeteer-core');
const { PAGES, LAUNCH } = require('./config');

const pass = [];
const fail = [];
const check = (ok, label, detail = '') => (ok ? pass : fail).push(label + (detail ? `  (${detail})` : ''));

const box = (page, selector) =>
  page.$eval(selector, (el) => {
    const r = el.getBoundingClientRect();
    const cs = getComputedStyle(el);
    return {
      x: r.x, y: r.y, w: r.width, h: r.height,
      visible: cs.visibility === 'visible' && cs.display !== 'none' && parseFloat(cs.opacity) > 0.05,
      opacity: +cs.opacity,
    };
  });

async function desktop(browser, url) {
  const page = await browser.newPage();
  await page.setViewport({ width: 1440, height: 900 });
  await page.goto(url, { waitUntil: 'networkidle2' });

  const trigger = '.nav .has-sub > a';
  const panel = '.nav .has-sub .sub';
  const child = '.nav .has-sub .sub-inner a:last-child';

  check(!(await box(page, panel)).visible, 'panel starts hidden');

  const t = await box(page, trigger);
  await page.mouse.move(t.x + t.w / 2, t.y + t.h / 2, { steps: 8 });
  await new Promise((r) => setTimeout(r, 350));
  check((await box(page, panel)).visible, 'panel opens on hover');

  // Walk down through the gap and on to the last child, a pixel at a time,
  // failing the moment the panel disappears mid-journey.
  const c = await box(page, child);
  const from = { x: t.x + t.w / 2, y: t.y + t.h };
  const to = { x: c.x + c.w / 2, y: c.y + c.h / 2 };
  let lostAt = null;
  const STEPS = 30;
  for (let i = 1; i <= STEPS; i++) {
    const k = i / STEPS;
    await page.mouse.move(from.x + (to.x - from.x) * k, from.y + (to.y - from.y) * k);
    await new Promise((r) => setTimeout(r, 25));
    if (!(await box(page, panel)).visible && lostAt === null) {
      lostAt = Math.round(from.y + (to.y - from.y) * k);
    }
  }
  check(lostAt === null, 'panel survives the trip to a child link', lostAt ? `vanished at y=${lostAt}` : '');

  // And the link is genuinely clickable at the end of that trip.
  const href = await page.$eval(child, (el) => el.getAttribute('href'));
  await page.mouse.down();
  await page.mouse.up();
  await page.waitForNavigation({ waitUntil: 'domcontentloaded', timeout: 5000 }).catch(() => {});
  check(page.url().includes(href.replace(/^https?:\/\/[^/]+/, '').replace(/\/$/, '').split('/').pop() || 'x'),
    'child link navigates', page.url().split('/').pop());

  await page.close();
}

async function mobile(browser, url, width) {
  const page = await browser.newPage();
  await page.setViewport({ width, height: 780, isMobile: true, hasTouch: true });
  await page.goto(url, { waitUntil: 'networkidle2' });
  const tag = `${width}px`;

  const shut = await box(page, '.nav');
  check(!shut.visible && shut.x >= width - 1, `${tag} drawer parked off screen`, `x=${Math.round(shut.x)}`);
  check(await page.$eval('.menu-toggle', (el) => el.getAttribute('aria-expanded') === 'false'), `${tag} toggle starts collapsed`);

  await page.click('.menu-toggle');
  await new Promise((r) => setTimeout(r, 450));

  const open = await box(page, '.nav');
  check(open.visible && Math.round(open.x + open.w) === width, `${tag} drawer slides fully in`, `right edge ${Math.round(open.x + open.w)}`);
  check((await box(page, '.nav-scrim')).visible, `${tag} scrim covers the page`);
  check(await page.$eval('.menu-toggle', (el) => el.getAttribute('aria-expanded') === 'true'), `${tag} toggle reports expanded`);
  check(await page.evaluate(() => getComputedStyle(document.documentElement).overflow === 'hidden'), `${tag} page scroll locked`);
  check(await page.evaluate(() => document.activeElement.classList.contains('nav-close')), `${tag} focus moves into the drawer`);

  // The drawer must sit above the chat launcher, not under it.
  const order = await page.evaluate(() => {
    const n = document.querySelector('.nav').getBoundingClientRect();
    const hit = document.elementFromPoint(n.x + n.width / 2, n.y + n.height - 40);
    return hit ? hit.closest('#site-nav') !== null : false;
  });
  check(order, `${tag} drawer is the topmost layer`);

  // Submenu accordion.
  const before = await box(page, '.nav .has-sub .sub');
  await page.click('.nav .sub-toggle');
  await new Promise((r) => setTimeout(r, 400));
  const after = await box(page, '.nav .has-sub .sub');
  check(before.h < 4 && after.h > 100, `${tag} submenu expands`, `${Math.round(before.h)} -> ${Math.round(after.h)}px`);
  check(await page.$eval('.nav .sub-toggle', (el) => el.getAttribute('aria-expanded') === 'true'), `${tag} caret reports expanded`);
  check(await page.$eval('.nav', (el) => el.scrollWidth <= el.clientWidth + 1), `${tag} no sideways overflow in the drawer`);

  await page.click('.nav .sub-toggle');
  await new Promise((r) => setTimeout(r, 400));
  check((await box(page, '.nav .has-sub .sub')).h < 4, `${tag} submenu collapses again`);

  // Close: button, then scrim, then Escape.
  await page.click('.nav-close');
  await new Promise((r) => setTimeout(r, 450));
  check(!(await box(page, '.nav')).visible, `${tag} close button dismisses`);
  check(await page.evaluate(() => !document.documentElement.classList.contains('nav-open')), `${tag} scroll lock released`);

  await page.click('.menu-toggle');
  await new Promise((r) => setTimeout(r, 450));
  await page.mouse.click(12, 400);
  await new Promise((r) => setTimeout(r, 450));
  check(!(await box(page, '.nav')).visible, `${tag} tapping the scrim dismisses`);

  await page.click('.menu-toggle');
  await new Promise((r) => setTimeout(r, 450));
  await page.keyboard.press('Escape');
  await new Promise((r) => setTimeout(r, 450));
  check(!(await box(page, '.nav')).visible, `${tag} Escape dismisses`);
  check(await page.evaluate(() => document.activeElement.classList.contains('menu-toggle')), `${tag} focus returns to the toggle`);

  await page.close();
}

(async () => {
  const slug = process.argv[2] || 'index';
  const url = PAGES[slug].prev;
  const browser = await puppeteer.launch(LAUNCH);
  await desktop(browser, url);
  await mobile(browser, url, 390);
  await mobile(browser, url, 820);
  await browser.close();

  for (const p of pass) console.log(`  ok    ${p}`);
  for (const f of fail) console.log(`  FAIL  ${f}`);
  console.log(`\n${pass.length} passed, ${fail.length} failed`);
  process.exit(fail.length ? 1 : 0);
})();
