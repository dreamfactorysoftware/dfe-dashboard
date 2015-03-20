<?php
use DreamFactory\Yii\Controllers\BaseWebController;
use DreamFactory\Yii\Utility\Pii;
use Kisma\Core\Utility\FilterInput;
use Kisma\Core\Utility\Option;
use Kisma\Core\Utility\Sql;

/**
 * WebController.php
 * The initialization and set-up controller
 */
class WebController extends BaseWebController
{
    //*************************************************************************
    //	Constants
    //*************************************************************************

    /**
     * @var bool Turn on/off HTTP_REFERER security
     */
    const ENABLE_REFERRER_SECURITY = true;
    /**
     * @var array White-listed hosts for referrer security
     */
    protected static $_referrerWhiteList = array(
        'https://www.dreamfactory.com',
        'https://dev.dreamfactory.com',
        'http://dev.dreamfactory.com',
    );

    //*************************************************************************
    //* Methods
    //*************************************************************************

    /**
     * Initialize
     */
    public function init()
    {
        parent::init();

        $this->defaultAction = 'index';
        $this->setLoginFormClass( 'WebLoginForm' );
    }

    /**
     * Login
     */
    public function actionLogin()
    {
        $this->layout = 'login';

        parent::actionLogin();
    }

    /**
     * Validate drupal user
     */
    public function actionValidate()
    {
        if ( null === ( $_referrer = Option::server( 'HTTP_REFERER' ) ) )
        {
            $this->redirect( '/' );
        }

        if ( static::ENABLE_REFERRER_SECURITY )
        {
            foreach ( static::$_referrerWhiteList as $_whiteDude )
            {
                if ( false !== stripos( $_referrer, $_whiteDude, 0 ) )
                {
                    break;
                }

                $_whiteDude = null;
            }

            if ( empty( $_whiteDude ) )
            {
                throw new \CHttpException( 'Bad request', 400 );
            }
        }

        $_id = FilterInput::request( 'token', null, FILTER_SANITIZE_STRING );

        if ( empty( $_id ) )
        {
            $this->redirect( $_referrer );
        }

        $_sql = <<<MYSQL
SELECT
	*
FROM
	fabric_auth.user_t
WHERE
	sha1(drupal_password_text) = :id
MYSQL;

        $_row = Sql::find( $_sql, array(':id' => $_id), Pii::pdo() );

        if ( empty( $_row ) )
        {
            $this->redirect( $_referrer );
        }

        $_model = new \WebLoginForm();
        $_model->email_addr_text = $_row['email_addr_text'];
        $_model->password_text = $_row['drupal_password_text'];
        $_model->remember_ind = false;

        if ( !$_model->validate() )
        {
            throw new \CHttpException(
                'Validation error: ' . print_r( $_model->getErrors(), true ) . PHP_EOL . print_r( $_row, true )
            );
        }

        //	Validate user input and redirect to the previous page if valid
        if ( !$_model->login() )
        {
            throw new \CHttpException( 'Invalid user name or password.' );
        }

        $this->redirect( '/' );

        return;
    }

    /**
     * {@InheritDoc}
     */
    public function filters()
    {
        return array(
            'accessControl',
        );
    }

    /**
     * {@InheritDoc}
     */
    public function accessRules()
    {
        return array(
            array(
                'allow',
                'actions' => array(
                    'error',
                ),
                'users'   => array('*'),
            ),
            //	Allow authenticated users access to init commands
            array(
                'allow',
                'actions' => array(
                    'login',
                    'validate',
                ),
                'users'   => array('?'),
            ),
            //	Allow authenticated users access to init commands
            array(
                'allow',
                'actions' => array(
                    'index',
                    'logout',
                ),
                'users'   => array('@'),
            ),
            //	Deny all others access to init commands
            array(
                'deny',
                'actions' => array('*'),
            ),
        );
    }

    /**
     * Inserts our uniform view data into the view data
     *
     * @param string $view
     * @param array  $data
     * @param bool   $return
     * @param array  $layoutData
     *
     * @return string
     */
    public function render( $view, $data = null, $return = false, $layoutData = null )
    {
        return parent::render(
            $view,
            $data,
            $return,
            array_merge( $this->_getLayoutData(), Option::clean( $layoutData ) )
        );
    }

    /**
     * {@InheritDoc}
     */
    public function actionIndex()
    {
        $this->render( 'index', array('user' => Option::get( $_SESSION, 'user', array() )) );
    }

    /**
     * @return array
     */
    protected function _getLayoutData()
    {
        $_route = $this->getRoute();
        $_dspList = null;

        //	Change these to update the CDN versions used. Set to false to disable
        $_bootstrapVersion = '3.2.0'; // Set to false to disable
        $_bootswatchVersion = '3.2.0';
        $_dataTablesVersion = false;
        $_bootswatchTheme = Pii::getState( 'dashboard.theme', Pii::getParam( 'dashboard.default_theme' ) );
        $_useBootswatchThemes = ( $_bootswatchTheme && 'default' != $_bootswatchTheme );
        $_fontAwesomeVersion = '4.2.0'; // Set to false to disable
        $_jqueryVersion = '2.1.1';

        $_portalApiKey = Pii::guest() || !isset( $_SESSION['user'] ) ? null : $_SESSION['user']['api_token_text'];

        //	Set to true to enable the push menu
        $_enablePushMenu = false;

        //	Our css building begins...
        $_css = $_endScripts = $_scripts = null;

        if ( $_useBootswatchThemes )
        {
            $_css[] =
                '<link href="//maxcdn.bootstrapcdn.com/bootswatch/' .
                $_bootswatchVersion .
                '/' .
                $_bootswatchTheme .
                '/bootstrap.min.css" rel="stylesheet" media="screen">';
        }
        else if ( false !== $_bootstrapVersion )
        {
            $_css[] =
                '<link href="//maxcdn.bootstrapcdn.com/bootstrap/' . $_bootstrapVersion . '/css/bootstrap.min.css" rel="stylesheet"  media="screen">';
        }

        if ( false !== $_fontAwesomeVersion )
        {
            $_css[] = '<link href="//maxcdn.bootstrapcdn.com/font-awesome/' . $_fontAwesomeVersion . '/css/font-awesome.css" rel="stylesheet">';
        }

        if ( false !== $_dataTablesVersion )
        {
            $_css[] = '<link href="/css/df.datatables.css" rel="stylesheet">';
        }

        if ( false !== $_jqueryVersion )
        {
            $_scripts[] = '<script src="//ajax.googleapis.com/ajax/libs/jquery/' . $_jqueryVersion . '/jquery.min.js"></script>';
        }

        $_css = implode( PHP_EOL . chr( 9 ), $_css ) . PHP_EOL;
        $_scripts = implode( PHP_EOL . chr( 9 ), $_scripts ) . PHP_EOL;

        if ( false !== $_bootstrapVersion )
        {
            $_endScripts .= '<script src="//maxcdn.bootstrapcdn.com/bootstrap/' . $_bootstrapVersion . '/js/bootstrap.min.js"></script>';
        }

        if ( false !== $_dataTablesVersion )
        {
            $_endScripts .=
                '<script src="//ajax.aspnetcdn.com/ajax/jquery.dataTables/' .
                $_dataTablesVersion .
                '/jquery.dataTables.min.js"></script>';
            $_endScripts .= '<script src="/js/df.datatables.js"></script>';
        }

        return array(
            '_route'          => $_route,
            '_dspList'        => $_dspList,
            '_enablePushMenu' => $_enablePushMenu,
            '_css'            => $_css,
            '_scripts'        => $_scripts,
            '_endScripts'     => $_endScripts,
            '_portalApiKey'   => $_portalApiKey,
        );
    }
}
