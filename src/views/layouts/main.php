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
 * @var string        $_portalApiKey
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="/img/logo-48x48.png">

    <title>DreamFactory - Enterprise Dashboard</title>

    <?php echo $_css; ?>
    <?php echo $_scripts; ?>

    <link href="/css/style.css" rel="stylesheet">
    <link href="/css/df.dashboard.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries --><!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script><![endif]-->
</head>
<body>
<div class="navbar navbar-default navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <div class="brand-wrap" style="margin-top: -10px;">
                <img src="/img/logo-32x32.png" alt="" />

                <div class="pull-left">
                    <a href="#dashboard" class="navbar-brand dashboard-title">Dashboard</a> <br />
                    <small>DSP Free Hosted Edition</small>
                </div>
            </div>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li class=""><a href="https://www.dreamfactory.com/developers/" target="_blank">Developer Resources</a></li>
                <li><a href="/web/logout">Logout</a></li>
            </ul>
        </div>
    </div>
</div>

<!-- Begin page content -->
<div class="container-fluid">
    <?php echo $content; ?>
</div>

<div class="footer">
    <div class="container-fluid">
        <div class="social-links pull-right">
            <ul class="list-inline">
                <li>
                    <a target="_blank"
                        href="http://facebook.com/dfsoftwareinc"><i class="fa fa fa-facebook-square fa-2x"></i></a>
                </li>
                <li>
                    <a target="_blank"
                        href="https://twitter.com/dfsoftwareinc"><i class="fa fa-twitter-square fa-2x"></i></a>
                </li>
                <li>
                    <a target="_blank"
                        href="http://dreamfactorysoftware.github.io/"><i class="fa fa-github-square fa-2x"></i></a>
                </li>
            </ul>
        </div>
        <div class="clearfix"></div>
        <p>
            <span class="pull-left hidden-xs hidden-sm">DreamFactory Enterprise&trade; Dashboard <small style="margin-left: 5px;font-size: 9px;">(v1.2.6)</small></span>
            <span class="pull-right">&copy; DreamFactory Software, Inc. 2012-<?php echo date(
                    'Y'
                ); ?>.&nbsp;All Rights Reserved.</span>
        </p>
    </div>
</div>

<?php echo $_endScripts; ?>
<script src="/js/app.jquery.js"></script>
<script src="/js/df.dashboard.js"></script>
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script src="/js/ie10-viewport-bug-workaround.js"></script>
</body>
</html>
