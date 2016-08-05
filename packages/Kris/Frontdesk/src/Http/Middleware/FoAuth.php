<?php

namespace Kris\Frontdesk\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class FoAuth
{
    protected $loggedin= false;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if (!\Session::has("fo_user"))
        {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
             } else {
                \Session::put('url.intended',\Request::url());
                return redirect()->route('fo.login');
            }
        }

        return $next($request);
    }
}
