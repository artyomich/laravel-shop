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
'action' => $model->id ? ('/admin/menus/update/' . $model->id) : '',
'options' => ['data-ajax' => 1]
]) /*--}}
<div class="box">
    <div class="box-body">
        <div class="row">
            <div class="col-xs-7">
                {{ $form->field($model, 'name')->textInput(['class' => 'form-control form-autoalias']) }}
            </div>
            <div class="col-xs-3">
                {{ $form->field($model, 'parent_id')->dropDownList(
                ArrayHelper::map($menus, 'id', 'name'),
                ['prompt' => 'Родительский пункт меню'])
                }}
            </div>
            <div class="col-xs-2">
                {{ $form->field($model, 'menu_id')->dropDownList(
                ArrayHelper::map($types, 'id', 'name'),
                ['prompt' => 'Без меню'])
                }}
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
                {{ $form->field($model, 'alias', [
                'addon' => $model->isNewRecord() ? [] : [
                'append' => [
                'content' => '<a href="/' . $model->alias . '" target="_blank"><i
                        class="glyphicon glyphicon-link"></i></a>'
                ]
                ]
                ])->textInput($model->isNewRecord() ? ['data-autoalias' => 1] : []) }}
                {{ $btnSubmit }}
            </div>
        </div>
    </div>
</div>
{{ $form->end() }}
@stop