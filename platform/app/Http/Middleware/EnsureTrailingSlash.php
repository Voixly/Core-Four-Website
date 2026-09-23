<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTrailingSlash
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->isMethod('GET') && ! $request->isMethod('HEAD')) {
            return $next($request);
        }

        $host = strtolower($request->getHost());
        $path = $request->getPathInfo();
        $apex = $host === 'www.corefourroofing.com' || str_ends_with($host, '.hostingersite.com');
        $slash = $path !== '/'
            && ! str_ends_with($path, '/')
            && ! $request->is('admin*')
            && ! $request->is('login')
            && ! $request->is('chat*')
            && ! $request->is('leads')
            && ! $request->is('up')
            && ! str_contains($path, '.');

        if ($apex || $slash) {
            if ($slash) {
                $path .= '/';
            }
            $qs = $request->getQueryString();
            $origin = $apex ? 'https://corefourroofing.com' : $request->getSchemeAndHttpHost();

            return redirect()->away($origin.$path.($qs ? '?'.$qs : ''), 301);
        }

        return $next($request);
    }
}
