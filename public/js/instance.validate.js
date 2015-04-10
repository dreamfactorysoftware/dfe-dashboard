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
jQuery(function($) {
	var VALIDATION_RULES = {
		ignoreTitle:    true,
		errorClass:     'help-inline',
		errorElement:   'span',
		errorPlacement: function(error, element) {
			error.appendTo($(element).closest('.form-group'));
		},
		highlight:      function(element, errorClass) {
			$(element).closest('.form-group').removeClass('has-success has-feedback').addClass('has-error has-feedback');
		},
		unhighlight:    function(element, errorClass) {
			$(element).closest('.form-group').removeClass('has-error has-feedback').addClass('has-success has-feedback');
		},
		rules:          {
			id: {
				required:  true,
				minlength: 3,
				maxlength: 64
			}
		}
	};

	var _validator = $('#form-provision').validate(VALIDATION_RULES);
});
