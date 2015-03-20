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
    <title>DreamFactory - Free Hosted Edition</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="utf-8">
    <?php echo $_css; ?>
    <?php echo $_scripts; ?>
    <link href="/css/default.css" rel="stylesheet">
    <link href="/css/main.css" rel="stylesheet">
    <link href="/css/component.css" rel="stylesheet">
    <link href="/css/df.dashboard.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="/img/logo-48x48.png">
</head>
<body>
<div id="wrap">
    <nav class="navbar navbar-default navbar-inverse navbar-fixed-top dsp-header">
        <div class="navbar-header">
            <button data-target=".navbar-collapse" data-toggle="collapse" class="navbar-toggle" type="button">
                <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span>
                <span class="icon-bar"></span> <span class="icon-bar"></span>
            </button>
            <div class="brand-wrap" style="margin-top: -10px;">
                <img src="/img/logo-32x32.png" alt="" />

                <div class="pull-left">
                    <a href="#" class="navbar-brand dashboard-title">Dashboard</a> <br />
                    <small>DSP Free Hosted Edition</small>
                </div>
            </div>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li>
                    <a href="https://www.dreamfactory.com/developers" target="_blank">Developer Resources</a>
                </li>
                <li class="pull-right">
                    <a href="/web/logout">Logout</a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="inner-content"><?php echo $content; ?></div>
</div>

<div id="footer">
    <div class="container">
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
            <span class="pull-left hidden-xs hidden-sm">Licensed under the <a target="_blank"
                    href="http://www.apache.org/licenses/LICENSE-2.0">Apache License v2.0</a>
            </span>
            <span class="pull-right">&copy; DreamFactory Software, Inc. 2012-<?php echo date( 'Y' ); ?>.&nbsp;All Rights Reserved.
                <small style="margin-left: 5px;font-size: 9px;">(v1.2.5)</small>
            </span>
        </p>
    </div>
</div>
<?php echo $_endScripts; ?>
<script src="/js/app.jquery.js"></script>
<script src="/js/df.dashboard.js"></script>
</body>
</html>
