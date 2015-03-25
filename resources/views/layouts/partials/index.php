<?php
/**
 * Dashboard index
 *
 * @var User          $user
 * @var WebController $this
 */
use DreamFactory\Fabric\Yii\Models\Auth\User;
use DreamFactory\Yii\Utility\Pii;
use Kisma\Core\Utility\Option;

//*************************************************************************
//* Requirements
//*************************************************************************

//	Must be registered...
if ( Pii::guest() )
{
    $this->redirect( '/web/login' );
}

//*************************************************************************
//* Page Variables
//*************************************************************************

$_tableId = 'platform-table';
$_shortName = 'Platform';
$_message = isset( $messages ) ? $messages : null;
$_user = $_SESSION['user'];

if ( !is_array( $_user ) && is_numeric( $_user ) )
{
    throw new \CHttpException( 'Invalid user ID', 401 );
}

//*************************************************************************
//* Initialization
//*************************************************************************

$_result = @UserDashboard::processRequest( $_user );
$_dspList = @UserDashboard::instanceTable( $_user, null, true );
$_dspListOptions = '<li class="dropdown-header">Your DSPs</li><li>None!</li>';

$_groupItems = array(
    $this->renderPartial( '_dashboard_item', require __DIR__ . '/_dashboard_new-fabric-dsp.php', true )
);

if ( !empty( $_dspList ) )
{
    $_domain = UserDashboard::getDefaultDomain();
    $_dspListOptions = '<li class="dropdown-header">Your DSPs</li>';

    foreach ( $_dspList as $_dsp )
    {
        $_dspListOptions .= <<<HTML
<li><a href="https://{$_dsp['instance']->instanceName}{$_domain}" title="Launch this DSP!" target="_blank">{$_dsp['instance']->instanceName}</a></li>
HTML;

        $_groupItems[] = $this->renderPartial( '_dashboard_item', $_dsp, true );
    }
}

$_panelGroup = $this->renderPartial(
    '_dashboard_group',
    array(
        'groupId'    => 'dsp_list',
        'groupItems' => array(),
        'groupHtml'  => $_groupItems,
    ),
    true
);


if ( null !== ( $_message = Pii::getFlash( 'success' ) ) )
{
    $_message = <<<HTML
		<div class="alert alert-success fade in">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4>Success!</h4>

			<p>{$_message}</p>
		</div>
HTML;
}
elseif ( null !== ( $_message = Pii::getFlash( 'error' ) ) )
{
    $_message = <<<HTML
		<div class="alert alert-danger fade in">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4>Fail!</h4>

			<p>{$_message}</p>
		</div>
HTML;
}

require_once __DIR__ . '/_dashboard_import-snapshot.php';
?>
<div class="container-fluid col-xs-12 col-sm-10 col-sm-offset-1 user-dashboard">
    
    <div id="alert-status-change" class="alert alert-info alert-block alert-fixed hide">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <h4>Notice</h4>

        <p>The status of one of your DSPs below has changed!</p>
    </div>

    <?php echo $_message; ?>

    <div class="dsp-list">
        <?php echo $_panelGroup; ?>
    </div>
</div>

<form id="_dsp-control" method="POST">
    <input type="hidden" name="id" value="">
    <input type="hidden" name="control" value="">
</form>

<script type="text/javascript">
jQuery(function($) {
        $('#dropdown-dsp-list').append('<?php echo $_dspListOptions; ?>');

        $('#reveal-portal-key').on('click', function(e) {
                alert('Your API key for "portal" access is:\n\n<?php echo Option::get($user,'api_token_text'); ?>\n\nAdd the following header to your requests:\n\nX-DreamFactory-Portal-Key: <?php echo Option::get($user,'api_token_text'); ?>\n\n');
                return false;
            }
        );
    }
);
</script>
