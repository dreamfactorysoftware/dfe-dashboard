<?php
/**
 * @file
 * This is the template for the new vendor key
 *
 * @var \stdClass $user
 */

$_vendorList = UserDashboard::vendorList( 'key' );
?>
<div class="accordion-group">
	<div class="instance-heading accordion-toggle" data-toggle="collapse" data-parent="#key_list" href="#key_new">
		<a class="accordion-toggle" data-trigger="hover" data-toggle="collapse" data-parent="#key_list" href="#key_new"><i class="fa fa-plus"
																														   style="text-decoration:none !important;"></i><span
				style="margin-left: 10px;">Create a Vendor Key</span></a>
	</div>
	<div id="key_new" class="accordion-body collapse">
		<div class="accordion-inner">
			<div class="key-icon well pull-left"><i class="fa fa-check-square-o fa-3x" style="color: darkgreen;"></i></div>
			<div class="key-info span8">
				<form id="form-vendor-key" class="form-horizontal" method="POST" action="/user/keys">
					<legend>Create a Key</legend>
					<p>blah blah blah... we need your key so we can provision the instance for you. </p>

					<div class="control-group">
						<label class=" control-label">Name</label>
						<div class="controls">
							<div class="input-append">
								<input type="hidden" name="control" value="create">
								<input type="text" name="id" id="key_name" value="key_<?php echo $user->name; ?>" class="input-medium">
							</div>
						</div>
					</div>

					<div class="control-group">
						<label class=" control-label">Hosting Provider</label>
						<div class="controls">
							<?php echo $_vendorList; ?>
						</div>
					</div>

					<?php include __DIR__ . '/_dashboard_vendor-key-amazon.php'; ?>
					<?php include __DIR__ . '/_dashboard_vendor-key-azure.php'; ?>


					<div class="key-links form-actions">
						<button type="submit" class="btn btn-primary">Save</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

