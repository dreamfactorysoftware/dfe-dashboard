/**
 * app.jquery.js
 * The file contains client-side functions that are global to the entire application.
 */
/**
 * Our global options
 */
var _options = {
    alertHideDelay:      10000,
    notifyDiv:           'div#request-message',
    ajaxMessageFadeTime: 6000
};
/**
 * Initialize any buttons and set fieldset menu classes
 */
$(function () {
    $('.navbar-brand').css({cursor: 'default'});

    /**
     * Clear any alerts after configured time
     */
    if (_options.alertHideDelay) {
        window.setTimeout(function () {
            $('div.alert.alert-dismissable').not('.alert-fixed').fadeTo('fast', 0).slideUp('fast', function () {
                $(this).remove();
            });
        }, _options.alertHideDelay);
    }
});
