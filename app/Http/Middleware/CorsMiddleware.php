<?php
// app/Http/Middleware/CorsMiddleware.php

namespace App\Http\Middleware;

use Closure;

class CorsMiddleware
{
    public function handle($request, Closure $next)
    {
        $origin = $request->header('Origin');

        // Allow requests from any origin
        $response = $next($request);

        $response->header('Access-Control-Allow-Origin', $origin);
        $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE');
        $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        
        // Additional headers for handling cookies and credentials
        $response->header('Access-Control-Allow-Credentials', 'true');
        $response->header('Access-Control-Expose-Headers', 'Authorization');

        return $response;
    }
}
