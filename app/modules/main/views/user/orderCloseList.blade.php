@extends('layouts.' . ($isAjax ? 'post' : 'default'))

@section('content')
    <h3>Список отгрузок</h3>
    {{ \modules\main\widgets\OrderList::widget(['orders'=>$orders]) }}
@stop