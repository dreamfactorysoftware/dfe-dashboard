<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>@yield('headTitle')</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<style type="text/css">

		.ExternalClass * {
			line-height: 100%;
		}

		@media screen and (max-width: 480px) {
			.desktopSection110 {
				display: none !important;
			}

			table[class=emailwrapto100pct], img[class=emailwrapto100pct] {
				display: block !important;
				width:   100% !important;
			}
		}
	</style>
	<!-- start padding for iOS -->    <!-- end padding for iOS -->
</head>
<body style="padding:0; margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:100%; background-color:#ffffff;" bgcolor="#ffffff">
<table class="desktopSection110" width="600" border="0" cellspacing="0" cellpadding="0" align="center" bgcolor="#ffffff">
	<!-- Left Header -->
	<tr>
		<td style="line-height:10px;" colspan="3"><img style="display:block;"
													   src="{{ asset('/img/spacer.gif') }}"
													   width="600"
													   height="10"
													   alt=""
													   border="0" /></td>
	</tr>
	<tr>
		<td align="left" colspan="3"><a name="Logo"
										href="http://www.dreamfactory.com/"
										target="_blank"><img style="display:block;"
															 src="{{ asset('/img/logo-dreamfactory-default.png') }}"
															 width="215"
															 height="29"
															 alt="DreamFactory"
															 border="0" /></a></td>
	</tr>
	<!-- End Left Header -->

	<tr>
		<td style="line-height:1px;"><img style="display:block;"
										  src="{{ asset( '/img/spacer.gif') }}"
										  width="10"
										  height="1"
										  alt=""
										  border="0" /></td>
		<td valign="top" align="left">
			@yield('content')
		</td>

		<td style="line-height:1px;"><img style="display:block;"
										  src="{{ asset('/img/spacer.gif') }}"
										  width="10"
										  height="1"
										  alt=""
										  border="0" /></td>
	</tr>

	<tr>
		<td align="center" style="line-height:1px;" colspan="3"><img style="display:block;"
																	 src="{{ asset('/img/gray-line.gif') }}"
																	 width="580"
																	 height="1"
																	 alt=""
																	 border="0" /></td>
	</tr>

	<tr>
		<td valign="top" align="left" colspan="3">
			<span style="font-size:26px; line-height:28px; font-family:Avenir, Arial, Helvetica, sans-serif; color:#000000;">&nbsp;</span></td>
	</tr>
	<tr>
		<td valign="top" align="left" colspan="3">
			<table width="142" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td valign="top"><a name="Facebook" href="http://www.facebook.com/dfsoftwareinc" target="_blank"><img style="display:block;"
																														  src="{{ asset('/img/facebook.gif') }}"
																														  alt="Facebook"
																														  width="44"
																														  height="44"
																														  border="0" /></a></td>
					<td valign="top"><img style="display:block;"
										  src="{{ asset('social_divider.gif') }}"
										  alt=""
										  width="1"
										  height="44"
										  border="0" /></td>
					<td valign="top"><a name="Twitter" href="http://twitter.com/dfsoftwareinc/" target="_blank"><img style="display:block;"
																													 src="{{ asset('/img/twitter.gif') }}"
																													 alt="Twitter"
																													 width="52"
																													 height="44"
																													 border="0" /></a></td>
					<td valign="top"><img style="display:block;"
										  src="{{ asset('social_divider.gif') }}"
										  alt=""
										  width="1"
										  height="44"
										  border="0" /></td>
					<td valign="top"><a name="GitHub" href="https://github.com/dreamfactorysoftware/" target="_blank"><img style="display:block;"
																														   src="{{ asset('/img/icon-github-48x48.png') }}"
																														   alt="GitHub"
																														   width="44"
																														   height="44"
																														   border="0" /></a></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="3"><img style="display:block;" src="{{ asset('/img/spacer.gif') }}" width="1" height="20" alt="" border="0" /></td>
	</tr>
	<tr>
		<td style="line-height:1px;"><img style="display:block;"
										  src="{{ asset('/img/spacer.gif') }}"
										  width="10"
										  height="1"
										  alt=""
										  border="0" /></td>
		<td valign="top"
			align="left"><span style="font-size:11px; line-height:16px; font-family:Avenir, Arial, Helvetica, sans-serif; color:#999999;">This message was sent to {{ $user->email_addr_text }}
				<br />
If you would like to update your email address, please&nbsp;<a name="UserProfile"
															   href="{{ url( '/user/profile') }}"
															   target="_blank"
															   style="color:#999999;">click&nbsp;here</a>.<br />

					Visit <a name="UserSettings"
							 href="{{ url( '/user/settings') }}"
							 target="_blank"
							 style="color:#999999;">alert preferences</a> to manage your email and mobile alerts.

		<br />&nbsp;<br />
				&copy;2012&mdash;{{ date('Y') }} DreamFactory Software, Inc. | All Rights&nbsp;Reserved.<br />
DreamFactory Software, Inc. 1999 Bascom Avenue, Suite 928, Campbell, CA&nbsp;95008<br />
<a name="Privacy"
   href="http://www.dreamfactory.com/privacy/"
   target="_blank"
   style="color:#999999;">Privacy Policy</a> | <a name="Terms"
												  href="http://www.dreamfactory.com/terms"
												  target="_blank"
												  style="color:#999999;">Terms and&nbsp;Conditions</a></span></td>
		<td style="line-height:1px;"><img style="display:block;"
										  src="{{ asset('/img/spacer.gif') }}"
										  width="10"
										  height="1"
										  alt=""
										  border="0" /></td>
	</tr>
</table>
</body>
</html>