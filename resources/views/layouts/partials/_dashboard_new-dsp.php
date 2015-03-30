<?php
/**
 * @var Instance[] $_dspList
 */

use DreamFactory\Enterprise\Dashboard\Providers\DashboardServiceProvider;

$_providerList = app( DashboardServiceProvider::IOC_NAME )->buildProviderList();
$_token = csrf_token();

$_item = array(
    'opened'         => false,
    'groupId'        => 'dsp_list',
    'targetId'       => 'dsp_new_cloud',
    'triggerContent' => '<span class="instance-heading-dsp-name"><i class="fa fa-fw fa-plus"></i>Install on Your Cloud</span>',
    'targetContent'  => <<<HTML
			<div class="dsp-icon well pull-left"><i class="fa fa-fw fa-check-square-o fa-3x text-success"></i></div>
			<div class="dsp-info">
				<form id="form-provision-cloud" class="form-horizontal" method="POST">
		            <input type="hidden" name="_token" value="{$_token}">
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
