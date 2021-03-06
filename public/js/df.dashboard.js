/*!
 * DreamFactory Enterprise(tm) User Dashboard
 * Copyright 2012-2102 DreamFactory Software, Inc. All Rights Reserved.
 * 
 * NOTICE: All information contained herein is, and remains the property of DreamFactory Software, Inc. and its
 * suppliers, if any. The intellectual and technical concepts contained herein are proprietary to DreamFactory
 * Software, Inc. and its suppliers and may be covered by U.S. and Foreign Patents, patents in process, and are
 * protected by trade secret or copyright law. Dissemination of this information or reproduction of this material is
 * strictly forbidden unless prior written permission is obtained from DreamFactory Software, Inc.
 */
/**
 * Our global options
 */
var _options = {
    alertHideDelay: 10000, notifyDiv: 'div#request-message', ajaxMessageFadeTime: 6000
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

var _dfeModal = function(title, body, closeBtn, confirmBtn){
    $dfeModal.find('.modal-title').text(title);
    $dfeModal.find('.modal-body').html(body);
    $dfeModal.find('button.btn-close').text(closeBtn);
    $dfeModal.find('button.btn-success').text(confirmBtn);
    $dfeModal.modal('show');
};

/**
 * Generates the necessary form data to push an action request to the console
 * @param {jQuery} [$element]
 * @param {jQuery} [$form]
 * @private
 */
var _processAction = function($element, $form) {
    //  Skip this for uploads...
    if ($form && 'form-import' == $form.attr('id') && $('input[name="upload-file"]', $form).val()) {
        return $form.submit();
    }

    if ($form && 'form-create' == $form.attr('id') && $('input[name="upload-package"]', $form).val()) {
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
            _extra = $('select[name="import-id"]', $form).val();
            break;
        default:
            $element.addClass('disabled').prop('disabled', true);
            break;
    }

    _makeRequest(_id, _action, _extra || null);

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

    $('input[name="id"]', _dso.controlForm).val(id);
    $('input[name="extra"]', _dso.controlForm).val(href);
    $('input[name="control"]', _dso.controlForm).val(action);

    switch (action) {
        case 'launch':
            window.top.open(href, id);
            return;

        case 'provision':
        case 'create':
            return _dso.controlForm.submit();
            break;

        case 'destroy':
        case 'deprovision':
        case 'delete':
            _dfeModal('Delete Instance ', 'Permanently delete instance "' + id + '"?', 'Cancel', 'Delete');
            break;

        case 'export':
            _dfeModal('Export Instance ', 'Do you want to export instance "' + id + '"?', 'Cancel', 'Export');

            break;

        case 'import':
            if(!$('#panel-body-'+ id).length){
                return _dso.controlForm.submit();
            } else {
                _dfeModal('Duplicate Instance Detected', 'It looks like you already have an active instance with the name "' + id + '". Importing will overwrite that instance. Proceed?', 'Cancel', 'Import');
            }
            break;

        default:
            alert('Invalid command "' + action + '"');
            return;
    }


    return;
};

/**
 * DR
 */

var $dfeModal;

jQuery(function($) {

        //	Reusables...
        var $_toolbars = $('div[id^="instance-toolbar-"]');

        $('[data-toggle="tooltip"]').tooltip();

        //	Set form if not already
        _dso.controlForm = _dso.controlForm || $('form#_dsp-control');

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

        $('#upload-file').change(function(e){
            //clear out any previous select entries..
            if($('#import-id').val()){
                $('#import-id, #instance-id').val('');
            }
        });

        $('#import-id').change(function(e){
            //clear out any previous file uploads..
            if($('#upload-file').val()){
                $('#upload-file').val('');
            }
            var $_selected = $("option:selected", $(this));
            $('input#instance-id').val($_selected.data('instance-id'));
            $('input#snapshot-id').val($_selected.val());
        });

        //	All instance panel toolbar button clicks go here...
        $('.panel-toolbar').on('click', 'button', function(e) {
                e.preventDefault();
                _processAction($(this));
            }
        );

        //  Nice cursor on logo
        $('.navbar-brand').css({cursor: 'default'});

        /** Clear any alerts after configured time */
        _closeAlert('.alert-dismissable', _dso.alertHideTimeout);

        //Create the modal object
        $dfeModal = $('.modal').clone().modal('hide');

        $dfeModal.on('click', '.btn-success', function(){
            $dfeModal.modal('hide');
            _dso.controlForm.submit();
        });

    }
);