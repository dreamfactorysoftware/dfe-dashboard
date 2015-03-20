<?php namespace DreamFactory\Enterprise\Dashboard\Auth;

use DreamFactory\Library\Fabric\Database\Models\Auth\User;
use Illuminate\Auth\DatabaseUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

class DashboardUserProvider extends DatabaseUserProvider
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array $credentials
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials( array $credentials )
    {
        $_query = $this->conn->table( $this->table );

        foreach ( $credentials as $key => $value )
        {
            if ( !str_contains( $key, 'password' ) )
            {
                $_query->where( $this->_mapKey( $key ), $value );
            }
        }

        $_model = $_query->first();

        return $_model ? User::find( $_model->id ) : $_model;
    }

    /**
     * Maps a generic key name to a database column name
     *
     * @param string $key
     *
     * @return string
     */
    public function _mapKey( $key )
    {
        switch ( $key )
        {
            case 'password':
                $key = 'password_text';
                break;

            case 'email':
                $key = 'email_addr_text';
                break;

            case 'remember':
                $key = 'remember_token';
                break;
        }

        return $key;
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  array                                      $credentials
     *
     * @return bool
     */
    public function validateCredentials( Authenticatable $user, array $credentials )
    {
        return $this->hasher->check( $credentials['password'], $user->getAuthPassword() );
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed $identifier
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById( $identifier )
    {
        $_model = $this->conn->table( $this->table )->find( $identifier );

        return $_model ? User::find( $_model->id ) : $_model;
    }

    /**
     * Retrieve a user by by their unique identifier and "remember me" token.
     *
     * @param  mixed  $identifier
     * @param  string $token
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken( $identifier, $token )
    {
        $_model = $this->conn->table( $this->table )
            ->where( 'id', $identifier )
            ->where( 'remember_token', $token )
            ->first();

        return $_model ? User::find( $_model->id ) : $_model;
    }

}
