<?php

namespace Amet\SimpleAdminAPI\Middleware;

use Closure;

class SimpleAdminMiddleware
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
        $request->request->add(['rules_access' => config('simple_admin_api.rules_access')[auth()->user()->role]]);
        $request->request->add(['manage_user' => config('simple_admin_api.manage_user')[auth()->user()->role]]);
        return $next($request);
    }
}
