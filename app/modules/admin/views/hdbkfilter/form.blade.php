@extends('layouts.' . ($isAjax ? 'post' : 'admin'))

@section('content')
    @use('helpers\Html')

    {?
    $btnSubmit = $model->isNewRecord() ?
    Html::submitButton('Создать', ['class' => 'btn btn-success']) :
    Html::submitButton('Сохранить', ['class' => 'btn btn-primary']);

    $form = \widgets\ActiveForm::begin([
    'action' => $model->id ? ('/admin/hdbkfilter/update/' . $model->id) : ''
    ]);
    ?}

    @if (\Session::has('success'))
        <div class="alert alert-success">{{ \Session::get('success') }}</div>
    @endif

    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-xs-6">
                    {{ $form->field($model, 'alias') }}
                </div>
                <div class="col-xs-6">
                    {{ $form->field($model, 'source') }}
                </div>
            </div>

            {{ $form->field($model, 'description')->textArea() }}

            {{ $btnSubmit }}
        </div>
    </div>

    {{ $form->end() }}
@stop