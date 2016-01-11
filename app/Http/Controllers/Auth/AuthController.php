<?php namespace DreamFactory\Enterprise\Dashboard\Http\Controllers\Auth;

use DreamFactory\Enterprise\Common\Http\Controllers\Auth\CommonAuthController;
use DreamFactory\Enterprise\Database\Models\User;

class AuthController extends CommonAuthController
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(array $data)
    {
        return \Validator::make($data,
            [
                'first_name_text' => 'required|max:64',
                'last_name_text'  => 'required|max:64',
                'nickname_text'   => 'required|max:64',
                'email_addr_text' => 'required|email|max:320|unique:user_t',
                'password_text'   => 'required|confirmed|min:6',
            ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     *
     * @return User
     */
    public function create(array $data)
    {
        return User::artisanRegister($data);
    }

}
