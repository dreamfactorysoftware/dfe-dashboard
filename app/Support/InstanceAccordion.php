<?php namespace DreamFactory\Enterprise\Dashboard\Support;

use DreamFactory\Enterprise\Dashboard\Enums\DashboardDefaults;
use DreamFactory\Enterprise\Database\Enums\GuestLocations;
use DreamFactory\Enterprise\Database\Enums\ProvisionStates;
use DreamFactory\Library\Utility\IfSet;

/**
 * Knows how to build instance accordions
 */
class InstanceAccordion
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

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param \stdClass[] $instances
     * @param bool        $asString
     *
     * @return array|null|string
     */
    public function userInstanceTable($instances, $asString = true)
    {
        $_html = $asString ? null : [];

        /** @var \stdClass $_model */
        foreach ($instances as $_dspName => $_model) {
            if (!isset($_model, $_model->id)) {
                continue;
            }

            if ($asString) {
                $_html .= $this->addInstanceSection($_model, true);
            } else {
                $_html[] = $this->addInstanceSection($_model, false);
            }
        }

        return $_html;
    }

    /**
     * @param \stdClass $instance
     * @param bool      $asString
     *
     * @return \Illuminate\View\View|string
     */
    public function addInstanceSection($instance, $asString = true)
    {
        $_domain = $this->getDefaultDomain();

        list($_divId, $_instanceHtml, $_statusIcon) = $this->decorateInstance($instance);

        $_item = [
            'instance'       => $instance,
            'groupId'        => 'dsp_list',
            'targetId'       => $_divId,
            'targetRel'      => $instance->id,
            'opened'         => false,
            'triggerContent' => <<<HTML
<div class="instance-heading-name">{$instance->instance_name_text}<span class="text-muted">{$_domain}</div>
<div class="instance-heading-status pull-right"><i class="fa fa-fw {$_statusIcon} fa-2x"></i></div>
HTML
            ,
            'targetContent'  => $_instanceHtml,
        ];

        $_view = view('layouts.partials._dashboard_item', $_item);

        if ($asString) {
            return $_view->render();
        }

        return $_view;
    }

    public function addHostedSection()
    {
    }

    public function addImportSection()
    {
    }

    public function renderSingleItem($item)
    {
    }

    /**
     * @param \stdClass $status
     * @param bool      $key
     *
     * @return array
     */
    public function getStatusIcon($status, $key = false)
    {
        $_statusIcon = 'fa-rocket';
        $_icon = 'fa-rocket';
        $_message = null;
        $_running = false;

        if ($key) {
            $_statusIcon = 'fa-key';
            $_message = null;
        } else {

            if (isset($status->vendorStateName) && null !== $status->vendorStateName) {
                switch ($status->vendorStateName) {
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
            } else {
                switch ($status->state_nbr) {
                    case ProvisionStates::CREATED:
                        $_statusIcon = $_icon = static::SPINNING_ICON;
                        $_message = 'This DSP request has been received and is queued for creation.';
                        break;

                    case ProvisionStates::PROVISIONING:
                        $_statusIcon = $_icon = static::SPINNING_ICON;
                        $_message =
                            'Your DSP is being carefully assembled with lots of love. You will receive an email when it is ready.';
                        break;

                    case ProvisionStates::PROVISIONED:
                        //	Queued for deprovisioning
                        if (1 == $status->deprovision_ind) {
                            $_statusIcon = $_icon = static::SPINNING_ICON;
                        }
                        $_message = 'This DSP is alive and well. Click on the name above to launch.';
                        $_running = true;
                        break;

                    case ProvisionStates::DEPROVISIONING:
                        $_statusIcon = $_icon = static::SPINNING_ICON;
                        $_message =
                            'This DSP is being destroyed. You will receive an email when it has been destroyed.';
                        break;

                    case ProvisionStates::DEPROVISIONED:
                        $_icon = '<img src="/img/icon-deprovisioned.png" class="fa fa-3x">';
                        $_statusIcon = 'fa-exclamation-triangle';
                        $_message =
                            'This DSP is being destroyed. You will receive an email when it has been destroyed.';
                        break;

                    case ProvisionStates::DEPROVISIONING_ERROR:
                    case ProvisionStates::PROVISIONING_ERROR:
                        $_message =
                            'There was an error issuing your request. Our engineers have been notified. Maybe go take a stroll?';
                        $_statusIcon = $_icon = 'fa-ambulance';
                        break;
                }
            }
        }

        return [$_icon, $_statusIcon, $_message, $_running];
    }

    /**
     * @param $instance
     *
     * @return array
     */
    public function decorateInstance($instance)
    {
        $_gettingStartedButton =
            '<a class="btn btn-xs btn-info dsp-help-button" id="dspcontrol-' .
            $instance->instanceName .
            '" data-placement="left" title="Help" target="_blank" href="' .
            config('dfe.dashboard.help-button-url') .
            '"><i class="fa fa-question-circle"></i></a>';

        list($_icon, $_statusIcon, $_message, $_running) = $this->getStatusIcon($instance);

        if (empty($instance->instanceId)) {
            $instance->instanceId = 'NEW';
        }

        $_divId = static::divId('dsp', $instance);

        $_instanceLinkText = $_linkLink = null;
        $_html = static::getDspControls($instance, $_buttons);

        if ($instance->instanceState == 2) {
            $_instanceLinkText = 'https://' . $instance->instanceName . $this->getDefaultDomain();
            $_instanceLink =
                '<a href="' .
                $_instanceLinkText .
                '" target="_blank" class="dsp-launch-link">' .
                $instance->instanceName .
                '</a>';
            $_linkLink = '<a href="' . $_instanceLinkText . '" target="_blank">' . $_instanceLinkText . '</a>';
        } else {
            $_instanceLink = $instance->instanceName;
        }

        if (static::_isIconClass($_icon)) {
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

        return [$_divId, $_html, $_statusIcon, $_instanceLinkText];
    }

    /**
     * Formats the button panel for an individual DSP
     *
     * @param \stdClass $instance
     * @param array     $buttons
     *
     * @return string
     */
    public function getDspControls($instance, &$buttons = null)
    {
        $_buttons = [
            'start'  => [
                'enabled' => false,
                'hint'    => 'Start this DSP',
                'color'   => 'success',
                'icon'    => 'play',
                'text'    => 'Start',
            ],
            'stop'   => [
                'enabled' => false,
                'hint'    => 'Stop this DSP',
                'color'   => 'warning',
                'icon'    => 'pause',
                'text'    => 'Stop',
            ],
            'export' => [
                'enabled' => false,
                'hint'    => 'Make a portable DSP backup',
                'color'   => 'info',
                'icon'    => 'cloud-download',
                'text'    => 'Backup',
            ],
            'import' => [
                'enabled' => false,
                'hint'    => 'Restore a portable backup',
                'color'   => 'warning',
                'icon'    => 'cloud-upload',
                'text'    => 'Restore',
                'href'    => '#dsp-import-snapshot',
            ],
            'delete' => [
                'enabled' => false,
                'hint'    => 'Delete this DSP permanently',
                'color'   => 'danger',
                'icon'    => 'trash',
                'text'    => 'Destroy!',
            ],
        ];

        if (isset($instance->vendorStateName) && !empty($instance->vendorStateName)) {
            switch ($instance->vendorStateName) {
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
        } else {
            switch ($instance->state_nbr) {
                case ProvisionStates::PROVISIONED:
                    //	Not queued for deprovisioning
                    if (1 != $instance->deprovision_ind) {
                        $_buttons['stop']['enabled'] = true;
                        $_buttons['export']['enabled'] = true;
                        $_buttons['import']['enabled'] = true;
                        $_buttons['delete']['enabled'] = true;
                    }
                    break;

                case ProvisionStates::DEPROVISIONED:
                    //	Not queued for reprovisioning
                    if (1 != $instance->provision_ind) {
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
        unset($_buttons['stop']);

        foreach ($_buttons as $_buttonName => $_button) {
            $_hint = null;
            $_disabledClass = 'disabled';
            $_disabled = (!$_button['enabled'] ? 'disabled="disabled"' : $_disabledClass = null);

            if (!$_disabled && null !== ($_hint = IfSet::get($_button, 'hint'))) {
                $_hint = 'data-toggle="tooltip" title="' . $_hint . '"';
            }

            if ((!isset($instance->vendor_id) || GuestLocations::DFE_CLUSTER == $instance->vendor_id) && $_buttonName == 'start') {
                $_href = config('dfe.dashboard.default-domain-protocol',
                        'https') . '://' . $instance->instance_name_text . $this->_defaultDomain;
                $_button['text'] = 'Launch!';
                $_disabled = $_disabledClass = null;
                $_buttonName = 'launch';
            } else {
                $_href = isset($_button['href']) ? $_button['href'] : '#';
            }

            $_html .= <<<HTML
  <a id="dspcontrol___{$_buttonName}___{$instance->instance_name_text}" class="btn btn-sm btn-{$_button['color']} {$_disabledClass}" {$_disabled} href="{$_href}" {$_hint}><i class="fa fa-{$_button['icon']}"></i> {$_button['text']}</a>
HTML;
        }

        $_html = <<<HTML
<div class="btn2-group">
{$_html}
</div>
HTML;

        return $_html;
    }

    public function getDefaultDomain()
    {
        static $_defaultDomain;

        return $_defaultDomain
            ?: $_defaultDomain =
                '.' . trim(config('dfe.dashboard.default-dns-zone'),
                    '.') . '.' . trim(config('dfe.dashboard.default-dns-domain'), '.');
    }

    /**
     * @param string $prefix
     * @param object $instance
     * @param bool   $key
     *
     * @return string
     */
    public function divId($prefix, $instance, $key = false)
    {
        return
            $prefix .
            '___' .
            $this->hashId($instance->id) .
            '___' .
            ($key ? $instance->label : $instance->instance_name_text);
    }

    /**
     * Get a hashed id suitable for framing
     *
     * @param string $valueToHash
     *
     * @return string
     */
    public function hashId($valueToHash)
    {
        static $_key;

        if (null === $valueToHash) {
            return null;
        }

        null === $_key && ($_key = config('app.key'));

        return hash(DashboardDefaults::SIGNATURE_METHOD, config('app.key') . $valueToHash);
    }

    /**
     * Determine if a class contains a FontAwesome icon (v3+)
     *
     * @param string $class
     *
     * @return bool
     */
    protected function _isIconClass($class)
    {
        return ('icon-' == substr($class, 0, 5) ||
            'fa-' == substr($class, 0, 3) ||
            $class == static::SPINNING_ICON);
    }

}
