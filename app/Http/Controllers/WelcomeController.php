<?php namespace App\Http\Controllers;

use Illuminate\Http\Response;

class WelcomeController extends Controller
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * ctor
     */
    public function __construct()
    {
        $this->middleware( 'guest' );
    }

    /**
     * Show the application welcome screen to the user.
     *
     * @return Response
     */
    public function index()
    {
        return view( 'welcome' );
    }

}
