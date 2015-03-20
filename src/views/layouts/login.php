<?php
/**
 * @var WebController $this
 * @var string        $content
 * @var bool          $_enablePushMenu
 * @var string        $_css
 * @var string        $_scripts
 * @var string        $_endScripts
 * @var string        $_route
 * @var string        $_dspList
 */
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>DSP Free Hosted Edition | Who goes there?</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset="utf-8">
	<?php echo $_css; ?>
	<?php echo $_scripts; ?>
	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
	<script src="/js/html5shiv.js"></script>
	<script src="/js/respond.min.js"></script>
	<![endif]-->
	<link href="/css/main.css" rel="stylesheet">
	<link href="/css/login.css" rel="stylesheet">
	<link rel="icon" type="image/png" href="/img/logo-48x48.png">
</head>
<body>
<?php echo $content; ?>
</body>
</html>
