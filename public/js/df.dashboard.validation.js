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
var _validator, _importValidator;

jQuery(function($) {
        var VALIDATION_CONFIG = {
            submitHandler:  function(form) {
                _processAction($(form).find('button[type="submit"]'), $(form));
            },
            ignoreTitle:    true,
            errorClass:     'help-block has-error',
            errorElement:   'p',
            errorPlacement: function(error, element) {
                error.appendTo($(element).closest('.form-group')).addClass('col-md-offset-2 col-md-10');
            },
            highlight:      function(element, errorClass) {
                $(element).closest('.form-group').find('p.help-block').hide();
                $(element).closest('.form-group').removeClass('has-success has-feedback').addClass('has-error has-feedback');
            },
            unhighlight:    function(element, errorClass) {
                $(element).closest('.form-group').removeClass('has-error has-feedback').addClass('has-success has-feedback');
                $(element).closest('.form-group').find('p.help-block').show();
            }
        };

        var VALIDATION_RULES = {
            rules: {
                'instance-id': {
                    required:     true,
                    minlength:    3,
                    maxlength:    64,
                    alphanumeric: true
                }
            }
        };

        _validator = $('#form-create').validate($.extend({}, VALIDATION_CONFIG, VALIDATION_RULES));

        var IMPORT_VALIDATION_RULES = {
            rules:    {
                'import-id':   {
                    required: function(element) {
                        return ($('import-id').data('import-count') > 1 && !$('#import-id').val()) || !$('#upload-file').val();
                    }
                },
                'upload-file': {
                    required: function(element) {
                        return $('import-id').data('import-count') == 1 || !$('#import-id').val();
                    }
                },
                'instance-id': {
                    required:     true,
                    minlength:    3,
                    maxlength:    64,
                    alphanumeric: true
                }
            },
            messages: {
                'import-id':   {
                    required: 'You must select an existing export or upload your own.'
                },
                'upload-file': {
                    required: 'You must upload an export or choose an existing one.'
                },
                'instance-id': {
                    required: 'An Instance name is required.',
                    alphanumeric: 'Instance names may contain only letters, numbers, and underscores.'
                }
            }
        };

        _importValidator = $('#form-import').validate($.extend({}, VALIDATION_CONFIG, VALIDATION_RULES, IMPORT_VALIDATION_RULES));
    }
);
