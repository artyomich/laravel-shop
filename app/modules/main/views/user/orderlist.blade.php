@extends('layouts.' . ($isAjax ? 'post' : 'default'))

@section('content')
    <h3>Список заказов</h3>
    {{ \modules\main\widgets\OrderList::widget(['orders'=>$orders]) }}
@stop