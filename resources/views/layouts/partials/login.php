<?php
/**
 * Expected variables:
 *
 * @var WebLoginForm  $model     The form model
 * @var boolean       $loginPost True if this load is a post-back
 * @var boolean       $success   True if post-back and login success
 * @var WebController $this
 * @var string        $header
 *
 * Optional variable:
 * @var string        $modelName Defaults to DreamLoginForm
 */
use DreamFactory\Yii\Utility\Validate;

$_message = null;

if ( !isset( $modelName ) )
{
    $modelName = $this->getLoginFormClass();
}

$modelName = basename( str_replace( '\\', '/', $modelName ) );

Validate::register(
    '#loginform',
    array(
        'ignoreTitle'  => true,
        'errorElement' => 'div',
        'rules'        => array(
            $modelName . '[email_addr_text]' => array(
                'required' => true,
                'email'    => true,
            ),
            $modelName . '[password_text]'   => array(
                'required'  => true,
                'minlength' => 5,
            ),
        ),
    )
);

Validate::register(
    '#recoverform',
    array(
        'ignoreTitle'  => true,
        'errorElement' => 'div',
        'rules'        => array(
            'name' => array(
                'required' => true,
                'email'    => true,
            ),
        ),
    )
);

$_errors = null;

if ( !isset( $loginPost ) )
{
    $loginPost = false;
}

if ( !isset( $success ) )
{
    $success = false;
}

if ( isset( $model ) )
{
    $_errors = $model->getErrors();

    if ( !empty( $_errors ) )
    {
        $_message
            = '<div class="alert alert-danger fade in" data-alert="alert"><strong>Nope.</strong>';

        foreach ( $_errors as $_error )
        {
            foreach ( $_error as $_value )
            {
                $_message .= '<p>' . $_value . '</p>';
            }
        }

        $_message .= '</div>';
    }
}
?>
<div id="loginbox">
    <div class="dsp-system-name">DreamFactory Enterprise Manager
        <div class="dsp-system-tagline">Dashboard</div>
    </div>

    <form id="form-login" method="POST" role="form">
        <input type="hidden" id="check-remember-ind" value="<?php echo $model->remember_ind; ?>">
        <div class="form-group normal_text logo-container"><h3><img src="/img/logo-login.png" alt="" /></h3></div>

        <?php echo $_message; ?>

        <div class="form-group">
            <label for="<?php echo $modelName; ?>_email_addr_text" class="sr-only">Email Address</label>

            <div class="input-group">
                <span class="input-group-addon bg_dg"><i class="fa fa-envelope fa-fw"></i></span>

                <input tabindex="1" class="form-control email required" autofocus type="email" id="<?php echo $modelName; ?>_email_addr_text"
                       name="<?php echo $modelName; ?>[email_addr_text]" placeholder="Email Address" />
            </div>
        </div>

        <div class="form-group">
            <label for="<?php echo $modelName; ?>_password_text" class="sr-only">Password</label>

            <div class="input-group">
                <span class="input-group-addon bg_ly"><i class="fa fa-lock fa-fw"></i></span>

                <input tabindex="2" class="form-control password required" id="<?php echo $modelName; ?>_password_text" placeholder="Password"
                       name="<?php echo $modelName; ?>[password_text]" type="password" />
            </div>
        </div>

        <div class="form-group">
            <div class="input-group remember-me">
                <span class="input-group-addon bg_db"><i class="fa fa-<?php echo !empty( $model->remember_ind ) ? 'check-'
                        : null; ?>circle-o fa-fw"></i></span>

                <input tabindex="3" class="form-control strong-disabled" id="<?php echo $modelName; ?>_remember_ind"
                       placeholder="<?php echo( $model->remember_ind ? null : 'Do Not ' ); ?>Keep Me Signed In" type="text"
                       disabled />
            </div>
        </div>

        <div class="form-buttons">
            <span class="pull-left"><button tabindex="4" type="button" class="flip-link btn btn-info" id="to-recover">Lost password?</button></span>
            <span class="pull-right"><button tabindex="3" type="submit" class="btn btn-success">Login</button></span>
        </div>
    </form>

    <!-- Password Recovery Form -->
    <form id="form-recover" target="_blank" action="https://www.dreamfactory.com/user/password" method="POST">
        <p class="normal_text">Enter your registered email address and instructions on how to reset your password will be sent to you.</p>

        <div class="form-group">
            <label for="name" class="sr-only">Email Address</label>

            <div class="input-group">
                <span class="input-group-addon bg_dg"><i class="fa fa-envelope fa-fw"></i></span>

                <input tabindex="1"
                       id="edit-name"
                       name="name"
                       class="form-control email required"
                       autofocus
                       type="email"
                       placeholder="Email Address" />
            </div>
        </div>

        <div class="form-buttons">
			<span class="pull-left">
				<button tabindex="3" type="button" class="flip-link btn btn-info" id="to-login">
                    <i class="fa fa-double-angle-left fa-fw"></i> Back to login
                </button>
			</span>
			<span class="pull-right">
				<button tabindex="2" type="submit" class="btn btn-success">Recover</button>
			</span>
        </div>
    </form>

</div>
<script type="text/javascript">
jQuery(function($) {
    var $_rememberMe = $('#check-remember-ind');
    var _remembered = ( 1 == $_rememberMe.val());
    var $_rememberHint = $('#<?php echo $modelName; ?>_remember_ind');

    $('#to-recover').on('click', function() {
        $('body').animate({"margin-top": "10%"});
        $("#form-login").slideUp();
        $("#form-recover").fadeIn();
    });

    $('#to-login').on('click', function() {
        $("#form-recover").fadeOut();
        $('body').animate({"margin-top": "20px"});
        $("#form-login").slideDown();
    });

    $('.input-group.remember-me').on('click', function(e) {
        e.preventDefault();
        var $_icon = $('i.fa', $(this));

        if (_remembered) {
            //	Disable
            _remembered = 0;
            $_icon.removeClass('fa-check-circle-o').addClass('fa-circle-o');
            $_rememberHint.attr({placeHolder: 'Do Not Keep Me Signed In'});
        } else {
            //	Enable
            _remembered = 1;
            $_icon.removeClass('fa-circle-o').addClass('fa-check-circle-o');
            $_rememberHint.attr({placeHolder: 'Keep Me Signed In'});
        }

        $_rememberMe.val(_remembered);
    });
});
</script>
