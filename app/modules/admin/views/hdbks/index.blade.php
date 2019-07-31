@extends('layouts.' . ($isAjax ? 'post' : 'admin'))

@section('content')

@use('helpers\Html')

<div class="row">
    <div class="col-xs-12">
        <ul class="nav nav-tabs" role="tablist" id="ordersTabs">
            <li class="active">
                <a href="#hdbkRedirects" role="tab" data-toggle="tab">Таблица переадресаций</a>
            </li>
            <li>
                <a href="#hdbkErrors" role="tab" data-toggle="tab">404</a>
            </li>
            <li>
                <a href="#hdbkCities" role="tab" data-toggle="tab">Города</a>
            </li>
            <li>
                <a href="#hdbkEmployers" role="tab" data-toggle="tab">Сотрудники</a>
            </li>
            <li>
                <a href="#hdbkFilter" role="tab" data-toggle="tab">Фильтр</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="hdbkRedirects">
                <div class="box">
                    <div class="box-body table-responsive">
                        <a href="/admin/hdbkredirects/create/" class="btn btn-primary">
                            <i class="glyphicon glyphicon-plus-sign"></i>&nbsp;&nbsp;Добавить запись
                        </a>
                        {{ \widgets\GridView::widget(['model' => \models\Redirects::className()]) }}
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="hdbkErrors">
                <div class="box">
                    <div class="box-body table-responsive">
                        {{ \widgets\GridView::widget(['model' => \models\LogErrors::className()]) }}
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="hdbkCities">
                <div class="box">
                    <div class="box-body table-responsive">
                        <a href="/admin/hdbkcities/create/" class="btn btn-primary">
                            <i class="glyphicon glyphicon-plus-sign"></i>&nbsp;&nbsp;Добавить город
                        </a>

                        <div class="pull-right">
                            <a href="/admin/hdbkcities/acquiring/enable/" class="btn btn-default">
                                Включить эквайринг
                            </a>
                            <a href="/admin/hdbkcities/acquiring/disable/" class="btn btn-default">
                                Отключить эквайринг
                            </a>
                        </div>
                        {{ \widgets\GridView::widget(['model' => \models\Cities::className()]) }}
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="hdbkEmployers">
                <div class="box">
                    <div class="box-body table-responsive">
                        <a href="/admin/hdbkemployers/create/" class="btn btn-primary">
                            <i class="glyphicon glyphicon-plus-sign"></i>&nbsp;&nbsp;Добавить сотрудника
                        </a>

                        <p>
                            {{ \widgets\SortView::widget([
                                'model' => \models\Employers::className(),
                                'controller' => 'hdbkemployers'
                            ]) }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="hdbkFilter">
                <div class="box">
                    <div class="box-body table-responsive">
                        <a href="/admin/hdbkfilter/create/" class="btn btn-primary">
                            <i class="glyphicon glyphicon-plus-sign"></i>&nbsp;&nbsp;Добавить
                        </a>

                        {{ \widgets\GridView::widget(['model' => \models\Filter::className()]) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@stop