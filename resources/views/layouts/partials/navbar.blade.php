@if (\Auth::check())
    <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-dashboard">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="/"><img src="{{ config('dfe.common.navbar-image') }}"></a>
            </div>

            <div class="collapse navbar-collapse navbar-right" id="navbar-dashboard">
                <ul class="nav navbar-nav">
                    <li><a href="http://www.dreamfactory.com/resources/"><i class="fa fa-fw fa-book"></i>Resources</a></li>
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            <i class="fa fa-fw fa-user"></i>
                            <span>{{ \Auth::user()->nickname_text ?: \Auth::user()->first_name_text }}</span>
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            @if(config('dashboard.allow-personalization',false))
                                <li class="dropdown-header">Dashboard Theme</li>
                                @foreach($themes as $_theme)
                                    <li><a href="#" name="select-theme-{{$_theme}}">{{$_theme}}</a></li>
                                @endforeach
                                <li class="divider" role="separator"></li>
                            @endif
                            <li><a href="/auth/logout"><i class="fa fa-fw fa-sign-out"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
@endif
