<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AllowEmbeddingMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Allow embedding from any origin
        $request->headers->set('X-Frame-Options', 'ALLOW-FROM *');

        return $next($request);
    }
}
