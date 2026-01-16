<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceDomain
{
    public function handle(Request $request, Closure $next): Response
    {
        if (
            $request->is('sitemap.xml*') ||
            $request->is('robots.txt*')
        ) {
            return $next($request);
        }
        $dominioOficial = 'www.frequenciacerta.app.br';

        if (
            app()->environment('production') &&
            $request->getHost() !== $dominioOficial
        ) {
            return redirect()->to(
                'https://' . $dominioOficial . $request->getRequestUri(),
                301
            );
        }

        return $next($request);
    }
}
