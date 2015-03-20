<?php
/**
 * @var Instance[] $_dspList
 */
use DreamFactory\Fabric\Yii\Models\Deploy\Instance;

$_providerList = UserDashboard::buildProviderList();

$_item = array(
    'opened'         => false,
    'groupId'        => 'dsp_list',
    'targetId'       => 'dsp_new_cloud',
    'triggerContent' => '<span class="instance-heading-dsp-name"><i class="fa fa-plus"></i>Install on Your Cloud</span>',
    'targetContent'  => <<<HTML
			<div class="dsp-icon well pull-left"><i class="fa fa-check-square-o fa-3x" style="color: darkgreen;"></i></div>
			<div class="dsp-info">
				<form id="form-provision-cloud" class="form-horizontal" method="POST">
					<h3>Install the DreamFactory Services Platform&trade; on Your Cloud</h3>
					<p>To install the DreamFactory Services Platform&trade; on your cloud, choose a supported vendor below. The next steps will be displayed.</p>
					<div class="clearfix"></div>
					<div class="panel-group" id="roll-your-own">
						{$_providerList}
					</div>
				</form>
			</div>
HTML
);

return $_item;
