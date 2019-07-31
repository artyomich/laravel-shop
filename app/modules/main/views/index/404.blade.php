@extends('layouts.' . ($isAjax ? 'post' : 'default'))

@section('content')

        <!-- CATALOG -->
{{ \modules\main\widgets\FilterSimple::widget(['categoryAlias' => isset($category) ? $category->alias : '']) }}
        <!-- /CATALOG -->
<!-- CONTENT -->
<br/>
<section class="content-tires">
    <h1 class="font-bold">Страница не найдена</h1>

    <div class="row">
        <div class="col-xs-12 col-md-8">
            <img src="/img/404.png" class="img-thumbnail"/>
        </div>
        <div class="col-md-4">
            <p>
                Мы очень сожалеем, но видимо наша команда что-то не предусмотрела...
            </p>

            <p>
                Предлагаем вам вернуться на <a href="/">главную страницу</a> или воспользоваться формой поиска в верхней
                части экрана.
            </p>

            <p>
                В случае, если вы хотите что-то сообщить - <a href="#" data-toggle="modal" data-target="#SupportModal">напишите
                    нам</a>!
            </p>
        </div>
    </div>

</section>
<div class="clearfix"></div>
<!-- /CONTENT -->
<div class="footer"></div>


@stop