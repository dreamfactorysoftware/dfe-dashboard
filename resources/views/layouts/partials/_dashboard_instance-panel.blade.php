<?php
/**
 *
 */
?>
<div class="{{ $panelSize }}">
    <div class="panel-instance">
        <div class="panel {{ $panelContext }} panel-instance">
            <div class="panel-heading panel-heading-instance">
                <h4 class="panel-title" data-instance-name="{{ $instanceName }}">{{ $instanceName }}
                    {{--<span class="text-muted text-faded">{{ $defaultDomain }}</span>--}}
                    <span class="instance-heading-status pull-right"><i class="fa fa-fw {{ $headerIcon }} {{ $headerIconSize or 'fa-1x' }}"></i></span>
                </h4>
            </div>

            <div id="{{ $instanceDivId }}" class="panel-body instance-status">
                <div class="row">

                    <div class="instance-status-icon col-sm-2">
                        <div class="well instance-status-icon {{ $instanceStatusContext }} pull-left">
                            <i class="fa fa-fw {{ $instanceStatusIcon }} {{ $instanceStatusIconSize or 'fa-3x' }}"></i></div>
                    </div>

                    <div class="instance-status-text dsp-info col-sm-10">
                        <div class="dsp-name instance-status-name"><a href="{{ $instanceUrl }}"
                                target="{{ $instanceUrlTarget or '_blank' }}"
                                class="dsp-launch-link instance-link">{{ $instanceName }}</a>
                        </div>
                        <div class="dsp-stats instance-status-stats">{{ $instanceStatusText }}</div>
                    </div>

                </div>

                <hr class="hr">

                <div class="row">
                    <div class="dsp-controls instance-toolbar">
                        @foreach($panelButtons as $_button)
                            <a id="{{ $_button['id'] }}"
                                class="{{ $_button['size'] or 'btn btn-xs col-xs-2 col-sm-2' }} {{ $_button['context'] or 'btn-success' }}"
                                href="{{ $_button['url'] or '#' }}" title="{{ $_button['hint'] }}"><i class="fa fa-fw {{ $_button['icon'] }}"></i>

                                <span class="hidden-sm hidden-xs hidden-md"> {{ $_button['text'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
