@extends('emails.layout')

@section('content')
	<p><strong>{{ $user->email_addr_text }}</strong>,</p>

	<p>We received a request to reset your password. To reset your password and access your account, click the link below.</p>

	<p>
		<a href="{{ url('password/reset/'.$token) }}"
		   style="color:#4dc5e2; text-decoration:none;"
		   target="_blank"><span style="color: #4dc5e2; ">Reset password</span></a>
	</p>

	<p>The link resets your forgotten password and lets you create a new one. For your security, the link is only valid for the next 24 hours.</p>

	<p>
		If you did not request this change to your password, please report this email to us by completing our
		<a href="http://www.dreamfactory.com/company/contact"
		   style="color:#4dc5e2; text-decoration:none;"
		   target="_blank"><span style="color: #4dc5e2; ">Contact Form</span></a>.
	</p>

	<p>
		Thank you for using DreamFactory!
	</p>
@stop