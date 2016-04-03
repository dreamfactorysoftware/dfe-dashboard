/*!
 * Copyright (c) 2014 - ∞ DreamFactory Software, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
var _validator, _importValidator;

jQuery(function($) {
        var VALIDATION_CONFIG = {
            submitHandler:  function(form) {
                _processAction($(form).find('button[type="submit"]'), $(form));
            },
            ignoreTitle:    true,
            errorClass:     'help-block',
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
                        return null == $("#upload-file").val();
                    }
                },
                'upload-file': {
                    required: function(element) {
                        return !$("#import-id").val();
                    }
                }
            },
            messages: {
                'import-id':   {
                    required: 'You must select an existing export or upload your own.'
                },
                'upload-file': {
                    required: 'You must upload an export or choose an existing one.'
                }
            }
        };

        _importValidator = $('#form-import').validate($.extend({}, VALIDATION_CONFIG, VALIDATION_RULES, IMPORT_VALIDATION_RULES));
    }
);
