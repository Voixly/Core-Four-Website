# Tools

Supporting tooling for the site rebuild. Nothing here ships to production.

## `audit/` — live vs. rebuild comparison

Drives Chrome through `puppeteer-core` to measure the live site and the local
static preview side by side, so layout drift shows up as numbers rather than by
eye.

### Setup

```bash
cd tools/audit
npm install
```

Chrome itself is not downloaded — the scripts use the copy already installed.
Override the path with `CHROME_PATH` if yours lives somewhere else.

### Serving the preview

The preview pages are generated into `platform/public/preview/`. Regenerate and
serve them first:

```bash
cd platform
php artisan preview:build
php -S 127.0.0.1:8792 -t public
```

`pages.json` maps each page slug to its live URL and its preview URL. Update it
if the port changes.

### Commands

| Command | What it does |
| --- | --- |
| `node audit.js` | Every page at 1440 / 1100 / 390px. Prints the share of text that lands in the wrong place and how far total page height drifts, then writes `output/summary.json`. |
| `node audit.js blog financing` | Same, limited to the named slugs. |
| `node probe.js <slug> <selector> [width...]` | Diffs the computed styles and box of one selector, live vs. preview. Use when a specific component looks off. |
| `node shot.js <slug> [--width N] [--full] [--selector sel]` | Screenshots both sides into `output/`. |

`output/` is scratch space and is not tracked in git.

## `captured/` — data pulled off the live site

Extraction artifacts kept so the pages can be rebuilt without re-scraping:

- `faq.json` — FAQ questions and answers, per page.
- `blog-thumbs.json` — blog post thumbnail URLs.
- `reviews.json` — full text of the featured testimonials.
- `live-vars.json` — CSS custom properties read off the live theme.
- `probe-live-home.json` — measured geometry of the live homepage.
- `page-text/` — plain-text copy of each page, used to check nothing is missing.
