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
var _closeAlert = function(selector, delay, show) {
	var $_alert = jQuery(selector).alert();

	if (show) {
		jQuery(selector).removeClass('hide').show();
	}

	window.setTimeout(function() {
		$_alert.alert('close')
	}, delay);
};

/**
 *
 * @private
 */
var _checkProgress = function() {
	(function($) {

		$(".instance-heading-status .fa-spinner").each(function(index, item) {
			var $_form = $("form#_dsp-control");
			var _div = $(this).closest('a[data-toggle="collapse"]').attr('href');

			if (_div && _div.length) {
				var _id = $(_div).attr('id');
				var _parts = _id.split('___');

				if (3 == _parts.length) {
					$('input[name="id"]', $_form).val(_parts[2]);
					$('input[name="control"]', $_form).val('status');

					$.post('/status/' + _parts[2], $_form.serialize(), function(data) {
							   var $_inner = $("div#" + _id + ".panel-collapse .panel-body");

							   $(item).fadeOut('slow', function() {
								   $(item).fadeIn('fast');
							   });

							   if (data && ( 404 == data.code || 1 == data.deleted )) {
								   //	Delete one...
								   try {
//								_flasher($(item).closest('.accordion-group').find('.instance-heading'), true);
									   $(item).removeClass('fa-spinner fa-spin').addClass(data.icons[1]);
									   $(".dsp-icon i", $_inner).removeClass('fa-spinner fa-spin').addClass(data.icons[1]);
									   $(item).closest('.panel-dsp').fadeOut();
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
 * DR
 */
jQuery(function($) {
	if (!_dso.controlForm) {
		_dso.controlForm = $('form#_dsp-control');
	}

	/**
	 * Panel eye-candy
	 */
	$('#dsp_list').on('hide.bs.collapse show.bs.collapse shown.bs.collapse', function(e) {
		var $_element = $(e.target);
		var $_heading = $_element.prev();

		switch (e.type) {
			case 'hide':
				$_element.removeClass('panel-body-open');
				$_heading.removeClass('instance-heading-shown');
				$('.instance-heading-dsp-name i.fa-chevron-up', $_heading).removeClass('fa-chevron-up').addClass('fa-chevron-down');
				break;

			case 'show':
				$_element.addClass('panel-body-open');
				$_heading.addClass('instance-heading-shown');
				$('.instance-heading-dsp-name i.fa-chevron-down', $_heading).removeClass('fa-chevron-down').addClass('fa-chevron-up');
				break;

			case 'shown':
				$_element.find('form #dsp_name').focus();
				break;
		}
	});

	//noinspection FunctionWithInconsistentReturnsJS
	/**
	 * Dashboard control buttons
	 */
	$('span.dsp-controls').on('click', 'a[id^="dspcontrol___"]', function(e) {
		e.preventDefault();

		if ($(this).hasClass('disabled')) {
			return false;
		}

		var _id = $(this).attr('id');
		var _parts = _id.split('___');

		switch (_parts[1]) {
			case 'launch':
				window.open($(this).attr('href'));
				return;

			case 'delete':
				if (!confirm('Permanently delete DSP "' + _parts[2] + '"?')) {
					return;
				}
				break;
			case 'start':
				if (!confirm('Restart DSP "' + _parts[2] + '"?')) {
					return;
				}
				break;
			case 'stop':
				if (!confirm('Stop DSP "' + _parts[2] + '"?')) {
					return;
				}
				break;
			case 'help':
				break;
			case 'export':
				if (!confirm('Export DSP "' + _parts[2] + '"?')) {
					return;
				}
				break;
			case 'import':
				//				if ( !confirm('WARNING: Destructive Procedure. This may overwrite existing settings in this DSP.' + "\n\n" + 'Import to DSP "' + _parts[2] + '"?') ) {
				//					return;
				//				}

				$('input[name="id"]', _dso.controlForm).val(_parts[2]);
				$('input[name="control"]', _dso.controlForm).val('snapshots');
				$('body').css('cursor', 'wait');

				$.post('/web/index', _dso.controlForm.serialize(), function(data) {
					$('body').css('cursor', 'pointer');
					if (data) {
						$('#dsp-snapshot-list').html(data);
						$('form#_dsp-snapshot-control input[name="id"]').val(_parts[2]);
						$('#dsp-import-snapshot').modal('show');
					} else {
						alert('No snapshots available for this instance.');
					}
				}, 'html');
				return;

			default:
				alert('Invalid command "' + _parts['1'] + '"');
				return;
		}

		$('input[name="id"]', _dso.controlForm).val(_parts[2]);
		$('input[name="control"]', _dso.controlForm).val(_parts[1]);
		return _dso.controlForm.submit();
	});

	window.setTimeout(_checkProgress, _dso.statusCheckFrequency);
	_closeAlert('.system-alert', _dso.alertHideTimeout);
});
