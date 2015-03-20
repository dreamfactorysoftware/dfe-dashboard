<?php
use DreamFactory\Platform\Yii\Models\BasePlatformSystemModel;
use Kisma\Core\Utility\Hasher;
use Kisma\Core\Utility\Log;
use Kisma\Core\Utility\Sql;

/**
 * GlobalProviderUser.php
 * The global user service registry model for the DSP
 *
 * Columns:
 *
 * @property int                 $instance_id
 * @property int                 $user_id
 * @property string              $provider_user_id
 * @property int                 $provider_id
 * @property int                 $account_type_nbr
 * @property array               $auth_text
 * @property string              $last_use_date
 *
 * @property User                $user
 * @property GlobalProvider      $provider
 */
class GlobalProviderUser extends BasePlatformSystemModel
{
	//*************************************************************************
	//* Methods
	//*************************************************************************

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return static::tableNamePrefix() . 'global_provider_user_t';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		$_rules = array(
			array( 'instance_id, provider_id, provider_user_id, user_id, account_type_nbr, auth_text, last_use_date', 'safe' ),
		);

		return array_merge( parent::rules(), $_rules );
	}

	/**
	 * @return array
	 */
	public function relations()
	{
		return array_merge(
			parent::relations(),
			array(
				 'user'     => array( static::BELONGS_TO, __NAMESPACE__ . '\\User', 'user_id' ),
				 'provider' => array( static::BELONGS_TO, __NAMESPACE__ . '\\Provider', 'provider_id' ),
			)
		);
	}

	/**
	 * @return array
	 */
	public function behaviors()
	{
		return array_merge(
			parent::behaviors(),
			array(
				 //	Secure JSON
				 'base_platform_model.secure_json' => array(
					 'class'            => 'DreamFactory\\Platform\\Yii\\Behaviors\\SecureJson',
					 'salt'             => $this->getDb()->password,
					 'secureAttributes' => array(
						 'auth_text',
					 )
				 ),
			)
		);
	}

	/**
	 * @param array $additionalLabels
	 *
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels( $additionalLabels = array() )
	{
		return parent::attributeLabels(
			array_merge(
				$additionalLabels,
				array(
					 'provider_id'      => 'Provider ID',
					 'user_id'          => 'User ID',
					 'provider_user_id' => 'Provider User ID',
					 'account_type'     => 'Account Type',
					 'auth_text'        => 'Authorization',
					 'last_use_date'    => 'Last Used',
				)
			)
		);
	}

	/**
	 * @param int    $userId
	 * @param string $portal
	 *
	 * @return $this
	 */
	public function byUserPortal( $userId, $portal )
	{
		$this->getDbCriteria()->mergeWith(
			array(
				 'condition' => 'user_id = :user_id and provider_id = ( select p.id from df_sys_provider p where p.api_name = :api_name limit 1 order by id )',
				 'params'    => array(
					 ':user_id'  => $userId,
					 ':api_name' => trim( strtolower( $portal ) ),
				 ),
			)
		);

		return $this;
	}

	/**
	 * @param int $userId
	 * @param int $portalId
	 *
	 * @return $this
	 */
	public function byUserPortalId( $userId, $portalId )
	{
		$this->getDbCriteria()->mergeWith(
			array(
				 'condition' => 'user_id = :user_id and provider_id = :portal_id',
				 'params'    => array(
					 ':user_id'   => $userId,
					 ':portal_id' => $portalId,
				 ),
			)
		);

		return $this;
	}

	/**
	 * @param int $userId
	 * @param int $GlobalProviderUserId
	 *
	 * @return $this
	 */
	public function byUserGlobalProviderUserId( $userId, $GlobalProviderUserId )
	{
		$this->getDbCriteria()->mergeWith(
			array(
				 'condition' => 'user_id = :user_id and provider_user_id = :provider_user_id',
				 'params'    => array(
					 ':user_id'          => $userId,
					 ':provider_user_id' => $GlobalProviderUserId,
				 ),
			)
		);

		return $this;
	}

	/**
	 * @param string $email
	 *
	 * @return User
	 */
	public static function getByEmail( $email )
	{
		return User::model()->find(
			'email = :email',
			array(
				 ':email' => $email,
			)
		);
	}

	/**
	 * @param int $userId
	 *
	 * @return Provider[]
	 */
	public static function getLogins( $userId )
	{
		return static::model()->findAll(
			'user_id = :user_id',
			array(
				 ':user_id' => $userId,
			)
		);
	}

	/**
	 * @param int $userId
	 * @param int $providerId
	 *
	 * @return GlobalProviderUser
	 */
	public static function getLogin( $userId, $providerId )
	{
		return static::model()->find(
			'user_id = :user_id and provider_id = :provider_id',
			array(
				 ':user_id'     => $userId,
				 ':provider_id' => $providerId,
			)
		);
	}
}
