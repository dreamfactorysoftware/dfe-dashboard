<?php namespace DreamFactory\Enterprise\Dashboard\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    //******************************************************************************
    //* Traits
    //******************************************************************************

    use DispatchesCommands, ValidatesRequests;

}
