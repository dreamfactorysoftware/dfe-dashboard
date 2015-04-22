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
	ajaxMessageFadeTime: 6000,
	enablePushMenu:      false
};
/**
 * Initialize any buttons and set fieldset menu classes
 */
$(function() {
	if (_options.enablePushMenu) {
		$(document.body).on('click', function(e) {
			if (0 != $('.cbp-spmenu-open').length) {
				$('.cbp-spmenu-push').toggleClass('cbp-spmenu-push-toright');
				$('#db-menu').toggleClass('cbp-spmenu-open');
			}
		});
	} else {
		$('a.navbar-brand').css({cursor: 'default'});
	}

	/**
	 * Clear any alerts after configured time
	 */
	if (_options.alertHideDelay) {
		window.setTimeout(function() {
			$('div.alert.alert-dismissable').not('.alert-fixed').fadeTo('fast', 0).slideUp('fast', function() {
				$(this).remove();
			});
		}, _options.alertHideDelay);
	}

	/**
	 * Menu Toggle
	 */
	if (_options.enablePushMenu) {
		$('a.navbar-brand').on('click', function(e) {
			$('.cbp-spmenu-push').toggleClass('cbp-spmenu-push-toright');
			$('#db-menu').toggleClass('cbp-spmenu-open');
			e.stopPropagation();
		});
	}
});
