<?php namespace DreamFactory\Enterprise\Dashboard\Services;

use DreamFactory\Enterprise\Common\Enums\AppKeyEntities;
use DreamFactory\Library\Fabric\Database\Models\Deploy\AppKey;
use DreamFactory\Library\Fabric\Database\Models\Deploy\User;
use Illuminate\Contracts\Auth\Registrar as RegistrarContract;
use Validator;

class Registrar implements RegistrarContract
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
    public function validator( array $data )
    {
        return Validator::make(
            $data,
            [
                'first_name_text' => 'required|max:64',
                'last_name_text'  => 'required|max:64',
                'nickname_text'   => 'required|max:64',
                'email_addr_text' => 'required|email|max:320|unique:user_t',
                'password_text'   => 'required|confirmed|min:6',
            ]
        );
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     *
     * @return User
     */
    public function create( array $data )
    {
        return \DB::transaction(
            function () use ( $data )
            {
                $_user = User::create(
                    [
                        'first_name_text' => $data['first_name_text'],
                        'last_name_text'  => $data['last_name_text'],
                        'email_addr_text' => $data['email_addr_text'],
                        'nickname_text'   => $data['nickname_text'],
                        'password_text'   => bcrypt( $data['password_text'] ),
                    ]
                );

                $_appKey = AppKey::create(
                    array(
                        'app_id_text' => AppKeyEntities::USER,
                        'owner_id'    => $_user->id,
                    )
                );

                $_user->update( ['api_token_text' => $_appKey->client_id] );

                return $_user;
            }
        );
    }

}
