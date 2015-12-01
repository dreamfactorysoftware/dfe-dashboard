<?php namespace DreamFactory\Enterprise\Dashboard\Http\Controllers\Auth;

use DreamFactory\Enterprise\Common\Enums\AppKeyClasses;
use DreamFactory\Enterprise\Common\Http\Controllers\Auth\CommonAuthController;
use DreamFactory\Enterprise\Database\Enums\OwnerTypes;
use DreamFactory\Enterprise\Database\Models\AppKey;
use DreamFactory\Enterprise\Database\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
        /** @noinspection PhpUndefinedMethodInspection */
        return DB::transaction(function () use ($data){
            /** @noinspection PhpUndefinedMethodInspection */
            $_user = User::create([
                'first_name_text' => $data['first_name_text'],
                'last_name_text'  => $data['last_name_text'],
                'email_addr_text' => $data['email_addr_text'],
                'nickname_text'   => $data['nickname_text'],
                'password_text'   => Hash::make($data['password_text']),
            ]);

            $_appKey = AppKey::create([
                'key_class_text' => AppKeyClasses::USER,
                'owner_id'       => $_user->id,
                'owner_type_nbr' => OwnerTypes::USER,
                'server_secret'  => config('dfe.security.console-api-key'),
            ]);

            //  Update the user with the key info and activate
            $_user->api_token_text = $_appKey->client_id;
            $_user->active_ind = 1;
            $_user->save();

            return $_user;
        });
    }

}
