<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <title>{{ $title }}</title>

    <link rel="shortcut icon" href="/favicon.ico?v=3"/>
    @section('styles')
    {{ HTML::style(URL::asset('packages/bootstrap/dist/css/bootstrap.min.css')) }}
    {{ HTML::style('//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css') }}
    {{ HTML::style(URL::asset('styles/admin/style.css')) }}
    @show

    @section('scripts')
    {{ HTML::script(URL::asset('packages/jquery/jquery.min.js')) }}
    {{ HTML::script(URL::asset('packages/jqueryui/jquery-ui.min.js')) }}
    {{ HTML::script(URL::asset('packages/bootstrap/dist/js/bootstrap.min.js')) }}
    {{ HTML::script(URL::asset('js/lte.js')) }}
    {{ HTML::script(URL::asset('js/admin.js')) }}
    {{ HTML::script(URL::asset('js/ckeditor/ckeditor.js')) }}
    {{ HTML::script(URL::asset('js/bootstrap-daterangepicker-master/moment.js')) }}
    {{ HTML::script(URL::asset('js/bootstrap-daterangepicker-master/daterangepicker.js')) }}
    @show

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
</head>
<body class="skin-black">
<!-- header logo: style can be found in header.less -->
<header class="header">
    <a href="/" class="logo">{{\Config::get('app.name')}}</a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </a>

        <div class="navbar-right">
            <ul class="nav navbar-nav">
                <!-- User Account: style can be found in dropdown.less -->
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="glyphicon glyphicon-user"></i>
                        <span>Профиль <i class="caret"></i></span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header bg-light-blue">
                            <img src="/img/admin/avatar{{ Sentry::getUser()->is_male ? 5 : 3 }}.png" class="img-circle" alt="User Image">

                            <p>
                                {{Sentry::getUser()->first_name}}
                                <small>Дата регистрации: {{ Sentry::getUser()->created_at }}</small>
                            </p>
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            {{--<div class="pull-left">
                                <a href="/admin/user/profile/" class="btn btn-default btn-flat">Профиль</a>
                            </div>--}}
                            <div class="pull-right">
                                <a href="/admin/user/logout/" class="btn btn-default btn-flat">Выйти</a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>
<div class="wrapper row-offcanvas row-offcanvas-left">
    <!-- Left side column. contains the logo and sidebar -->
    <aside class="left-side sidebar-offcanvas">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
            <!-- Sidebar user panel -->
            <div class="user-panel">
                <div class="pull-left image">
                    <img src="/img/admin/avatar{{ Sentry::getUser()->is_male ? 5 : 3 }}.png" class="img-circle" alt="User Image"/>
                </div>
                <div class="pull-left info">
                    <p>Здравствуйте, {{Sentry::getUser()->first_name}}!</p>
                    <a href="#"><i class="fa fa-circle text-success"></i> В сети</a>
                </div>
            </div>
            <!-- search form -->
            <!--<form action="#" method="get" class="sidebar-form">
                <div class="input-group">
                    <input type="text" name="q" class="form-control" placeholder="Search..."/>
                    <span class="input-group-btn">
                        <button type='submit' name='seach' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i></button>
                    </span>
                </div>
            </form>-->
            <!-- /.search form -->
            <!-- sidebar menu: : style can be found in sidebar.less -->
            <ul class="sidebar-menu">
                @foreach ($navigation as $item)
                <li
                @if ($item['link'] == '/' . Request::path() . '/') class="active" @endif
                @if (isset($item['items'])) class="treeview" @endif >
                <a href="{{$item['link']}}">
                    {{$item['label']}}
                    @if (isset($item['items'])) <i class="fa fa-angle-left pull-right"></i> @endif
                </a>
                @if (isset($item['items']))
                <ul class="treeview-menu">
                    @foreach ($item['items'] as $item)
                    <li><a href="{{$item['link']}}"><i class="fa fa-angle-double-right"></i> {{$item['label']}}</a></li>
                    @endforeach
                </ul>
                @endif
                </li>
                @endforeach
            </ul>
        </section>
        <!-- /.sidebar -->
    </aside>

    <!-- Right side column. Contains the navbar and content of the page -->
    <aside class="right-side">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                {{{$title}}}
            </h1>
            @if ($breadcrumbs)
            <ol class="breadcrumb">
                @if (count($breadcrumbs) > 1)
                @foreach ($breadcrumbs as $breadcrumb)
                @if (is_array($breadcrumb))
                <li><a href="{{{ $breadcrumb['link'] }}}">{{ $breadcrumb['label'] }}</a></li>
                @else
                <li class="active">{{{ $breadcrumb }}}</li>
                @endif
                @endforeach
                @endif
            </ol>
            @endif
        </section>

        <!-- Main content -->
        <section class="content">
            @yield('content')
        </section>
        <!-- /.content -->
    </aside>
    <!-- /.right-side -->
</div>
<!-- ./wrapper -->
</body>
</html>