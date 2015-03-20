<?php namespace DreamFactory\Enterprise\Dashboard\Http\Controllers\Auth;

use DreamFactory\Enterprise\Dashboard\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Mail\Message;

class PasswordController extends Controller
{
    //******************************************************************************
    //* Traits
    //******************************************************************************

    use ResetsPasswords;

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
     * Create a new password controller instance.
     *
     * @param  \Illuminate\Contracts\Auth\Guard          $auth
     * @param  \Illuminate\Contracts\Auth\PasswordBroker $passwords
     */
    public function __construct( Guard $auth, PasswordBroker $passwords )
    {
        $this->auth = $auth;
        $this->passwords = $passwords;

        $this->middleware( 'guest' );
    }

    /**
     * Send a reset link to the given user.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function postEmail( Request $request )
    {
        $this->validate( $request, ['email' => 'required|email'] );

        $response = $this->passwords->sendResetLink(
            $request->only( 'email' ),
            function ( Message $m )
            {
                $m->subject( $this->getEmailSubject() );
            }
        );

        switch ( $response )
        {
            case PasswordBroker::RESET_LINK_SENT:
                return redirect()->back()->with( 'status', trans( $response ) );

            case PasswordBroker::INVALID_USER:
                return redirect()->back()->withErrors( ['email' => trans( $response )] );
        }

        return null;
    }

    /**
     * Reset the given user's password.
     *
     * @param  Request $request
     *
     * @return Response
     */
    public function postReset( Request $request )
    {
        $this->validate(
            $request,
            [
                'token'    => 'required',
                'email'    => 'required|email',
                'password' => 'required|confirmed',
            ]
        );

        $credentials = $request->only(
            'email',
            'password',
            'password_confirmation',
            'token'
        );

        $response = $this->passwords->reset(
            $credentials,
            function ( $user, $password )
            {
                $user->password_text = bcrypt( $password );

                $user->save();

                $this->auth->login( $user );
            }
        );

        switch ( $response )
        {
            case PasswordBroker::PASSWORD_RESET:
                return redirect( $this->redirectPath() );

            default:
                return redirect()->back()
                    ->withInput( $request->only( 'email' ) )
                    ->withErrors( ['email' => trans( $response )] );
        }
    }

}
