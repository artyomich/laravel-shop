@extends('layouts.' . ($isAjax ? 'post' : 'admin'))

@section('content')

@use('helpers\Html')

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-body">
                {{ \modules\admin\widgets\OrderGridView::widget() }}
            </div>
        </div>
    </div>
    {{--<div class="col-xs-3">
        {{ Html::a('<i class="glyphicon glyphicon-plus-sign"></i> Создать заказ', '/admin/orders/create',
            ['class' => 'btn btn-primary btn-block']) }}
    </div>--}}
</div>

@stop