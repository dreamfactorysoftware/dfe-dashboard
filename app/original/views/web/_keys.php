<?php
/**
 * @file
 * Custom theme implementation to display the user dashboard
 *
 * @var array           $page
 * @var array           $variables
 * @var array|\stdClass $node
 * @var string          $directory
 * @var \stdClass       $user
 */
//	Must be registered...
if ( user_is_anonymous() )
{
	drupal_set_message( t( 'You must be logged in to access your dashboard.' ), 'error' );
	drupal_goto( 'user/login' );
}

//*************************************************************************
//* Requirements
//*************************************************************************

require_once dirname( dirname( dirname( __DIR__ ) ) ) . '/libraries/kisma/vendor/autoload.php';
require_once __DIR__ . '/_navbar-global.php';

use DreamFactory\Drupal\Droopy;
use DreamFactory\Drupal\Themes\DreamStrap\UserDashboard;
use Kisma\Core\Utility\Log;

//*************************************************************************
//* Page Variables
//*************************************************************************

$_tableId = 'key-table';
$_shortName = 'Key';
$_message = isset( $messages ) ? $messages : null;

//*************************************************************************
//* Initialization
//*************************************************************************

$_result = UserDashboard::processKeysRequest( $user );
$_keyList = UserDashboard::keyTable( $user );

//	Add in our css & junk
\drupal_add_css( drupal_get_path( 'theme', 'dreamstrap' ) . '/css/dreamstrap.dashboard.css', array( 'group' => CSS_THEME, 'weight' => 50 ) );
\drupal_add_js( drupal_get_path( 'theme', 'dreamstrap' ) . '/js/dreamstrap.dashboard.js' );
\drupal_add_js( drupal_get_path( 'theme', 'dreamstrap' ) . '/js/jquery.html5uploader.min.js' );

?>
<div class="container user-dashboard">
	<div class="system-messages"><?php echo $_message; ?>
	</div>
	<h2>Your Keys		<span class="pull-right">
			<button class="btn btn-small btn-success" data-toggle="collapse" data-parent="#key_list" href="#key_new"><i style="margin-right: 7px;"
																														class="fa fa-plus"></i>Add
				a New Key
			</button>
			<a class="btn btn-small btn-info" href="/user/dashboard"><i style="margin-right: 7px;" class="fa fa-sitemap"></i>Manage DSPs </a>
		</span>
	</h2>

	<div class="dashboard-headlines">
		<div class="accordion" id="key_list">
			<div id="key_list">
				<?php include __DIR__ . '/_dashboard_new-vendor-key.php'; ?>
				<?php echo $_keyList; ?>
			</div>
		</div>
	</div>
</div>

<form id="_key-control" method="POST">
	<input type="hidden" name="id" value="">
	<input type="hidden" name="control" value="">
	<input type="hidden" name="key" value="">
	<input type="hidden" name="secret" value="">
	<input type="hidden" name="label" value="">
</form>
<script type="text/javascript">
jQuery(function($) {
	$('a[id^="keycontrol-"]').tooltip({placement: "top"});

	$('.accordion-body').on('hide',function() {
		$(this).removeClass('panel-body-open');
		var $_heading = $(this).prev();
		$_heading.removeClass('instance-heading-shown');
		$_heading.find('i.fa-chevron-up').removeClass('fa-chevron-up').addClass('fa-chevron-down');
	}).on('show', function() {
			$(this).addClass('panel-body-open');
			var $_heading = $(this).prev();
			$_heading.addClass('instance-heading-shown');
			$_heading.find('i.fa-chevron-down').removeClass('fa-chevron-down').addClass('fa-chevron-up');
			//			if ( 'key_new' == $(this).attr('id') ) {
			//				$('form#form-vendor-key input#key_name').focus();
			//			}
		});

	$('#key_vendor').on('change', function() {
		$('.vendor-settings').slideUp();
		$('#vendor_' + $(this).val()).slideDown();
	});
});
</script>
