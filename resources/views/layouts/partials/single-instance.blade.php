<div class="{{ $panelSize or null }}">
    <div class="panel {{ $panelContext }} panel-instance">

        <div class="panel-heading" role="tab">
            <h4 class="panel-title" data-instance-name="{{ $instanceName or 'NEW' }}">
                @if(isset($collapse))
                    <a data-toggle="collapse" data-parent="#panel-accordion"
                       href="#{{ 'panel-body-' . $instanceName }}">
                        @endif
                        @if ( isset($headerIcon) )
                            <i class="fa fa-fw {{ $headerIcon }}"></i>
                        @endif
                        @if ( isset($panelTitle) )
                            {{ $panelTitle }}
                        @else
                            {{ "Set \$panelTitle to change" }}
                        @endif
                        @if ( isset($headerStatusIcon) )
                            <span class="pull-right"><i
                                        class="fa fa-fw {{ $headerStatusIcon }} {{ $headerStatusIconSize or 'fa-1x' }} header-status-icon"></i></span>
                        @endif
                        @if(isset($collapse))
                    </a>
                @endif
            </h4>
        </div>

        @if(isset($collapse))
            <div id="{{ 'panel-body-' . $instanceName }}" class="panel-collapse collapse in" role="tabpanel">
                @endif

                <div class="panel-body instance-panel-body">
                    @if(!isset($noForm))
                        <form id="{{ $formId }}" class="form-horizontal" method="POST">@endif

                            @if( isset($panelDescription) && !empty($panelDescription) )
                                <div class="panel-description">{!! $panelDescription !!}</div>
                                <hr class="hr"/>
                            @endif

                            @section('panel-body')
                                @if( isset($panelBody) )
                                    {!! $panelBody !!}
                                @endif
                            @show

                            @section('panel-captcha')
                                @if ( config('dashboard.require-captcha') )
                                    <div class="form-group form-group-recaptcha">
                                        <label for="{{ $captchaId or 'rc-text-input' }}"
                                               class="col-md-2 control-label"></label>

                                        <div class="col-md-10">
                                            <div id="{{ $captchaId or 'rc-text-input' }}"
                                                 class="g-recaptcha"
                                                 data-sitekey="{{ config('recaptcha.siteKey') }}"></div>
                                            <p class="help-block"
                                               style="margin-top:2px;font-size: 13px;color:#888;">{!! \Lang::get('dashboard.instance-proof-text') !!}</p>
                                        </div>
                                    </div>
                                @endif
                            @show

                            @section('panel-toolbar')
                                @if ( isset($toolbarButtons) && !empty($toolbarButtons) )
                                    <div class="instance-actions"
                                         data-toggle="collapse"
                                         data-target="#instance-toolbar-{{ $instanceName }}"
                                         aria-expanded="false"
                                         aria-controls="instance-toolbar-{{ $instanceName }}"><i
                                                class="fa fa-fw fa-angle-down"></i>
                                    </div>

                                    <div id="instance-toolbar-{{ $instanceName }}" class="panel-toolbar collapse"
                                         role="toolbar">
                                        <hr class="hr" style="margin-bottom: 5px;"/>
                                        @foreach( $toolbarButtons as $_button )
                                            <button id="{{ $_button['id'] }}"
                                                    type="{{ $_button['type'] }}"
                                                    @if(isset($_button['data']))
                                                    @foreach( $_button['data'] as $_dataKey => $_dataItem )
                                                    data-{{ $_dataKey }}="{{ $_dataItem }}"
                                                    @endforeach
                                                    @endif
                                                    data-instance-href="{{ $instanceUrl }}"
                                                    data-toggle="tooltip" data-placement="bottom"
                                                    title="{{ $_button['hint'] or $_button['text'] }}"
                                                    class="btn {{ $_button['size'] or 'btn-xs' }} {{ $_button['context'] or 'btn-default' }}">{!! $_button['icon'] !!}
                                                <span
                                                        class="hidden-xs hidden-sm hidden-md hidden-toolbar-button">{{ $_button['text'] }}</span>
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            @show

                            <div class="form-actions pull-right">
                                @section('panel-body-links')
                                    @if ( isset($instanceLinks) && !empty($instanceLinks) )
                                        <div class="dsp-links">
                                            @foreach( $instanceLinks as $_linkId => $_link )
                                                <button id="{{ $_link['id'] or $_linkId }}"
                                                        type="{{ $_link['type'] }}"
                                                        class="btn {{ $_link['context'] or 'btn-success' }}">
                                                    <i class="fa fa-fw {{ $_link['icon'] }}"></i>{{ $link['text'] }}
                                                </button>
                                            @endforeach
                                        </div>
                                    @endif
                                @show
                            </div>

                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="_provisioner"
                                   value="{{ \DreamFactory\Enterprise\Database\Enums\GuestLocations::DFE_CLUSTER }}">
                            @if(!isset($noForm))</form>@endif
                </div>

                @if(isset($collapse))
            </div>
        @endif
    </div>
</div>
