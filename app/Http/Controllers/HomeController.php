<?php namespace App\Http\Controllers;

use Illuminate\Http\Response;

class HomeController extends Controller
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * ctor
     */
    public function __construct()
    {
        //  require auth'd users
        $this->middleware( 'auth' );
    }

    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function index()
    {
        return view( 'home' );
    }

}
