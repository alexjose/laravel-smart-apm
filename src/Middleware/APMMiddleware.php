<?php

namespace SmartAPM\Middleware;

use Closure;
use SmartAPM\APMCollector;

class APMMiddleware
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
        return $next($request);
    }
    
    public function terminate($request, $response)
    {
        resolve(APMCollector::class)->setResponseCode($response->getStatusCode());
    }
}
