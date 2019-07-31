@extends('layouts.' . ($isAjax ? 'post' : 'admin'))

@section('content')

    @use('helpers\Html')
    @use('helpers\ArrayHelper')

    {?
    $btnSubmit = $model->isNewRecord() ?
    Html::submitButton('Создать', ['class' => 'btn btn-success']) :
    Html::submitButton('Сохранить', ['class' => 'btn btn-primary']);

    $form = \widgets\ActiveForm::begin([
    'action' => $model->id ? ('/admin/opinions/update/' . $model->id) : '',
    'options' => ['enctype' => 'multipart/form-data']
    ]);
    ?}

    <div class="row">
        <div class="col-xs-8">
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-10">
                            {{ $form->field($model, 'name') }}
                        </div>
                        <div class="col-xs-2">
                            {{ $form->field($model, 'is_visible')->dropDownList([0 => 'Нет', 1 => 'Да']) }}
                        </div>
                    </div>
                    {{ $btnSubmit }}
                </div>
            </div>
        </div>
        <div class="col-xs-4">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Изображение</h3>
                </div>
                <div class="box-body thumbnails-product">
                    <div class="row">
                        @if ($model->image)
                            <div class="col-xs-6 col-md-3">
                                {{ Html::hiddenInput($model->formName() . '[image_id]', $model->image->id) }}
                                <a href="#" class="thumbnail">
                                    @if(isset($model->image))
                                        {{ \helpers\Image::img($model->image->filename, 300, 300, ['crop' => true]) }}
                                    @endif
                                </a>
                            </div>
                        @endif
                    </div>
                    {{ $model->hasError('image') ? ('<div class="help-block error-block">' . $model->getError('image') . '</div>') : '' }}
                    {{ Html::fileInput('image') }}
                    <p></p>
                    {{ $btnSubmit }}
                </div>
            </div>
        </div>
    </div>
    {{ $form->end() }}
@stop