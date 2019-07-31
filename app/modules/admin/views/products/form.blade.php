@extends('layouts.' . ($isAjax ? 'post' : 'admin'))

@section('content')

@use('helpers\Html')
@use('helpers\ArrayHelper')

{?
$btnSubmit = $model->isNewRecord() ?
Html::submitButton('Создать', ['class' => 'btn btn-success']) :
Html::submitButton('Сохранить', ['class' => 'btn btn-primary']);

$form = \widgets\ActiveForm::begin([
'action' => $model->id ? ('/admin/products/update/' . $model->id) : '',
'options' => ['enctype' => 'multipart/form-data']
]);
?}

<div class="box">
    <div class="box-body">
        <div class="row">
            <div class="col-xs-8">
                {{ $form->field($model, 'name')->textInput(['class' => 'form-control form-autocopy form-autoalias']) }}
            </div>
            <div class="col-xs-2">
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
                <h3 class="box-title">Описание</h3>
                <div class="box-tools pull-right">
                    <span class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></span>
                </div>
            </div>
            <div class="box-body">
                {{ $form->field($model, 'description')->textarea(['rows' => '8']) }}
            </div>
        </div>
        <div class="box collapsed-box">
            <div class="box-header">
                <h3 class="box-title">Алиас, метатеги</h3>
                <div class="box-tools pull-right">
                    <span class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></span>
                </div>
            </div>
            <div class="box-body" style="display:none">
                {{ $form->field($model, 'alias', [
                'addon' => [
                'prepend' => ['content' => '/products/'],
                'append' => $model->isNewRecord() ? [] : [
                'content' => '<a href="/products/' . $model->alias . '/" target="_blank"><i
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
                <h3 class="box-title">Свойства</h3>
            </div>
            <div class="box-body">
                <table class="table">
                    @foreach ($model->properties->getColumnsNames() as $propName)
                    @if (!empty($model->properties->$propName) && $propName != 'product_id')
                    <tr>
                        <td>{{ $model->properties->getAttributeLabel($propName) }}:</td>
                        <td>{{ $model->properties->$propName }}</td>
                    </tr>
                    @endif
                    @endforeach
                </table>
            </div>
        </div>
    </div>
    <div class="col-xs-6">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Изображения</h3>
            </div>
            <div class="box-body thumbnails-product">
                <div class="row">
                    @foreach ($model->images as $image)
                    <div class="col-xs-6 col-md-3">
                        {{ Html::hiddenInput($model->formName() . '[uploaded][]', $image->id) }}
                        <a href="#" class="delete"><i class="glyphicon glyphicon-trash"></i></a>
                        <a href="#" class="thumbnail">
                            {{ \helpers\Image::img($image->filename, 300, 300, ['crop' => true]) }}
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
</div>
{{ $form->end() }}
@stop