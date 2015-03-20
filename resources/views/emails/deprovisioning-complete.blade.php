@extend('email.layout');

@section('headTitle')
    DreamFactory Enterprise&trade; : Shutdown Request
@stop

@section('contentHeader')
    <h3>Your instance has been shut down</h3>
@stop

@section('contentBody')
    <div>
        <p>
            {{ $firstName }},
        </p>

        <div>
            {{ $emailBody }}
        </div>

        <p>
            If you've got any questions, feel free to shoot us an email at <a
                    href="mailto:{{ $supportEmail or "support@dreamfactory.com" }}">{{ $supportEmail or "support@dreamfactory.com" }}</a>
        </p>

        <p>
            Have a great day!<br /> The Dream Team
        </p>
    </div>
@stop
