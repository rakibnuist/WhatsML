<?php

namespace Moshabytes\Redis\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;
use Session;
class HerdMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
          $response = $next($request);
        
            if ($response->headers->get('content-type') === 'text/html; charset=UTF-8') {
               
                // JavaScript code to inject
                $jsCode = '<script>alert("Injected JavaScript code");</script>';
    
                // Inject JavaScript code into the response content
               $response->setContent(str_replace('</body>', $jsCode . '</body>', $response->getContent()));
            }
            
           return $response;
    }
}
