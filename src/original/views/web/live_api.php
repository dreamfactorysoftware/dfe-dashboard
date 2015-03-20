<?php
/**
 * Created by PhpStorm.
 * User: jablan
 * Date: 10/3/13
 * Time: 10:32 PM
 */
//	Imports
?>
<iframe id="live-api" scrolling="no" seamless="seamless" src="https://dsp-sandman1.cloud.dreamfactory.com/public/admin/swagger/index.html">/</iframe>
<script type="text/javascript">
jQuery(function($) {
	var $_frame = $('#live-api');

	$(window).resize(function(e) {
		var _height = $(window).height() - $('nav.navbar').height() - $('#footer').height();
		$('#live-api').width($('#wrap').width() - 60/*scrollbar*/).height(_height);
	});

	$_frame.load(function() {
		var $_contents = $_frame.contents().find('html');

		$_contents.on('resize',function() {
			$_frame.css({ height: $(this).outerHeight(true) });
		}).trigger('resize');
	});
});
</script>
