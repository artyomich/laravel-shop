@extends('layouts.' . ($isAjax ? 'post' : 'admin'))

@section('content')

{{--*/
use helpers\Html;
use helpers\ArrayHelper;

$btnSubmit = $model->isNewRecord() ?
Html::submitButton('Создать', ['class' => 'btn btn-success']) :
Html::submitButton('Сохранить', ['class' => 'btn btn-primary'])
/*--}}

{{--*/ $form = \widgets\ActiveForm::begin([
'action' => $model->id ? ('/admin/pages/update/' . $model->id) : '',
'options' => ['enctype' => 'multipart/form-data']
]) /*--}}
<div class="box">
    <div class="box-body">
        <div class="row">
            <div class="col-xs-7">
                {{ $form->field($model, 'name')->textInput(['class' => 'form-control form-autocopy form-autoalias']) }}
            </div>
            <div class="col-xs-3">
                {{ $form->field($model, 'category_id')->dropDownList(
                ArrayHelper::map($categories, 'id', 'name'),
                ['prompt' => 'Без категории'])
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
                <h3 class="box-title">Параметры страницы</h3>
            </div>
            <div class="box-body">
                {{ $form->field($model, 'alias', [
                'addon' => [
                'append' => $model->isNewRecord() ? [] : [
                'content' => '<a href="' . $model->alias . '" target="_blank"><i
                        class="glyphicon glyphicon-link"></i></a>'
                ]
                ]
                ])->textInput($model->isNewRecord() ? ['data-autoalias' => 1] : []) }}
                {{ $form->field($model, 'meta_title')->textInput(['data-autocopy' => 1]) }}
                {{ $form->field($model, 'meta_keywords') }}
                {{ $form->field($model, 'meta_description')->textarea() }}
                {{ $btnSubmit }}
            </div>
        </div>
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Фотографии</h3>
            </div>
            <div class="box-body thumbnails-product">
                <div class="row">
                    @foreach ($model->photos as $image)
                    <div class="col-xs-6 col-md-3">
                        {{ Html::hiddenInput($model->formName() . '[uploaded][]', $image->id) }}
                        <a href="#" class="delete"><i class="glyphicon glyphicon-trash"></i></a>
                        <a href="#" class="thumbnail">
                            {{ \helpers\Image::img($image->filename, 150, 150, ['crop' => true]) }}
                        </a>
                    </div>
                    @endforeach
                </div>
                {{ Html::fileInput('Images[]', '', ['multiple' => true]) }}
                <p></p>
                {{ $btnSubmit }}
            </div>
        </div>
    </div>
    <div class="col-xs-6">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Краткое описание</h3>
            </div>
            <div class="box-body">
                {{ $form->field($model, 'description')->textarea(['class' => 'form-control ckeditor']) }}
                {{ $btnSubmit }}
            </div>
        </div>
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Основное изображение</h3>
            </div>
            <div class="box-body thumbnails-product">
                <div class="row">
                    @if ($model->image_id)
                    <div class="col-xs-6 col-md-3">
                        {{ Html::hiddenInput($model->formName() . '[mainImage]', $model->image->id) }}
                        <a href="#" class="delete"><i class="glyphicon glyphicon-trash"></i></a>
                        <a href="#" class="thumbnail">
                            {{ \helpers\Image::img($model->image->filename, 300, 300, ['crop' => true]) }}
                        </a>
                    </div>
                    @endif
                </div>
                {{ Html::fileInput('mainImage') }}
                <p></p>
                {{ $btnSubmit }}
            </div>
        </div>
    </div>
</div>

<div class="box">
    <div class="box-header">
        <h3 class="box-title">Содержание</h3>
    </div>
    <div class="box-body">
        {{ $form->field($model, 'body')->textarea(['class' => 'form-control ckeditor']) }}
        {{ $btnSubmit }}
    </div>
</div>
{{ $form->end() }}
@stop