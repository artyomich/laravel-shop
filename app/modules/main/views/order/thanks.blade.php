@extends('layouts.' . ($isAjax ? 'post' : 'default'))

@section('content')

<h1>Благодарим за покупку!</h1>
<p>Ваш заказ N{{ $model->id }} поступил в обработку. Наш менеджер свяжется с вами в ближайшее время.</p>
<a href="/">Вернуться на сайт</a>

<div class="footer"></div>
@stop