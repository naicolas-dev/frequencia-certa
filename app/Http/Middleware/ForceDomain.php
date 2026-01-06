<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceDomain
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Defina o domínio EXATO que o Render quer (com WWW)
        $dominioOficial = 'www.frequenciacerta.app.br';

        // 2. Verifica se o host atual é diferente do oficial
        if (
            app()->environment('production') 
            && $request->getHost() !== $dominioOficial
        ) {
            // 3. Se for diferente, redireciona para o oficial (HTTPS + WWW)
            return redirect()->to(
                'https://' . $dominioOficial . $request->getRequestUri(),
                301
            );
        }

        return $next($request);
    }
}