@extends('layouts.partials.single-instance')

@section('panel-body')
    <div class="form-group">
        <label for="instance-name"
               class="col-md-2 control-label">{{ \Lang::get('dashboard.instance-name-label') }}</label>

        <div class="col-md-10">
            <div class="input-group">
                <input type="text"
                       minlength="3"
                       maxlength="64"
                       required
                       name="instance-name"
                       id="instance-name"
                       class="form-control"
                       placeholder="{{ $defaultInstanceName }}">
                <span class="input-group-addon">{{ $defaultDomain }}</span>
            </div>
            {!! \Lang::get('dashboard.instance-create-help') !!}
        </div>
    </div>
    @if( !empty( $offerings ) )
        {!! $offerings !!}
    @endif

    @if( !empty( $importables ) )
        <div class="row">
            <div class="col-md-offset-2 col-md-1"><h3 class="either-or">Or</h3></div>
        </div>

        <div class="form-group">
            <label for="import-id"
                   class="col-md-2 control-label">{{ \Lang::get('dashboard.instance-import-label') }}</label>

            <div class="col-md-10">
                <select name="import-id"
                        id="import-id"
                        class="form-control">
                    <option data-instance-id="" value="">Select an export...</option>
                    @foreach( $importables as $importable )
                        <option data-instance-id="{{ $importable['instance-id'] }}"
                                value="{{ $importable['name'] }}">{{ $importable['instance-name']}}
                            ({{ $importable['export-date'] }})
                        </option>
                    @endforeach
                </select>
                {!! \Lang::get('dashboard.instance-import-help') !!}
            </div>
        </div>
    @endif
    <input type="hidden" name="control" value="create">

    <script>
        jQuery(function ($) {
            var _importText = '{{ \Lang::get('dashboard.instance-import-button-text') }}',
                    _createText = '{{ \Lang::get('dashboard.instance-create-button-text') }}',
                    $_button = $('#btn-create-instance'),
                    $_btnIcon = $_button.find('i'),
                    $_btnSpan = $_button.find('span'),
                    $_select = $('select#import-id'),
                    $_input = $('input#instance-name');

            $_input.on('change', function (e) {
                $_btnIcon.removeClass('fa-upload').addClass('fa-rocket');
                $_btnSpan.html(_createText);
                $_select.removeAttr('aria-required aria-describedby aria-invalid');
                $_input.attr('aria-required', 'true');

                if ( $_select.closest('.form-group').hasClass('has-feedback')) {
                    $_select.val(null);
                    $_select.closest('.form-group').removeClass('has-feedback has-success has-error');
                    $_input.prop('required',true);
                    $_select.prop('required',false);
                }
            });

            $_select.on('change',function(e){
                $_btnIcon.removeClass('fa-rocket').addClass('fa-upload');
                $_btnSpan.html(_importText);
                $_input.removeAttr('aria-required aria-describedby aria-invalid');
                $_select.attr('aria-required', 'true');
                $_input.val($(this).find(':selected').data('instance-id'));

                if ($_input.closest('.form-group').hasClass('has-feedback')) {
                    $_input.closest('.form-group').removeClass('has-feedback has-success has-error');
                    $_input.prop('required', false);
                    $_select.prop('required', true);
                }

                return true;
            });
        });
    </script>
@overwrite

@section('panel-body-links')
    <button id="btn-create-instance" type="submit" class="btn btn-primary btn-success btn-sm"
            data-instance-action="create">
        <i class="fa fa-fw {{ config('icons.create') }}"
           style="margin-right: 8px;"></i><span>{{ \Lang::get('dashboard.instance-create-button-text') }}</span>
    </button>
@overwrite
