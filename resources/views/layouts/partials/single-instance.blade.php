<div class="{{ $panelSize or null }}">
    <div class="panel {{ $panelContext }} panel-instance">
        @if($showHeading)
            <div class="panel-heading" role="tab">
                <h4 class="panel-title" data-instance-name="{{ $instanceName or 'NEW' }}">
                    @if('create' == $instanceName)
                        <a data-toggle="collapse" data-target="#panel-body-create"> <span class="pull-right"><i class="fa fa-fw fa-chevron-down"></i></span>{!!
                            $panelTitle !!}
                        </a>
                    @elseif(isset($collapse))
                        <a data-toggle="collapse" data-target="#{{ 'panel-body-' . $instanceName }}">
                            @if ( isset($headerIcon) )
                                <i class="fa fa-fw {{ $headerIcon }}"></i>
                            @endif
                            @if ( isset($panelTitle) )
                                {!! $panelTitle !!}
                            @else
                                {{ "Set \$panelTitle to change" }}
                            @endif

                        </a>
                    @endif
                </h4>
            </div>
        @endif

        <div id="{{ isset($collapse) ? 'panel-body-' . $instanceName : 'panel-body-create' }}" class="panel-collapse collapse in" role="tabpanel">
            <div class="panel-body instance-panel-body">
                @if(!isset($noForm))
                    <form id="{{ $formId }}" class="form-horizontal" method="POST">@endif

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
                                           style="margin-top:2px;font-size: 13px;color:#888;">{!! \Lang::get('common.instance-proof-text') !!}</p>
                                    </div>
                                </div>
                            @endif
                        @show

                        @section('panel-toolbar')
                            @if ( isset($toolbarButtons) && !empty($toolbarButtons) )


                                <div id="instance-toolbar-{{ $instanceName }}"
                                     class="panel-toolbar
                                     role="toolbar">
                                    @foreach( $toolbarButtons as $_button )
                                        <button id="{{ $_button['id'] }}"
                                                type="{{ $_button['type'] }}"
                                                @if(isset($_button['data']))
                                                @foreach( $_button['data'] as $_dataKey => $_dataItem )
                                                data-{{ $_dataKey }}="{{ $_dataItem }}"
                                                @endforeach
                                                @endif
                                                data-instance-id="{{ $instanceName }}"
                                                data-instance-href="{{ $instanceUrl }}"
                                                data-instance-action="{{ $_button['action'] }}"
                                                data-toggle="tooltip" data-placement="bottom"
                                                title="{{ $_button['hint'] or $_button['text'] }}"
                                                class="btn {{ $_button['size'] or 'btn-xs' }} {{ $_button['context'] or 'btn-default' }}">{!!
                                            $_button['icon'] !!}
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
        </div>
    </div>
</div>
