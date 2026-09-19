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

To compare against the live site you need the pages as flat files. The
`preview:build` command produces them by sending real requests through the HTTP
kernel, so the output is exactly what a visitor is served — there is no second
renderer to keep in sync. Only links are rewritten, to point at the neighbouring
files:

```bash
cd platform
php artisan preview:build            # writes public/preview/
php artisan preview:build blog.html  # or just one page
php -S 127.0.0.1:8792 -t public      # http://127.0.0.1:8792/preview/
```

The page list comes from the route table, so a new page appears in the preview
with no extra wiring. Elfsight widgets (reviews, maps, Instagram) are locked to
the live domain and will not appear on localhost.

## Checking fidelity (`tools/`)

See `tools/README.md`. In short:

```bash
cd tools/audit && npm install
node audit.js                  # all pages, all breakpoints
node probe.js blog ".blk-post" # one component, live vs. preview
node shot.js financing --full  # screenshots into output/
```
