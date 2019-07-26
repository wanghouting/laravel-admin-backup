<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ Admin::title() }} @if($header) | {{ $header }}@endif</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    {!! Admin::css() !!}

    <script src="{{ Admin::jQuery() }}"></script>

    @if(method_exists(\Encore\Admin\Admin::class,'headerJs'))
    {!! Admin::headerJs() !!}
    @endif
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>.box{border: none}</style>
</head>

<body class="hold-transition {{config('admin.skin')}} {{join(' ', config('admin.layout'))}}">
<div class="wrapper">

    <div class="" id="pjax-container">
        <div id="app">
        @yield('content')
        </div>
        {!! Admin::script() !!}
    </div>

</div>

<script>
    function LA() {}
    LA.token = "{{ csrf_token() }}";
</script>

<!-- REQUIRED JS SCRIPTS -->
{!! Admin::js() !!}

</body>
</html>
