@extends('layouts.' . ($isAjax ? 'post' : 'admin'))

@section('content')

@use('helpers\Html')
@use('helpers\ArrayHelper')

{?
$btnSubmit = $model->isNewRecord() ?
    Html::submitButton('Создать', ['class' => 'btn btn-success']) :
    Html::submitButton('Сохранить', ['class' => 'btn btn-primary']);

$form = \widgets\ActiveForm::begin([
    'action' => $model->id ? ('/admin/banners/update/' . $model->id) : '',
    'options' => ['enctype' => 'multipart/form-data']
]);
?}
<div class="box">
    <div class="box-body">
        <div class="row">
            <div class="col-xs-6">
                {{ $form->field($model, 'name') }}
            </div>
            <div class="col-xs-2">
                {{ $form->field($model, 'group_id')->dropDownList(
                   \helpers\ArrayHelper::map(\models\BannersGroups::all(), 'id', 'name'), ['prompt' => 'Вне группы'])
                }}
            </div>
            <div class="col-xs-2">
                {{ $form->field($model, 'city_id')->dropDownList(
                   \helpers\ArrayHelper::map(\models\Cities::all(), 'id', 'name'), ['prompt' => 'Все города'])
                }}
            </div>
            <div class="col-xs-2">
                {{ $form->field($model, 'is_visible')->dropDownList([0 => 'Нет', 1 => 'Да']) }}
            </div>
        </div>
        {{ $btnSubmit }}
    </div>
</div>

<div class="row">
    <div class="col-xs-6">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Параметры</h3>
            </div>
            <div class="box-body">
                {{ $form->field($model, 'link', [
                    'addon' => [
                    'append' => $model->isNewRecord() ? [] : [
                    'content' => '<a href="' . $model->link . '" target="_blank"><i
                            class="glyphicon glyphicon-link"></i></a>'
                    ]
                    ]
                    ])->textInput() }}
                <p></p>
                {{ $btnSubmit }}
            </div>
        </div>
    </div>
    <div class="col-xs-6">
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