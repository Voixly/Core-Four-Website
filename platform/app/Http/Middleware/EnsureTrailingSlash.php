<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTrailingSlash
{
    public function handle(Request $request, Closure $next): Response
    {
        $path = $request->getPathInfo();

        if (
            $request->isMethod('GET')
            && $path !== '/'
            && ! str_ends_with($path, '/')
            && ! $request->is('admin*')
            && ! $request->is('login')
            && ! $request->is('chat*')
            && ! $request->is('leads')
            && ! $request->is('up')
            && ! str_contains($path, '.')
        ) {
            $qs = $request->getQueryString();
            $target = $path.'/'.($qs ? '?'.$qs : '');

            return redirect()->away($target, 301);
        }

        return $next($request);
    }
}
