# Core Four Roofing

Everything for the Core Four Roofing site lives in this folder. Nothing outside
it is needed to build, preview, or deploy.

```
platform/   The Laravel site that replaces the WordPress build. This is what ships.
tools/      Tooling for comparing the rebuild against the live site, plus the
            data captured off it. Never deployed.
report/     The standalone performance report delivered earlier. Static HTML,
            unrelated to the site.
```

## The site (`platform/`)

Laravel 11, MySQL, no build step for CSS — stylesheets are plain files under
`public/css/`. See `platform/DEPLOY.md` for the VPS deployment.

Public pages are rendered two ways:

- **Hand-built views** — the homepage, city pages, guides, and legal pages have
  their own Blade templates in `resources/views/public/`.
- **Captured layouts** — the 21 pages carried over from the old site are stored
  as structured JSON in `resources/data/pages/` and rendered by
  `App\Support\PageLayout` through `resources/views/blocks/`. The matching
  stylesheet is `public/css/blocks.css` and its classes are prefixed `blk-`.

### Local preview

The Laravel app needs a database. To iterate on layout without one,
`scripts/build-preview.py` renders the same JSON to static HTML:

```bash
cd platform
python3 scripts/build-preview.py     # writes public/preview/
php -S 127.0.0.1:8792 -t public      # http://127.0.0.1:8792/preview/
```

The preview builder deliberately mirrors the Blade renderer. If you change how a
page renders, change both, then confirm the generated HTML matches what you
expect. Elfsight widgets (reviews, maps, Instagram) are locked to the live
domain and will not appear on localhost.

## Checking fidelity (`tools/`)

See `tools/README.md`. In short:

```bash
cd tools/audit && npm install
node audit.js                  # all pages, all breakpoints
node probe.js blog ".blk-post" # one component, live vs. preview
node shot.js financing --full  # screenshots into output/
```
