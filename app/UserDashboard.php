<?php
namespace DreamFactory\Enterprise\Dashboard;

/**
 * UserDashboard
 * Drives the bus
 */
class UserDashboard
{
    //*************************************************************************
    //* Constants
    //*************************************************************************

    /**
     * @var string
     */
    const SPINNING_ICON = 'fa fa-spinner fa-spin';
    /**
     * @var string
     */
    const HELP_BUTTON_URL = 'http://dreamfactorysoftware.github.io';
    /**
     * @var string
     */
    const DEFAULT_DSP_DOMAIN = '.cloud.dreamfactory.com';

    //*************************************************************************
    //* Variables
    //*************************************************************************

    /**
     * @var \stdClass
     */
    protected static $_user;
    /**
     * @var bool
     */
    protected static $_isAdminUser = false;
    /**
     * @var string The default sub-domain for new DSPs
     */
    protected static $_defaultDomain;
    /**
     * @var \stdClass
     */
    protected static $_lastResponse;
    /**
     * @type bool
     */
    protected static $_enableCaptcha = false;
    /**
     * @type string
     */
    protected static $_endpoint;
    /**
     * @type string
     */
    protected static $_hashKey;

    //*************************************************************************
    //* Methods
    //*************************************************************************

    /**
     * @param \stdClass $user
     *
     * @return bool|mixed|\stdClass|void
     * @return bool
     */
    public static function processRequest( &$user )
    {
        static::$_user = $user;

        if ( isset( $user, $user['admin_ind'] ) )
        {
            static::setIsAdminUser( 1 == $user['admin_ind'] );
        }

        if ( Pii::postRequest() )
        {
            if ( null === ( $_id = FilterInput::post( 'id' ) ) )
            {
                Pii::controller()->redirect( '/' );
            }

            //	Is this a control request?
            if ( null !== ( $_command = FilterInput::post( 'control' ) ) )
            {
//                $_captcha = new Captcha();
//                $_captcha->setPrivateKey( Pii::getParam( 'recaptcha.private_key' ) );
//                $_captcha->timeout = 30;

                switch ( $_command )
                {
                    case 'create':
                        /**                     try
                         * {
                         * //    Check captcha...
                         * if ( !$_captcha->isValid() )
                         * {
                         * $_captcha->setError();
                         * throw new CaptchaException( 'Validation code was not entered correctly.' );
                         * }
                         * }
                         * catch ( CaptchaException $_ex )
                         * {
                         * Pii::setFlash( 'error', $_ex->getMessage() );
                         *
                         * return false;
                         * }
                         **/

                        static::provisionInstance( $_id, $user, true, false );
                        break;

                    case 'create-remote':
                        static::provisionInstance( $_id, $user, false, true );
                        break;

                    case 'destroy':
                    case 'delete':
                        static::deprovisionInstance( $_id, $user );
                        break;

                    case 'start':
                        static::startInstance( $_id, $user );
                        break;

                    case 'stop':
                        static::stopInstance( $_id, $user );
                        break;

                    case 'export':
                    case 'snapshot':
                        static::snapshotInstance( $_id, $user );
                        break;

                    case 'snapshots':
                        static::_instanceSnapshots( $_id, $user );
                        break;

                    case 'migrate':
                    case 'import':
                        static::importInstance( $_id, $user );
                        break;

                    case 'status':
                        static::_instanceStatus( $_id );
                        break;
                }
            }

            Pii::controller()->redirect( '/' );
        }

        return false;
    }

    /**
     * @param \stdClass $user
     *
     * @return bool|mixed|\stdClass|void
     * @return bool
     */
    public static function processKeysRequest( &$user )
    {
        static::$_user = $user;

        if ( Pii::postRequest() )
        {
            if ( null === ( $_id = FilterInput::post( 'id' ) ) )
            {
                Pii::controller()->redirect( '/web/keys' );
            }

            if ( isset( $_FILES ) )
            {
                static::_handleFileUpload();
            }
            else
            {
                //	Is this a control request?
                if ( null !== ( $_command = FilterInput::post( 'control' ) ) )
                {
                    switch ( $_command )
                    {
                        case 'create':
                        case 'edit':
                            return static::_upsertKey( $_id, $_POST );

                        case 'delete':
                            return static::_deleteKey( $_id );

                        case 'enable':
                            return static::_enableKey( $_id );

                        case 'disable':
                            return static::_disableKey( $_id );
                    }
                }
            }

            Pii::controller()->redirect( '/web/keys' );
        }

        return false;
    }

    /**
     * @return bool|mixed|\stdClass
     */
    protected static function _upsertKey()
    {
        $_payload = array(
            'label'    => FilterInput::post( 'key_name' ),
            'key'      => FilterInput::post( 'key_key' ),
            'secret'   => FilterInput::post( 'key_secret' ),
            'vendorId' => FilterInput::post( 'vendor_id' ),
        );

        $_result = static::_apiCall( '/drupal/keys', $_payload, true );

        if ( !$_result->success )
        {
            Pii::setFlash( 'error', $_result->details->message );
        }
        else
        {
            Pii::setFlash( 'success', 'Key deleted' );
        }

        return $_result;
    }

    /**
     * @return bool|mixed|\stdClass
     */
    protected static function _deleteKey()
    {
        $_label = FilterInput::post( 'key_name' );
        $_vendorId = FilterInput::post( 'vendor_id' );

        if ( empty( $_label ) || empty( $_vendorId ) )
        {
            Pii::setFlash( 'error', 'Invalid arguments supplied.' );

            return false;
        }

        $_result = static::_apiCall( '/drupal/keys/' . $_vendorId . '/' . $_label, array(), true, HttpMethod::Delete );

        if ( !$_result->success )
        {
            Pii::setFlash( 'error', $_result->details->message );
        }
        else
        {
            Pii::setFlash( 'success', 'Key deleted' );
        }

        return $_result;
    }

    /**
     * @throws Kisma\Core\Exceptions\NotImplementedException
     * @return mixed
     */
    protected static function _enableKey()
    {
        throw new NotImplementedException();
    }

    /**
     * @throws Kisma\Core\Exceptions\NotImplementedException
     * @return mixed
     */
    protected static function _disableKey()
    {
        throw new NotImplementedException();
    }

    /**
     * @param string $id
     */
    protected static function _instanceStatus( $id )
    {
        $_status = static::_apiCall( '/drupal/status/' . $id );
        $_status->deleted = false;

        if ( isset( $_status->code, $_status->message ) )
        {
            $_status->provisioned = false;
            $_status->deprovisioned = false;
            $_status->trial = false;
            $_status->instanceState = 4;

            if ( $_status->code == 404 )
            {
                $_status->deleted = true;
            }
        }

        $_status->icons = static::getStatusIcon( $_status );
        $_status->buttons = static::getDspControls( $_status );

        if ( 2 == $_status->instanceState )
        {
            $_status->link =
                '<a href="https://' .
                $_status->instanceName .
                static::getDefaultDomain() .
                '" target="_blank" class="dsp-launch-link">' .
                $_status->instanceName .
                '</a>';
        }

        ob_end_clean();
        echo json_encode( $_status );
        Pii::end();
    }

    /**
     * @param string    $name
     * @param \stdClass $user
     * @param bool      $trial
     *
     * @return bool|mixed|\stdClass
     */
    public static function deprovisionInstance( $name, $user, $trial = true )
    {
        $_result = static::_apiCall( '/drupal/destroy', array('name' => $name), true );

        if ( !$_result->success )
        {
            Pii::setFlash( 'error', $_result->details->message );
        }
        else
        {
            Pii::setFlash(
                'success',
                'Your DSP is being destroyed. Once complete, you\'ll receive an email and you will be able to start a new instance.'
            );
        }

        return $_result;
    }

    /**
     * @param string    $name
     * @param \stdClass $user
     * @param bool      $trial
     * @param bool      $remote If true, create instance on user's account
     *
     * @return bool|mixed|\stdClass
     */
    public static function provisionInstance( $name, $user, $trial = false, $remote = false )
    {
        //	Clean up the name
        $_instanceName = trim( strtolower( preg_replace( "/[^a-zA-z0-9]/", '-', $name ) ) );

        if ( is_numeric( $_instanceName[0] ) )
        {
            Pii::setFlash( 'error', 'Your DSP name must begin with a letter (A-Z)' );

            return false;
        }

        //	Only admins can have a dsp without the prefix
        if ( !static::$_isAdminUser )
        {
            $_cluster = 1;
            $_dbServerId = 4;

            if ( 'dsp-' != substr( $_instanceName, 0, 4 ) )
            {
                $_instanceName = 'dsp-' . $_instanceName;
            }
        }
        else
        {
            $_cluster = 2;
            $_dbServerId = 7;
        }

        $_payload = array(
            'name'         => $_instanceName,
            'trial'        => $trial,
            'remote'       => $remote,
            'cluster_id'   => $_cluster,
            'db_server_id' => $_dbServerId,
        );

        $_url = '/drupal/provision';

        if ( $remote )
        {
            $_payload['size'] = FilterInput::post( 'dsp_size' );
            $_payload['key'] = FilterInput::post( 'vendor_key' );
            $_payload['secret'] = FilterInput::post( 'vendor_secret' );
        }

        $_result = static::_apiCall( $_url, $_payload, true );

        if ( !$_result->success )
        {
            static::drupal_set_message( $_result->details->message, 'error' );
        }
        else
        {
            static::drupal_set_message(
                'Your DSP is being created. You\'ll receive an email when it\'s complete.',
                'success'
            );
        }

        return $_result;
    }

    /**
     * @param string $name
     *
     * @return bool|mixed|\stdClass
     */
    public static function stopInstance( $name )
    {
        $_status = static::_apiCall( '/instance/stop/' . $name, array(), true );

        if ( $_status->success )
        {
            static::drupal_set_message( 'Your DSP is being stopped.', 'success' );
        }
        else
        {
            static::drupal_set_message( 'There was a problem stopping your DSP.', 'error' );
        }
    }

    /**
     * @param string $name
     *
     * @return bool|mixed|\stdClass
     */
    public static function startInstance( $name )
    {
        $_status = static::_apiCall( '/instance/start/' . $name, array(), true );

        if ( $_status->success )
        {
            static::drupal_set_message( 'Your DSP is being started.', 'success' );
        }
        else
        {
            static::drupal_set_message( 'There was a problem starting your DSP.', 'error' );
        }
    }

    /**
     * @param string    $name
     * @param \stdClass $user
     * @param bool      $trial
     *
     * @return bool|mixed|\stdClass
     */
    public static function snapshotInstance( $name, $user, $trial = true )
    {
        $_result = static::_apiCall( '/instance/snapshot', array('id' => $name), true );

        if ( !$_result->success )
        {
            static::drupal_set_message( $_result->details->message, 'error' );
        }
        else
        {
            static::drupal_set_message(
                'Your snapshot is being prepared. We will send you an email when it\'s ready to be downloaded.',
                'success'
            );
        }

        return $_result;
    }

    /**
     * @param string    $name
     * @param \stdClass $user
     *
     * @return bool|mixed|\stdClass
     */
    protected static function _instanceSnapshots( $name, $user )
    {
        $_result = static::_apiCall( '/instance/snapshots/' . $name, array(), true, HttpMethod::Get );

        if ( !$_result->success )
        {
            static::drupal_set_message( $_result->details->message, 'error' );

            return;
        }

        $_return = null;

        foreach ( $_result->details as $_name => $_snapshots )
        {
            $_return .= '<optgroup label="' . $_name . '">';

            /** @var $_snapshots \stdClass[] */
            foreach ( $_snapshots as $_snapshot )
            {
                $_date = date( 'F j, Y @ H:i:s', strtotime( $_snapshot->date ) );

                $_return .=
                    '<option id="' .
                    $_snapshot->snapshot_id .
                    '" value="' .
                    $_snapshot->snapshot_id .
                    '" name="' .
                    $_snapshot->snapshot_id .
                    '">' .
                    $_date .
                    '</option>';
            }

            $_return .= '</optgroup>';
        }

        ob_end_clean();
        echo $_return;
        Pii::end();
    }

    /**
     * @param string    $name
     * @param \stdClass $user
     *
     * @return bool|mixed|\stdClass
     */
    public static function importInstance( $name, $user )
    {
        $_snapshot = FilterInput::post( 'dsp-snapshot-list' );

        if ( empty( $_snapshot ) )
        {
            static::drupal_set_message( 'No snapshot selected to import.', 'error' );

            return false;
        }

        //	Strip off the name if there...
        if ( false !== strpos( $_snapshot, '.' ) )
        {
            $_parts = explode( '.', $_snapshot );

            if ( 2 != count( $_parts ) || false === strtotime( $_parts[1] ) )
            {
                static::drupal_set_message( 'Invalid snapshot ID', 'error' );

                return false;
            }

            $_snapshot = $_parts[1];
        }

        $_result = static::_apiCall( '/instance/import', array('id' => $name, 'snapshot' => $_snapshot), true );

        if ( !$_result->success )
        {
            static::drupal_set_message( $_result->details->message, 'error' );
        }
        else
        {
            static::drupal_set_message(
                'Your import request is now queued for processing. We will send you an email when it\'s complete.',
                'success'
            );
        }

        return $_result;
    }

    /**
     * @param string    $name
     * @param \stdClass $user
     * @param bool      $trial
     *
     * @return bool|mixed|\stdClass
     */
    public static function reprovisionInstance( $name, $user, $trial = true )
    {
        //	Clean up the name
        $_instanceName = preg_replace( "/[^a-zA-z0-9]/", '_', $name );

        $_result = static::_apiCall( '/drupal/reprovision', array('name' => $_instanceName), true );

        if ( !$_result->success )
        {
            static::drupal_set_message( $_result->details->message, 'error' );
        }
        else
        {
            static::drupal_set_message(
                'Your DSP is being restarted. You\'ll receive an email when it\'s complete.',
                'success'
            );
        }

        return $_result;
    }

    /**
     * @return bool|mixed|\stdClass
     */
    public static function getInstances()
    {
        return static::_apiCall( '/drupal/instances' );
    }

    /**
     * @param      $user
     * @param null $columns
     * @param bool $forRender
     *
     * @return array|null|string
     */
    public static function instanceTable( &$user, $columns = null, $forRender = false )
    {
        $_html = null;
        $_result = static::_apiCall( '/drupal/instances', array(), true );

        if ( !$_result->success )
        {
            Log::error( 'Error pulling instance list: ' . print_r( $_result, true ) );
            static::drupal_set_message( $_result->details->message, 'error' );
        }
        else
        {
            if ( !empty( $_result->details ) )
            {
                /** @var \stdClass $_model */
                foreach ( $_result->details as $_dspName => $_model )
                {
                    if ( !isset( $_model, $_model->id ) )
                    {
                        continue;
                    }

                    list( $_divId, $_instanceHtml, $_statusIcon ) = static::formatInstance( $_model );
                    $_domain = static::getDefaultDomain();

                    $_item = array(
                        'instance'       => $_model,
                        'groupId'        => 'dsp_list',
                        'targetId'       => $_divId,
                        'targetRel'      => $_model->id,
                        'opened'         => count( $_result->details ),
                        'triggerContent' => <<<HTML
<span class="instance-heading-dsp-name"><i class="fa fa-chevron-down"></i>{$_model->instanceName}<span class="muted">{$_domain}</span></span>
<span class="instance-heading-status"><i class="fa {$_statusIcon} fa-2x"></i></span>
HTML

                        ,
                        'targetContent'  => $_instanceHtml,
                    );

                    if ( $forRender )
                    {
                        $_html[] = $_item;
                    }
                    else
                    {
                        $_html .= Pii::controller()->renderPartial( '_dashboard_item', $_item, true );
                    }

                    unset( $_model );
                }
            }
        }

        return $_html;
    }

    /**
     *
     */
    public static function keyTable( &$user, $columns = null )
    {
        $_html = null;
        $_result = static::_apiCall( '/drupal/keys', array(), true );

        if ( !$_result->success )
        {
            Log::error( 'Error pulling key list: ' . print_r( $_result, true ) );
            static::drupal_set_message( $_result->details->message, 'error' );
        }
        else
        {
            $_html = null;

            if ( !empty( $_result->details ) )
            {
                foreach ( $_result->details as $_label => $_key )
                {
                    if ( !isset( $_key, $_key->id ) )
                    {
                        continue;
                    }

                    list( $_divId, $_keyHtml, $_statusIcon ) = static::formatKey( $_key );

                    $_item = array(
                        'groupId'        => 'key_list',
                        'targetId'       => $_divId,
                        'triggerContent' =>
                            '<i class="fa fa-chevron-down" style="text-decoration:none !important;"></i><span style="margin-left: 10px;">' .
                            $_key->label .
                            '</span></a><span class="instance-heading-status"><i class="fa ' .
                            $_statusIcon .
                            ' fa-2x"></i></span>',
                        'targetContent'  => $_keyHtml,
                    );

                    $_html .= Pii::controller()->renderPartial( '_dashboard_item', $_item, true );

                    unset( $_key );
                }
            }
        }

        return $_html;
    }

    /**
     * @param \stdClass $instance
     * @param int       $how
     *
     * @return string
     */
    public static function formatInstance( &$instance, $how = null )
    {
        $_gettingStartedButton =
            '<a class="btn btn-xs btn-info dsp-help-button" id="dspcontrol-' .
            $instance->instanceName .
            '" data-placement="left" title="Help" target="_blank" href="' .
            static::HELP_BUTTON_URL .
            '"><i class="fa fa-question-circle"></i></a>';

        list( $_icon, $_statusIcon, $_message, $_running ) = static::getStatusIcon( $instance );

        if ( empty( $instance->instanceId ) )
        {
            $instance->instanceId = 'NEW';
        }

        $_divId = static::divId( 'dsp', $instance );

        $_instanceLinkText = $_linkLink = null;
        $_html = static::getDspControls( $instance, $_buttons );

        if ( $instance->instanceState == 2 )
        {
            $_instanceLinkText = 'https://' . $instance->instanceName . static::getDefaultDomain();
            $_instanceLink =
                '<a href="' .
                $_instanceLinkText .
                '" target="_blank" class="dsp-launch-link">' .
                $instance->instanceName .
                '</a>';
            $_linkLink = '<a href="' . $_instanceLinkText . '" target="_blank">' . $_instanceLinkText . '</a>';
        }
        else
        {
            $_instanceLink = $instance->instanceName;
        }

        if ( static::_isIconClass( $_icon ) )
        {
            $_icon = '<i class="fa ' . $_icon . ' fa-3x"></i>';
        }

        $_html = <<<HTML
	<div class="dsp-icon well pull-left dsp-real">{$_icon}</div>
	<div class="dsp-info">
		<div class="dsp-name">{$_instanceLink}<small>{$_linkLink}</small></div>
		<div class="dsp-stats">{$_message}</div>
		<div class="dsp-links">
		<span class="dsp-controls pull-left">{$_html}</span>
			{$_gettingStartedButton}
		</div>
	</div>
HTML;

        return array($_divId, $_html, $_statusIcon, $_instanceLinkText);
    }

    /**
     * @param \stdClass $key
     *
     * @return string
     */
    public static function formatKey( &$key )
    {
        $_gettingStartedButton =
            '<a class="btn btn-sm btn-info dsp-help-button pull-right" id="keycontrol-' .
            $key->label .
            '" data-placement="left" title="Help!" target="_blank" href="' .
            static::HELP_BUTTON_URL .
            '"><i class="fa fa-question-circle"></i></a >';

        list( $_icon, $_statusIcon, $_message, $_running ) = static::getStatusIcon( $key, true );

        if ( empty( $key->id ) )
        {
            $key->id = 'NEW';
        }

        $_divId = static::divId( 'key', $key, true );
        $_html = static::getKeyControls( $key );

        $_keyLink = $key->label;

        if ( static::_isIconClass( $_icon ) )
        {
            $_icon = '<i class="fa ' . $_icon . ' fa-3x"></i>';
        }

        $_html = <<<HTML
	<div class="key-icon well pull-left">{$_icon}</div>
	<div class="key-info">
		<div class="key-name">{$_keyLink}</div>
		<div class="key-stats">{$_message}</div>
		<div class="key-links">
		<span class="key-controls pull-left">{$_html}</span>
			{$_gettingStartedButton}
		</div>
	</div>
HTML;

        return array($_divId, $_html, $_statusIcon);
    }

    /**
     * Formats the button panel for an individual DSP
     *
     * @param \stdClass $instance
     * @param array     $buttons
     *
     * @return string
     */
    public static function getDspControls( $instance, &$buttons = null )
    {
        $_buttons = array(
            'start'  => array(
                'enabled' => false,
                'hint'    => 'Start this DSP',
                'color'   => 'success',
                'icon'    => 'play',
                'text'    => 'Start'
            ),
            'stop'   => array(
                'enabled' => false,
                'hint'    => 'Stop this DSP',
                'color'   => 'warning',
                'icon'    => 'pause',
                'text'    => 'Stop'
            ),
            'export' => array(
                'enabled' => false,
                'hint'    => 'Make a portable DSP backup',
                'color'   => 'info',
                'icon'    => 'cloud-download',
                'text'    => 'Backup'
            ),
            'import' => array(
                'enabled' => false,
                'hint'    => 'Restore a portable backup',
                'color'   => 'warning',
                'icon'    => 'cloud-upload',
                'text'    => 'Restore',
                'href'    => '#dsp-import-snapshot',
            ),
            'delete' => array(
                'enabled' => false,
                'hint'    => 'Delete this DSP permanently',
                'color'   => 'danger',
                'icon'    => 'trash',
                'text'    => 'Destroy!'
            ),
        );

        if ( isset( $instance->vendorStateName ) && !empty( $instance->vendorStateName ) )
        {
            switch ( $instance->vendorStateName )
            {
                case 'terminated':
                    $_buttons['start']['enabled'] = false;
                    $_buttons['stop']['enabled'] = false;
                    $_buttons['export']['enabled'] = false;
                    $_buttons['import']['enabled'] = false;
                    $_buttons['delete']['enabled'] = true;
                    break;

                case 'stopped':
                    $_buttons['start']['enabled'] = true;
                    $_buttons['stop']['enabled'] = false;
                    $_buttons['export']['enabled'] = true;
                    $_buttons['delete']['enabled'] = true;
                    $_buttons['import']['enabled'] = false;
                    break;

                case 'running':
                    $_buttons['start']['enabled'] = false;
                    $_buttons['stop']['enabled'] = true;
                    $_buttons['export']['enabled'] = true;
                    $_buttons['delete']['enabled'] = true;
                    $_buttons['import']['enabled'] = true;
                    break;
            }
        }
        else
        {
            switch ( $instance->instanceState )
            {
                case static::Provisioned:
                    //	Not queued for deprovisioning
                    if ( 1 != $instance->deprovisioned )
                    {
                        $_buttons['stop']['enabled'] = true;
                        $_buttons['export']['enabled'] = true;
                        $_buttons['import']['enabled'] = true;
                        $_buttons['delete']['enabled'] = true;
                    }
                    break;

                case static::Deprovisioned:
                    //	Not queued for reprovisioning
                    if ( 1 != $instance->provisioned )
                    {
                        $_buttons['start']['enabled'] = true;
                        $_buttons['export']['enabled'] = true;
                        $_buttons['delete']['enabled'] = true;
                        $_buttons['import']['enabled'] = false;
                    }
                    break;
            }
        }

        $buttons = $_buttons;

        $_html = null;

        //	No stop for hosted instances...
        unset( $_buttons['stop'] );

        foreach ( $_buttons as $_buttonName => $_button )
        {
            $_hint = null;
            $_disabledClass = 'disabled';
            $_disabled = ( !$_button['enabled'] ? 'disabled="disabled"' : $_disabledClass = null );

            if ( !$_disabled && null !== ( $_hint = Option::get( $_button, 'hint' ) ) )
            {
                $_hint = 'data-toggle="tooltip" title="' . $_hint . '"';
            }

            if ( ( !isset( $instance->vendorId ) || 1 == $instance->vendorId ) && $_buttonName == 'start' )
            {
                $_href = 'https://' . $instance->instanceName . static::getDefaultDomain();
                $_button['text'] = 'Launch!';
                $_disabled = $_disabledClass = null;
                $_buttonName = 'launch';
            }
            else
            {
                $_href = isset( $_button['href'] ) ? $_button['href'] : '#';
            }

            $_html .= <<<HTML
  <a id="dspcontrol___{$_buttonName}___{$instance->instanceName}" class="btn btn-sm btn-{$_button['color']} {$_disabledClass}" {$_disabled} href="{$_href}" {$_hint}><i class="fa fa-{$_button['icon']}"></i> {$_button['text']}</a>
HTML;
        }

        $_html = <<<HTML
<div class="btn2-group">
{$_html}
</div>
HTML;

        return $_html;
    }

    /**
     * Formats the button panel for an individual key
     *
     * @param \stdClass $key
     * @param array     $buttons
     *
     * @return string
     */
    public static function getKeyControls( $key, &$buttons = null )
    {
        static $_buttons = array(
            'enable'  => array(
                'enabled' => false,
                'hint'    => 'Enable this key',
                'color'   => 'success',
                'icon'    => 'play',
                'text'    => 'Enable'
            ),
            'disable' => array(
                'enabled' => false,
                'hint'    => 'Disable this key',
                'color'   => 'warning',
                'icon'    => 'pause',
                'text'    => 'Disable'
            ),
            'edit'    => array(
                'enabled' => false,
                'hint'    => 'Edit this key',
                'color'   => 'info',
                'icon'    => 'pencil',
                'text'    => 'Edit'
            ),
            'delete'  => array(
                'enabled' => false,
                'hint'    => 'Delete this key permanently',
                'color'   => 'danger',
                'icon'    => 'trash',
                'text'    => 'Delete'
            ),
        );

        if ( isset( $key->label ) )
        {
            $_buttons['edit']['enabled'] = true;
            $_buttons['delete']['enabled'] = true;

            $_buttons['enable']['enabled'] = !( isset( $key->enabled ) && $key->enabled );
            $_buttons['disable']['enabled'] = false;
        }
        else
        {
            $_buttons['edit']['enabled'] = true;
        }

        $buttons = $_buttons;

        $_html = null;

        foreach ( $_buttons as $_buttonName => $_button )
        {
            $_hint = null;
            $_disabledClass = 'disabled';
            $_disabled = ( !$_button['enabled'] ? 'disabled="disabled"' : $_disabledClass = null );

            if ( !$_disabled && null !== ( $_hint = Option::get( $_button, 'hint' ) ) )
            {
                $_hint = 'data-toggle="tooltip" title="' . $_hint . '"';
            }

            $_href = isset( $_button['href'] ) ? $_button['href'] : '#';

            $_html .= <<<HTML
<button type="button" id="keycontrol___{$_buttonName}___{$key->label}" class="btn btn-{$_button['color']} {$_disabledClass}" {$_disabled} href="{$_href}" {$_hint}><i class="fa fa-{$_button['icon']}"></i> {$_button['text']}</a>
HTML;
        }

        $_html = <<<HTML
<div class="btn3-group">
{$_html}
</div>
HTML;

        return $_html;
    }

    /**
     * @param \stdClass $status
     * @param bool      $key
     *
     * @return array
     */
    public static function getStatusIcon( $status, $key = false )
    {
        $_statusIcon = 'fa-rocket';
        $_icon = 'fa-rocket';
        $_message = null;
        $_running = false;

        if ( $key )
        {
            $_statusIcon = 'fa-key';
            $_message = null;
        }
        else
        {

            if ( isset( $status->vendorStateName ) && null !== $status->vendorStateName )
            {
                switch ( $status->vendorStateName )
                {
                    case 'stopped':
                        $_statusIcon = $_icon = 'fa-stop';
                        $_message = 'This DSP is stopped. Click the Start button to restart.';
                        break;

                    case 'terminated':
                        $_statusIcon = $_icon = 'fa-ambulance';
                        $_message = 'This DSP is terminated. All you can do is destroy it.';
                        break;

                    case 'shutting-down':
                    case 'stopping':
                        $_statusIcon = $_icon = static::SPINNING_ICON;
                        $_message = 'This DSP is being stopped.';
                        break;

                    case 'pending':
                        $_statusIcon = $_icon = static::SPINNING_ICON;
                        $_message =
                            'This DSP is being prepared for the requested operation. Be cool baby, it\'ll be done in a sec.';
                        break;

                    case 'running':
                        $_message = 'This DSP is alive and well. Click on the name above to launch.';
                        $_running = true;
                        break;
                }
            }
            else
            {
                switch ( $status->instanceState )
                {
                    case static::Created:
                        $_statusIcon = $_icon = static::SPINNING_ICON;
                        $_message = 'This DSP request has been received and is queued for creation.';
                        break;

                    case static::Provisioning:
                        $_statusIcon = $_icon = static::SPINNING_ICON;
                        $_message =
                            'Your DSP is being carefully assembled with lots of love. You will receive an email when it is ready.';
                        break;

                    case static::Provisioned:
                        //	Queued for deprovisioning
                        if ( 1 == $status->deprovisioned )
                        {
                            $_statusIcon = $_icon = static::SPINNING_ICON;
                        }
                        $_message = 'This DSP is alive and well. Click on the name above to launch.';
                        $_running = true;
                        break;

                    case static::Deprovisioning:
                        $_statusIcon = $_icon = static::SPINNING_ICON;
                        $_message =
                            'This DSP is being destroyed. You will receive an email when it has been destroyed.';
                        break;

                    case static::Deprovisioned:
                        $_icon = '<img src="/img/icon-deprovisioned.png" class="fa fa-3x">';
                        $_statusIcon = 'fa-exclamation-triangle';
                        $_message =
                            'This DSP is being destroyed. You will receive an email when it has been destroyed.';
                        break;

                    case static::DeprovisioningError:
                    case static::ProvisioningError:
                        $_message =
                            'There was an error issuing your request. Our engineers have been notified. Maybe go take a stroll?';
                        $_statusIcon = $_icon = 'fa-ambulance';
                        break;
                }
            }
        }

        return array($_icon, $_statusIcon, $_message, $_running);
    }

    /**
     * @param string $url
     * @param array  $payload
     * @param bool   $returnAll
     *
     * @param string $method
     *
     * @throws CHttpException
     * @return bool|mixed|\stdClass
     */
    protected static function _apiCall( $url, $payload = array(), $returnAll = false, $method = HttpMethod::Post )
    {
        if ( null === static::$_user )
        {
            if ( null === ( static::$_user = Option::get( $_SESSION, 'user' ) ) )
            {
                throw new CHttpException( 'Not authorized.', 401 );
            }

//			Log::debug( 'UserDashboard user set to id#' . static::$_user['drupal_id'] );
        }

        $_payload = array_merge(
            array(
                'user_id'      => static::$_user['drupal_id'],
                'access_token' => static::$_user['drupal_password_text'],
            ),
            $payload ?: array()
        );

        $_response = Curl::post( static::Endpoint . '/' . trim( $url, '/' ), $_payload );
        static::$_lastResponse = $_response;

        if ( is_string( $_response ) && strlen( $_response ) > 1024 )
        {
            $_snippet = substr( $_response, 0, 1024 );
        }
        else
        {
            $_snippet = print_r( $_response, true );
        }

//		Log::debug(
//			PHP_EOL . PHP_EOL . '//====== API Call: ' . $url . ' ========== BEGIN =========\\' . PHP_EOL .
//			'>>----------------------------------------------------------->>  [REQUEST]' . PHP_EOL .
//			print_r( $_payload, true ) . PHP_EOL .
//			'<<-----------------------------------------------------------<<  [RESPONSE]' . PHP_EOL .
//			( empty( $_snippet ) ? print_r( $_response, true ) : $_snippet ) . '...[truncated]' . PHP_EOL .
//			'\\====== API Call: ' . $url . ' ========== END ==========//' . PHP_EOL . PHP_EOL
//		);

        if ( $_response && is_object( $_response ) && isset( $_response->success ) )
        {
            return $returnAll ? $_response : $_response->details;
        }

        //	Error and redirect
        static::drupal_set_message(
            'An unexpected situation has occurred with your request. Please try again in a few minutes, or email <a href="mailto:support@dreamfactory.com">support@dreamfactory.com</a>.',
            'error'
        );

        return false;
    }

    /**
     * @param string $prefix
     * @param object $instance
     * @param bool   $key
     *
     * @return string
     */
    public static function divId( $prefix, $instance, $key = false )
    {
        return
            $prefix .
            '___' .
            static::hashId( $instance->id ) .
            '___' .
            ( $key ? $instance->label : $instance->instanceName );
    }

    /**
     * Get a hashed id suitable for framing
     *
     * @param string $valueToHash
     *
     * @return string
     */
    public static function hashId( $valueToHash )
    {
        if ( null === $valueToHash )
        {
            return null;
        }

        return Hasher::hash( static::SaltyGoodness . $valueToHash );
    }

    /**
     * @param \stdClass $user
     */
    public static function setUser( $user )
    {
        static::$_user = $user;
    }

    /**
     * @return \stdClass
     */
    public static function getUser()
    {
        return static::$_user;
    }

    /**
     * @param string $defaultDomain
     */
    public static function setDefaultDomain( $defaultDomain )
    {
        static::$_defaultDomain = $defaultDomain;
    }

    /**
     * @return string
     */
    public static function getDefaultDomain()
    {
        if ( empty( static::$_defaultDomain ) )
        {
            static::$_defaultDomain = Pii::getParam( 'dashboard.default_dsp_domain', static::DEFAULT_DSP_DOMAIN );
        }

        return static::$_defaultDomain;
    }

    /**
     * @return bool
     */
    protected static function _handleFileUpload()
    {
        if ( !isset( $_FILES['key_file'] ) || !is_uploaded_file( $_FILES['key_file']['tmp_name'] ) )
        {
            return false;
        }

        $_fileHash = Hasher::generateUnique();
        $_fileName = str_replace( ' ', '-', strtolower( $_FILES['key_file']['name'] ) );
        $_size = $_FILES['key_file']['size'];
        $_tempSource = $_FILES['key_file']['tmp_name'];
        $_type = $_FILES['key_file']['type'];

        //	Key files shouldn't be this big
        if ( $_size > 10000 )
        {
            return false;
        }

        switch ( strtolower( $_type ) )
        {
            case 'application/xml':
                //
                break;
        }

        return true;
    }

    /**
     * Builds a list of enabled providers based on files in the dreamstrap/templates/cloud-providers directory
     *
     * @return string
     */
    public static function buildProviderList()
    {
        if ( null === ( $_html = Pii::getState( 'cache.cloud_providers' ) ) )
        {

            $_path = \Kisma::get( 'app.config_path' ) . '/templates/cloud-providers';

            Log::debug( '>> Provider scan started > ' . $_path );

            $_files = scandir( $_path );
            $_count = 0;

            if ( !empty( $_files ) )
            {
                foreach ( $_files as $_file )
                {
                    if ( $_file != '.' && $_file != '..' && !is_dir( $_path . '/' . $_file ) )
                    {
                        /** @noinspection PhpIncludeInspection */
                        $_config = @include( $_path . '/' . $_file );

                        if ( !empty( $_config ) )
                        {
                            if ( false === Pii::getParam( 'dashboard.' . $_config['id'], 'enabled', false ) )
                            {
                                Log::debug( '  * Skipping disabled provider "' . $_config['id'] . '": ' . $_file );
                                continue;
                            }

                            Log::debug( '  * Found "' . $_config['id'] . '": ' . $_file );

                            $_count++;
                            $_header = Option::get( $_config, 'header', 'Installing on ' . $_config['title'] );

                            $_item = array(
                                'groupId'        => 'roll-your-own',
                                'targetId'       => $_config['id'],
                                'triggerContent' =>
                                    '<i class="fa fa-cloud" style="text-decoration:none !important;"></i><span style="margin-left: 10px;">' .
                                    $_config['title'] .
                                    '</span>',
                                'targetContent'  => '<h4>' . $_header . '</h4>' . $_config['body'],
                            );

                            $_html .= Pii::controller()->renderPartial( '_dashboard_item', $_item, true );
                        }
                    }
                }
            }

            Log::debug( '<< Provider scan complete > ' . $_count . ' providers found' );
        }

        Pii::setState( 'cache.cloud_providers', $_html );

        return $_html;
    }

    /**
     * I didn't feel like rearranging all that code, so bite me
     *
     * @param string $message
     * @param string $type
     *
     * @return CConsoleApplication|CWebApplication
     */
    public static function drupal_set_message( $message, $type )
    {
        return Pii::setFlash( $type, $message );
    }

    /**
     * @param boolean $isAdminUser
     */
    public static function setIsAdminUser( $isAdminUser )
    {
        static::$_isAdminUser = $isAdminUser;
    }

    /**
     * @return boolean
     */
    public static function getIsAdminUser()
    {
        return static::$_isAdminUser;
    }

    /**
     * @return boolean
     */
    public static function isEnableCaptcha()
    {
        return static::$_enableCaptcha;
    }

    /**
     * @param boolean $enableCaptcha
     */
    public static function setEnableCaptcha( $enableCaptcha )
    {
        static::$_enableCaptcha = $enableCaptcha;
    }

    /**
     * @param \stdClass $lastResponse
     */
    public static function setLastResponse( $lastResponse )
    {
        static::$_lastResponse = $lastResponse;
    }

    /**
     * @return \stdClass
     */
    public static function getLastResponse()
    {
        return static::$_lastResponse;
    }

    /**
     * Determine if a class contains a FontAwesome icon (v3+)
     *
     * @param string $class
     *
     * @return bool
     */
    protected static function _isIconClass( $class )
    {
        return ( 'icon-' == substr( $class, 0, 5 ) ||
            'fa-' == substr( $class, 0, 3 ) ||
            $class == static::SPINNING_ICON );
    }
}
