<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceDomain
{
    public function handle(Request $request, Closure $next): Response
    {
        // Substitua pelo seu domínio oficial
        $dominioOficial = 'frequenciacerta.app.br'; 

        // Se estiver em produção E o domínio atual NÃO for o oficial
        if (app()->environment('production') && $request->getHost() !== $dominioOficial) {

            // Redireciona para o domínio certo mantendo a rota e os parâmetros
            return redirect()->to(
                $request->getScheme() . '://' . $dominioOficial . $request->getRequestUri()
            );
        }

        return $next($request);
    }
}