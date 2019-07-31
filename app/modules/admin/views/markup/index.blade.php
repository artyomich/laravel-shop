@extends('layouts.' . ($isAjax ? 'post' : 'admin'))

@section('content')

    @use('helpers\Html')
    @use('helpers\ArrayHelper')




    <?php
    $form = \widgets\ActiveForm::begin([
    'action' => '/admin/markup/',
    'options' => [],
    'method' => 'post',
    ]);
    ?>
    {{ $form->field($model, 'value')->input('text', ['class' => 'col-sm-1'])->label(false)}}
    <br/>
    <br/>
    {{ Form::submit('Сохранить' , array('class' => 'btn btn-success top-buffer bottom-buffer'))}}
    {{ $form->end() }}

@stop