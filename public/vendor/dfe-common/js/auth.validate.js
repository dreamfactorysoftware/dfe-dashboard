var DefaultValidateOptions = {
    ignoreTitle:    true,
    errorClass:     'help-inline',
    errorElement:   'span',
    errorPlacement: function (error, element) {
        error.appendTo($(element).closest('.form-group'));
    },
    highlight:      function (element, errorClass) {
        $(element).closest('.form-group').removeClass('has-success has-feedback').addClass('has-error has-feedback');
    },
    unhighlight:    function (element, errorClass) {
        $(element).closest('.form-group').removeClass('has-error has-feedback').addClass('has-success has-feedback');
    },
    rules:          {}
};

/** doc ready */
jQuery(function ($) {
    var _rules = {
        email: {
            required: true,
            email:    true
        }
    };

    if ($('input[name="password"]').length) {
        _rules.password = {
            required:  true,
            minlength: 5
        };
    }

    if ($('input[name="password_confirmation"]').length) {
        _rules.password_confirmation = {
            required:  true,
            minlength: 5
        };
    }

    var _validator = $('form').validate($.extend({}, DefaultValidateOptions, _rules));
    var $_body = $('body'), _of = $_body.css('overflow');
});
