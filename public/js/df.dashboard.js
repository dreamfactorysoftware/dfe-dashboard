/**
 * Our global options
 */
var _options = {
    alertHideDelay: 10000, notifyDiv: 'div#request-message', ajaxMessageFadeTime: 6000, tour: [null, null, null]
};
/**
 * Global settings
 * @type {{}}
 * @private
 */
var _dso = {
    controlForm: null, statusCheckFrequency: 30000, alertHideTimeout: 30000
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
var _closeAlert = function(selector, delay, show) {
    var $_alert = $(selector).not('.alert-fixed');

    if ($_alert.length) {
        show && $(selector).removeClass('hide').show();

        if (!$_alert.hasClass('alert-fixed')) {
            window.setTimeout(function() {
                    $_alert.alert('close')
                }, delay
            );
        }
    }
};
/**
 * Generates the necessary form data to push an action request to the console
 * @param {jQuery} [$element]
 * @param {jQuery} [$form]
 * @private
 */
var _processAction = function($element, $form) {
    //  Skip this for uploads...
    if ($form && 'form-upload' == $form.attr('id')) {
        return $form.submit();
    }

    var _id = $element.data('instance-id'), _extra = $element.data('instance-href');
    var _action = $element.data('instance-action');

    switch (_action) {
        case 'create':
            if (_validator && !_validator.valid()) {
                return;
            }

            _id = $('input#instance-id').val();

            if (!_id || !_id.length) {
                alert('Invalid instance name.');
            }
            break;
        case 'import':
            _id = $('input[name="instance-id"]', $form).val();
            _extra = $('input[name="snapshot-id"]', $form).val();
            break;
        default:
            $element.addClass('disabled').prop('disabled', true);
            break;
    }

    var _result = _makeRequest(_id, _action, _extra || null);

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
var _makeRequest = function(id, action, href) {

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
            //  Nothing...
            break;

        default:
            alert('Invalid command "' + action + '"');
            return;
    }

    $('input[name="id"]', _dso.controlForm).val(id);
    $('input[name="extra"]', _dso.controlForm).val(href);
    $('input[name="control"]', _dso.controlForm).val(action);

    return _dso.controlForm.submit();
};

/**
 * Initializes the tour by tab
 * @param tab
 */
var initTour = function(tab) {
    switch (tab) {
        case 0:
            if (!_options.tour[tab]) {
                _options.tour[tab] = new Tour({
                        name:     'dfe-dashboard-tour-' + tab,
                        template: '<div class="popover tour"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div><div class="popover-navigation"><button class="btn btn-sm btn-warning" data-role="prev"><i class="fa fa-fw fa-arrow-circle-left"></i> Prev</button><span data-role="separator">|</span><button class="btn btn-sm btn-warning" data-role="next">Next <i class="fa fa-fw fa-arrow-circle-right"></i></button><button class="btn btn-sm btn-warning" data-role="end">End tour</button></div></div>',
                        steps:    [
                            {
                                element:   '#new-instance input#instance-id',
                                title:     'New Instance Name',
                                content:   'Choose a name for your new DreamFactory&trade; instance, and enter it here',
                                placement: 'left'
                            },
                            {
                                element:   '#new-instance #upload-file',
                                title:     'Upload a package',
                                content:   'You may also upload an existing DreamFactory&trade;-created package file to be installed automatically on your new instance',
                                placement: 'left'
                            }
                        ]
                    }
                );
                _options.tour[tab].init();
            }
            break;

        case 1: //  New from Export
            if (!_options.tour[tab]) {
                _options.tour[tab] = new Tour({
                        name:     'dfe-dashboard-tour-' + tab,
                        template: '<div class="popover tour"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div><div class="popover-navigation"><button class="btn btn-sm btn-warning" data-role="prev"><i class="fa fa-fw fa-arrow-circle-left"></i> Prev</button><span data-role="separator">|</span><button class="btn btn-sm btn-warning" data-role="next">Next <i class="fa fa-fw fa-arrow-circle-right"></i></button><button class="btn btn-sm btn-warning" data-role="end">End tour</button></div></div>',
                        steps:    [
                            {
                                element:   '#import-instance #import-id',
                                title:     'Have an existing export?',
                                content:   'We keep a backup of all your exported instances, choose one to instantly restore it',
                                placement: 'left'
                            },
                            {
                                element:   '#import-instance #upload-file',
                                title:     'Upload your own!',
                                content:   'No backups available? You can upload any DreamFactory&trade; export and restore it on this system',
                                placement: 'left'
                            },
                            {
                                element:   '#import-instance #instance-id',
                                title:     'Name your instance',
                                content:   'If you have uploaded your own backup, you can choose a new name for your instance here',
                                placement: 'left'
                            }
                        ]
                    }
                );
                _options.tour[tab].init();
            }
            break;

        case 2: //  New from package
            break;
    }

    if (_options.tour[tab]) {
        _options.tour[tab].start();
    }
};

/**
 * DR
 */
jQuery(function($) {
        //	Reusables...
        var $_toolbars = $('div[id^="instance-toolbar-"]');

        //	Set form if not already
        if (!_dso.controlForm) {
            _dso.controlForm = $('form#_dsp-control');
        }

        //	Open/close toolbar
        $_toolbars.on('show.bs.collapse', function() {
                var $_icon = $(this).prev('.instance-actions').find('.fa-angle-down');
                $_icon.removeClass('fa-angle-down').addClass('fa-angle-up');
            }
        ).on('hide.bs.collapse', function() {
                var $_icon = $(this).prev('.instance-actions').find('.fa-angle-up');
                $_icon.removeClass('fa-angle-up').addClass('fa-angle-down');
            }
        );

        //	No clicks allowed on disabled stuff
        $('body').on('click', '.disabled', function(e) {
                e.preventDefault();
            }
        );

        //	All toolbar button clicks go here...
        $('.panel-toolbar').on('click', 'button', function(e) {
                e.preventDefault();
                _processAction($(this));
            }
        );

        //  Set the data-instance-id on btn-import-instance when an import is chosen
        $('select#import-id').on('change', function(e) {
                var $_form = $('#form-import'), $_selected = $(this).find(':selected');
                $_form.find('input[name="instance-id"]').val($_selected.data('instance-id'));
                $_form.find('input[name="snapshot-id"]').val($_selected.val());
            }
        );

        //  Nice cursor on logo
        $('.navbar-brand').css({cursor: 'default'});

        /** Clear any alerts after configured time */
        _closeAlert('.alert-dismissable', _dso.alertHideTimeout);
    
        /** Handle the help stuff */
        $('#instance-create-tabs').find('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                var _tab = e.target;
                _tab && initTour($(_tab).data('tab-id'));
            }
        );

        initTour(0);
    }
);
