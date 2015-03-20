<?php namespace DreamFactory\Enterprise\Dashboard\Http\Controllers\Auth;

use DreamFactory\Enterprise\Dashboard\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{
    //******************************************************************************
    //* Traits
    //******************************************************************************

    use AuthenticatesAndRegistersUsers;

    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type string
     */
    protected $redirectTo = '/app/dashboard';

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Create a new authentication controller instance.
     *
     * @param  \Illuminate\Contracts\Auth\Guard     $auth
     * @param  \Illuminate\Contracts\Auth\Registrar $registrar
     */
    public function __construct( Guard $auth, Registrar $registrar )
    {
        $this->auth = $auth;
        $this->registrar = $registrar;

        $this->middleware( 'guest', ['except' => 'getLogout'] );
    }
}
