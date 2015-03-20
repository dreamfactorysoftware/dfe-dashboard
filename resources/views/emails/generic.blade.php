@extends('emails.layout');

{{--

 This is a generic blade for generating emails.

 The following view data is required:

 $headTitle           The title of the email/page
 $firstName           The first name of the recipient
 $contentHeader       The callout/header of the email's body
 $emailBody           The actual guts of the email
 $supportEmail        The email address for customer support

--}}

@section('headTitle')
    DreamFactory Enterprise&trade; : {{ $headTitle }}
@stop

@section('contentHeader')
    {{ $contentHeader }}
@stop

@section('contentBody')
    <div style="padding: 10px;">
        <p>
            {{ $firstName }},
        </p>

        <div>
            {!! $emailBody !!}
        </div>

        <p>
            If you have any questions or feedback, feel free to shoot us an email at <a
                    href="mailto:{{ $supportEmail or "support@dreamfactory.com" }}">{{ $supportEmail or "support@dreamfactory.com" }}</a>
        </p>

        <p>
            <cite>-- The Dream Team</cite>
        </p>
    </div>
@stop
