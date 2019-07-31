@extends('layouts.' . ($isAjax ? 'post' : 'default'))

@section('content')

<h1>Заказ N{{ $model->id }}</h1>
Состояние: {{ $model->getStatus() }}

<div class="footer"></div>
@stop