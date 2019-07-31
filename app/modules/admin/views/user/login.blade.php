<!DOCTYPE html>
<html class="bg-black">
<head>
    <meta charset="UTF-8">
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

    <title>{{ $title }}</title>

    @section('styles')
    {{ HTML::style(URL::asset('packages/bootstrap/dist/css/bootstrap.min.css')) }}
    {{ HTML::style(URL::asset('styles/admin/AdminLTE.css')) }}
    @show

    @section('scripts')
    {{ HTML::script(URL::asset('packages/jquery/jquery.min.js')) }}
    {{ HTML::script(URL::asset('packages/bootstrap/dist/js/bootstrap.min.js')) }}
    @show

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
</head>
<body class="bg-black">
<div class="form-box" id="login-box">
    <div class="header">{{ $title }}</div>
    {{ Form::open(array('class' => 'form-signin')) }}
    <div class="body bg-gray">
        @if (!$errors->isEmpty())
        <div class="">
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
                @endforeach
            </div>
        </div>
        @endif

        <div class="form-group">
            {{ Form::text('email', null, array('class' => 'form-control', 'placeholder' => 'Логин', 'autofocus')) }}
        </div>
        <div class="form-group">
            {{ Form::password('password', array('class' => 'form-control', 'placeholder' => 'Пароль')) }}
        </div>
        {{--<div class="form-group">
            <label class="text-">{{ Form::checkbox('remember-me', 1, array('class' => 'form-control')) }} Запомнить
                меня</label>
        </div>--}}
    </div>
    <div class="footer">
        {{ Form::submit('Войти', array('class' => 'btn bg-olive btn-block')) }}
    </div>
    {{ Form::close() }}
</div>
</div>
</body>
</html>