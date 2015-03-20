<?php
/**
 * WebLoginForm.php
 */
use DreamFactory\Fabric\Yii\Components\AuthUserIdentity;
use DreamFactory\Fabric\Yii\Models\Auth\User;
use Kisma\Core\Enums\DateTime;
use DreamFactory\Yii\Utility\Pii;

/**
 * WebLoginForm
 * Provides a standard simple login form
 */
class WebLoginForm extends \CFormModel
{
	//********************************************************************************
	//* Members
	//********************************************************************************

	/**
	 * @var string
	 */
	public $email_addr_text;
	/**
	 * @var string
	 */
	public $password_text;
	/**
	 * @var string
	 */
	public $password_confirm_text;
	/**
	 * @var bool
	 */
	public $remember_ind = false;
	/**
	 * @var AuthUserIdentity
	 */
	protected $_identity;

	//********************************************************************************
	//* Methods
	//********************************************************************************

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 *
	 * @return array
	 */
	public function rules()
	{
		return array(
			array( 'email_addr_text, password_text', 'required' ),
			array( 'password_text', 'authenticate', 'skipOnError' => true ),
		);
	}

	/**
	 * Declares attribute labels.
	 *
	 * @return array
	 */
	public function attributeLabels()
	{
		return array(
			'email_addr_text'       => 'Email Address',
			'password_text'         => 'Password',
			'password_confirm_text' => 'Password Again',
			'remember_ind'          => 'Remember Me',
		);
	}

	/**
	 * Authenticates the password.
	 * This is the 'authenticate' validator as declared in rules().
	 *
	 * @param string $attribute
	 * @param array  $params
	 *
	 * @return void
	 */
	public function authenticate( $attribute, $params )
	{
		$this->_identity = new AuthUserIdentity( $this->email_addr_text, $this->password_text );

		if ( !$this->_identity->authenticate() )
		{
			$this->addError( 'password', 'Incorrect username or password.' );
		}
	}

	/**
	 * Logs in the user using the given username and password in the model.
	 *
	 * @param bool $remember If true, cookie will be set for 30 days.
	 *
	 * @return boolean whether login is successful
	 */
	public function login( $remember = false )
	{
		if ( null === $this->_identity )
		{
			$this->_identity = new AuthUserIdentity( $this->email_addr_text, $this->password_text );
			$this->_identity->authenticate();
		}

		if ( AuthUserIdentity::ERROR_NONE === $this->_identity->errorCode )
		{
			Pii::user()->login( $this->_identity, ( false !== $remember ? DateTime::SecondsPerDay * 30 : 0 ) );
			$_SESSION['user'] = User::model()->findByPk( Pii::user()->getId() )->getAttributes();

			return true;
		}

		return false;
	}

	/**
	 * Starts a password recovery process
	 */
	public function recoverPassword()
	{
		/** @var $_user \User */
		if ( null === ( $_user = User::model()->byEmailAddress( $this->email_addr_text )->find() ) )
		{
			return false;
		}

		return $_user->sendPasswordRecovery();
	}

	/**
	 * @param AuthUserIdentity $identity
	 *
	 * @return WebLoginForm
	 */
	public function setIdentity( $identity )
	{
		$this->_identity = $identity;

		return $this;
	}

	/**
	 * @return AuthUserIdentity
	 */
	public function getIdentity()
	{
		return $this->_identity;
	}
}
