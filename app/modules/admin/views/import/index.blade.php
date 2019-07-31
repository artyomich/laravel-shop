@extends('layouts.' . ($isAjax ? 'post' : 'admin'))

@section('content')

{{--*/
use helpers\Html;
/*--}}

@if(Session::has('success'))
<div class="alert alert-success">{{ Session::get('success'); }}</div>
@endif

@if(Session::has('error'))
<div class="alert alert-danger">{{ Session::get('error'); }}</div>
@endif

{{--*/ $form = \widgets\ActiveForm::begin([
'options' => ['enctype' => 'multipart/form-data']
]) /*--}}
<div class="box">
    <div class="box-header">
        <div class="box-title">Импорт из 1C</div>
    </div>
    <div class="box-body table-responsive">
        {{ Html::fileInput('file', '') }}<br/>
        {{ Html::submitButton('Импортировать', ['class' => 'btn btn-primary']) }}
    </div>
</div>
{{ $form->end() }}
@stop