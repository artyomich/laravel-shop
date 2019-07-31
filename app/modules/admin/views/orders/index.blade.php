@extends('layouts.' . ($isAjax ? 'post' : 'admin'))

@section('content')

@use('helpers\Html')

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-body">
                <ul class="nav nav-tabs" role="tablist" id="ordersTabs">
                    <li class="active">
                        <a href="#orderNew" role="tab" data-toggle="tab">
                            Новые <span class="badge alert-danger">{{ $newOrdersCount }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="#orderAccepted" role="tab" data-toggle="tab">
                            Принятые <span class="badge alert-info">{{ $myOrdersCount }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="#orderComplited" role="tab" data-toggle="tab">
                            Выполненные <span class="badge alert-success">{{ $completedOrdersCount }}</span>
                        </a>
                    </li>
                    {{--<li>
                        <a href="#orderDeleted" role="tab" data-toggle="tab">Удаленные</a>
                    </li>--}}
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="orderNew">
                        {{ \modules\admin\widgets\OrderGridView::widget(['status' => \models\Orders::STATUS_NEW, 'city_id' => $city->id]) }}
                    </div>
                    <div class="tab-pane" id="orderAccepted">
                        {{ \modules\admin\widgets\OrderGridView::widget(['status' => \models\Orders::STATUS_ACCEPTED, 'city_id' => $city->id]) }}
                    </div>
                    <div class="tab-pane" id="orderComplited">
                        {{ \modules\admin\widgets\OrderGridView::widget(['status' => \models\Orders::STATUS_COMPLETED, 'city_id' => $city->id]) }}
                    </div>
                    {{--<div class="tab-pane" id="orderDeleted">
                        {{ \modules\admin\widgets\OrderGridView::widget(['status' => 'D']) }}
                    </div>--}}
                </div>
            </div>
        </div>
    </div>
    {{--<div class="col-xs-3">
        {{ Html::a('<i class="glyphicon glyphicon-plus-sign"></i> Создать заказ', '/admin/orders/create',
            ['class' => 'btn btn-primary btn-block']) }}
    </div>--}}
</div>

@stop