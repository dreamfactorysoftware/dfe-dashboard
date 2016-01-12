<?php namespace DreamFactory\Enterprise\Dashboard\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class Authenticate
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Create a new filter instance.
     *
     * @param  Guard $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->guest()) {
            return $request->ajax()
                ? response()->json('Unauthorized.', 401)
                : \Redirect::guest('auth/login')->with('Your session has expired or is otherwise not valid.', 'Invalid Session');
        }

        return $next($request);
    }
}
