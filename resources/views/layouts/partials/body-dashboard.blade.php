@include('layouts.partials.navbar')

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-3 col-md-3 sidebar">
            @include('layouts.partials.sidebar')
        </div>

        <div id="content" class="col-sm-9 col-md-9 main">
            @yield('inner-content')
        </div>
    </div>
</div>

<div class="loading-content" style="display: none;"><img src="/img/img-loading.gif" class="loading-image" alt="Loading..." /></div>
