<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;

class Authenticate
{

    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {

        //dont check user login status if it's a request for login or get flow from bot engine (which is restricted by IP address)
        if ((strpos(env('ANONYMOUS_ACCESS_IP'), $_SERVER['REMOTE_ADDR']) === false) && !$request->user() && $request->path() != 'api/system/login') {

            return response()->json(['message' => 'UNAUTHORIZED'], 401);
        }

        return $next($request);
    }

}
