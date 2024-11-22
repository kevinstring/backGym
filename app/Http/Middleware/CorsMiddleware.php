<?php

namespace App\Http\Middleware;

use Closure;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        
        // Intercepta solicitudes OPTIONS y responde adecuadamente
        if ($request->isMethod('OPTIONS')) {
            return response()->json('', 200, $this->getHeaders());
        }

        return $next($request);
    }

    /**
     * Obtiene los encabezados CORS necesarios.
     *
     * @return array
     */
    protected function getHeaders()
    {
        return [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'POST, GET, OPTIONS, PUT, DELETE',
            'Access-Control-Allow-Headers' => 'Content-Type, X-Auth-Token, Origin, Authorization',
            'Access-Control-Allow-Credentials' => 'true',
        ];
    }
    
}
