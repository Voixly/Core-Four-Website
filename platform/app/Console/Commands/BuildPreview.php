<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * Writes the public pages out as static HTML so they can be compared against
 * the live site without a running app.
 *
 * The pages are produced by sending real requests through the HTTP kernel, so
 * what lands in public/preview is exactly what a visitor would be served. Only
 * the links are rewritten, to point at the neighbouring files on disk.
 */
class BuildPreview extends Command
{
    protected $signature = 'preview:build
                            {slug?* : Limit the build to these preview files}
                            {--out=preview : Directory under public/ to write into}';

    protected $description = 'Render the public pages to static HTML for offline comparison';

    public function handle(Kernel $kernel): int
    {
        $targets = $this->targets();
        $rewrites = $this->rewrites($targets);

        $only = $this->argument('slug');
        if ($only) {
            $targets = array_intersect_key($targets, array_flip($only));
            if (! $targets) {
                $this->error('No pages matched. Known pages: '.implode(', ', array_keys($this->targets())));

                return self::FAILURE;
            }
        }

        $dir = public_path($this->option('out'));
        if (! is_dir($dir) && ! mkdir($dir, 0755, true) && ! is_dir($dir)) {
            $this->error("Could not create {$dir}");

            return self::FAILURE;
        }

        // url() normalises away the trailing slash, which the trailing-slash
        // middleware would then 301 on, so build the URL by hand.
        $base = rtrim(url('/'), '/');

        $failed = 0;
        foreach ($targets as $name => $path) {
            $request = Request::create($base.$path, 'GET');
            $response = $kernel->handle($request);
            $status = $response->getStatusCode();

            if ($status !== 200) {
                $this->error(sprintf('%-52s %s -> HTTP %d', $name, $path, $status));
                $failed++;
                $kernel->terminate($request, $response);

                continue;
            }

            $html = strtr($response->getContent(), $rewrites);
            file_put_contents($dir.'/'.$name, $html);
            $kernel->terminate($request, $response);

            $this->line(sprintf('  %-52s %6.1f KB', $name, strlen($html) / 1024));
        }

        $this->newLine();
        $this->info(sprintf('%d pages written to %s', count($targets) - $failed, $dir));

        return $failed ? self::FAILURE : self::SUCCESS;
    }

    /**
     * Map each output filename to the path that produces it, read off the route
     * table so a new page needs no change here.
     *
     * @return array<string, string>
     */
    protected function targets(): array
    {
        $targets = ['index.html' => '/'];

        foreach (Route::getRoutes() as $route) {
            if (($route->defaults['slug'] ?? null) === null) {
                continue;
            }
            if (! str_ends_with($route->getActionName(), 'PageController@page')) {
                continue;
            }
            $targets[$route->defaults['slug'].'.html'] = '/'.trim($route->uri(), '/').'/';
        }

        return $targets;
    }

    /**
     * Point in-page links at the generated files. Longest paths are replaced
     * first so a parent page cannot swallow the start of a child's URL.
     *
     * @param  array<string, string>  $targets
     * @return array<string, string>
     */
    protected function rewrites(array $targets): array
    {
        $base = rtrim(url('/'), '/');
        $out = '/'.trim($this->option('out'), '/');

        $rewrites = [];
        foreach ($targets as $name => $path) {
            $file = $out.'/'.$name;
            $bare = rtrim($path, '/');

            // url() drops the trailing slash, so both spellings reach the HTML.
            // Quoting both ends keeps these exact matches, so no page path can
            // be rewritten as a prefix of a longer one.
            foreach (array_unique([$base.$path, $base.$bare, $path, $bare]) as $form) {
                if ($form === '') {
                    continue;
                }
                $rewrites['"'.$form.'"'] = '"'.$file.'"';
            }
        }

        return $rewrites;
    }
}
