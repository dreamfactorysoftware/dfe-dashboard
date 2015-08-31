/**
 * Global settings
 * @type {{}}
 * @private
 */
var _dso = {
    controlForm:          null,
    statusCheckFrequency: 30000,
    alertHideTimeout:     30000
};
/**
 * @type {boolean}
 * @private
 */
var _checking = false;

/**
 * Sets up a timer to close an open alert
 * @param selector
 * @param delay
 * @param [show]
 * @private
 */
var _closeAlert = function (selector, delay, show) {
    var $_alert = jQuery(selector).not('.alert-fixed').alert();

    if ($_alert.length) {
        if (show) {
            jQuery(selector).removeClass('hide').show();
        }

        if (!$_alert.hasClass('alert-fixed')) {
            window.setTimeout(function () {
                $_alert.alert('close')
            }, delay);
        }
    }
};

/**
 *
 * @private
 */
var _checkProgress = function () {
    (function ($) {

        $(".instance-heading-status .fa-spinner").each(function (index, item) {
            var $_form = $("form#_dsp-control");
            var _div = $(this).closest('a[data-toggle="collapse"]').attr('href');

            if (_div && _div.length) {
                var _id = $(_div).attr('id');
                var _parts = _id.split('___');

                if (3 == _parts.length) {
                    $('input[name="id"]', $_form).val(id);
                    $('input[name="control"]', $_form).val('status');

                    $.post('/status/' + id, $_form.serialize(), function (data) {
                            var $_inner = $("div#" + _id + ".panel-collapse .panel-body");

                            $(item).fadeOut('slow', function () {
                                $(item).fadeIn('fast');
                            });

                            if (data && ( 404 == data.code || 1 == data.deleted )) {
                                //	Delete one...
                                try {
//								_flasher($(item).closest('.accordion-group').find('.instance-heading'), true);
                                    $(item).removeClass('fa-spinner fa-spin').addClass(data.icons[1]);
                                    $(".dsp-icon i", $_inner).removeClass('fa-spinner fa-spin').addClass(data.icons[1]);
                                    $(item).closest('.panel-instance').fadeOut();
                                } catch (e) {
                                    //	Ignore
                                }

                                _closeAlert('#alert-status-change', _dso.alertHideTimeout, true);
                            } else {
                                if (data.instanceState) {
                                    //	Replace message...
                                    if (data.icons[2]) {

                                        $("div.dsp-info div.dsp-stats", $_inner).html(data.icons[2]);
                                    }

                                    if (data.link) {
                                        $("div.dsp-info div.dsp-name", $_inner).html(data.link);
                                    }

                                    if (data.buttons) {
                                        $("div.dsp-info div.dsp-links .dsp-controls", $_inner).html(data.buttons);
                                    }

                                    if (data.icons[1] != 'fa-spinner fa-spin') {
//								_flasher($(item).closest('.accordion-group').find('.instance-heading'));
                                        $(item).removeClass('fa-spinner fa-spin').addClass(data.icons[1]);
                                        $(".dsp-icon i", $_inner).removeClass('fa-spinner fa-spin').addClass(data.icons[1]);

                                        _closeAlert('#alert-status-change', _dso.alertHideTimeout, true);
                                    }
                                }
                            }
                        }

                        , 'json');
                }
            }
        });
        _checking = false;
    })(jQuery);

    //	Re-up if there are more...
    if (!_checking && jQuery(".instance-heading-status .fa-spinner").length) {
        _checking = true;
        window.setTimeout(_checkProgress, _dso.statusCheckFrequency);
    }
};

/**
 * Generates the necessary form data to push an action request to the console
 * @param {jQuery} $element
 * @private
 */
var _processAction = function ($element) {
    var _id = $element.data('instance-id');
    var _action = $element.data('instance-action');

    if ('create' == _action) {

        if (_validator && !_validator.valid()) {
            return;
        }

        _id = $('input#instance-name').val();

        if (!_id || !_id.length) {
            alert('Invalid instance name.');
        }
    } else {
        $element.addClass('disabled').prop('disabled', true);
    }

    var _result = _makeRequest(_id, _action, $element.data('instance-href') || null);

    $element.removeClass('disabled').prop('disabled', false);
};

/**
 *
 * @param id
 * @param action
 * @param href
 * @returns {*}
 * @private
 */
var _makeRequest = function (id, action, href) {

    switch (action) {
        case 'launch':
            window.top.open(href, id);
            return;
        case 'destroy':
        case 'deprovision':
        case 'delete':
            if (!confirm('Permanently delete instance "' + id + '"?')) {
                return;
            }
            break;
        case 'start':
            if (!confirm('Start instance "' + id + '"?')) {
                return;
            }
            break;
        case 'stop':
            if (!confirm('Stop instance "' + id + '"?')) {
                return;
            }
            break;
        case 'provision':
        case 'create':
        case 'help':
            break;
        case 'export':
            if (!confirm('Export instance "' + id + '"?')) {
                return;
            }
            break;
        case 'import':
            //				if ( !confirm('WARNING: Destructive Procedure. This may overwrite existing settings in this DSP.' + "\n\n" + 'Import to DSP "' + id + '"?') ) {
            //					return;
            //				}

            $('input[name="id"]', _dso.controlForm).val(id);
            $('input[name="control"]', _dso.controlForm).val('snapshots');
            $('body').css('cursor', 'wait');

            $.post('/control', _dso.controlForm.serialize(), function (data) {
                $('body').css('cursor', 'pointer');
                if (data) {
                    $('#dsp-snapshot-list').html(data);
                    $('form#_dsp-snapshot-control input[name="id"]').val(id);
                    $('#dsp-import-snapshot').modal('show');
                } else {
                    alert('No snapshots available for this instance.');
                }
            }, 'html');
            return;

        default:
            alert('Invalid command "' + action + '"');
            return;
    }

    $('input[name="id"]', _dso.controlForm).val(id);
    $('input[name="control"]', _dso.controlForm).val(action);

    return _dso.controlForm.submit();
};

/**
 * DR
 */
jQuery(function ($) {
    //	Reusables...
    var $_toolbars = $('div[id^="instance-toolbar-"]');

    //	Set form if not already
    if (!_dso.controlForm) {
        _dso.controlForm = $('form#_dsp-control');
    }

    //	Open/close toolbar
    $_toolbars.on('show.bs.collapse', function () {
        var $_icon = $(this).prev('.instance-actions').find('.fa-angle-down');
        $_icon.removeClass('fa-angle-down').addClass('fa-angle-up');
    }).on('hide.bs.collapse', function () {
        var $_icon = $(this).prev('.instance-actions').find('.fa-angle-up');
        $_icon.removeClass('fa-angle-up').addClass('fa-angle-down');
    });

    //	No clicks allowed on disabled stuff
    $('body').on('click', '.disabled', function (e) {
        e.preventDefault();
    });

    //	All toolbar button clicks go here...
    $('.panel-toolbar > button').on('click', function (e) {
        e.preventDefault();
        _processAction($(this));
    });

    //  Set the data-instance-id on btn-import-instance when an import is chosen
    $('select#import-id').on('change', function (e) {
        $('#btn-import-instance').data('instance-id', $(this).find(':selected').data('instance-id'));
    });

    window.setTimeout(_checkProgress, _dso.statusCheckFrequency);
    _closeAlert('.alert-dismissable', _dso.alertHideTimeout);
});
