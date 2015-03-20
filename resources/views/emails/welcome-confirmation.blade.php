@extend('email.layout');

@section('headTitle')
    DreamFactory Enterprise&trade; : Welcome
@stop

@section('contentHeader')
    Welcome to DreamFactory!
@stop

@section('contentBody')
    <p>
        {{ $firstName }},</p>

    <p>
        Welcome to the DreamFactory community! We're excited to have you join our growing developer community and want to help you succeed, in any way we can.</p>

    <p>
        Before you can start exploring the RESTful world of DreamFactory, you need to confirm your email address. To do so, click the link below and you'll be all set.
    </p>

    <p>
        <a href="{{ $confirmationUrl }}"
           title="Click to confirm your email address">{{ $confirmationUrl }}</a>
    </p>

    <p>
        If you've got any questions, feel free to drop us a line at <a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a>
    </p>

    <p>
        Have a great day!<br /> The Dream Team
    </p>
@stop
