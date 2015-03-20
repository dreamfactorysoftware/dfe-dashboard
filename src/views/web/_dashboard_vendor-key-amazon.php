<?php
/**
 * @file
 * Partial include for new Amazon keys
 *
 * @var \stdClass $user
 */
echo 'NEW AMAZON';
?>
<fieldset>
	<legend>Amazon AWS Credentials</legend>
	<p>In order to launch a DSP in your EC2 space, you will need to provide a set of AWS keys. We will request a temporary security token with these
		keys and destroy them immediately, saving only the token. You can create a new user with restricted access (in AWS) for this purpose if you
		prefer.</p>

	<div id="vendor_1" class="vendor-settings">
		<div class="control-group">
			<label class="control-label">Key</label>
			<div class="controls">
				<input type="text" id="key_key" name="key_key" class="input-large">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label">Secret</label>
			<div class="controls">
				<input type="password" id="key_secret" name="key_secret" class="input-large">
			</div>
		</div>
	</div>
</fieldset>