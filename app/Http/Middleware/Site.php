<?php

namespace App\Http\Middleware;

use Closure;

class Site
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

        $path = $request->path();
        $domain = $request->server('HTTP_HOST');
        $protocol = strpos($request->server('SERVER_PROTOCOL'), 'https') ? 'https' : 'http';
        $queryString = $request->server('QUERY_STRING');
        $lang = $request->route('lang');

        $variables = compact('path', 'lang', 'domain', 'protocol', 'queryString');

        $request->request->add($variables);

        return $next($request);
    }
}
