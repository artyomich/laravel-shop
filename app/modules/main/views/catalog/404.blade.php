@extends('layouts.' . ($isAjax ? 'post' : 'default'))

@section('content')

    <!-- CATALOG -->
    <nav class="navbar navbar-catalog row">
        {{ \modules\main\widgets\Categories::widget(['categoryAlias' => $categoryAlias]) }}
    </nav>
    <!-- /CATALOG -->
    <!-- CONTENT -->
    <section class="col-lg-9 col-md-9 col-sm-12 content-tires">
        <h1 class="font-bold">Товар не найден</h1>
        <a href="/" class="btn btn-primary">На главную</a>
    </section>
    <!-- /CONTENT -->
    <div class="footer"></div>


@stop