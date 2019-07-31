@extends('layouts.' . ($isAjax ? 'post' : 'admin'))

@section('content')
@use('helpers\Html')
@use('helpers\ArrayHelper')

{?
$btnSubmit = $model->isNewRecord() ?
   Html::submitButton('Создать', ['class' => 'btn btn-success']) :
   Html::submitButton('Сохранить', ['class' => 'btn btn-primary']);

$form = \widgets\ActiveForm::begin([
    'action' => $model->id ? ('/admin/hdbkcities/update/' . $model->id) : ''
]);
?}

<div class="box">
    <div class="box-body">
        <div class="row">
            <div class="col-xs-6">
                {{ $form->field($model, 'name') }}
            </div>
            <div class="col-xs-4">
                {{ $form->field($model, 'alias') }}
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
                <h3 class="box-title">Контакты</h3>
            </div>
            <div class="box-body">
                {{ $form->field($model, 'address') }}
                {{ $form->field($model, 'address_storage') }}
                {{ $form->field($model, 'phones') }}
                {{ $form->field($model, 'email') }}
                {{ $btnSubmit }}
            </div>
        </div>
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Эквайринг</h3>
            </div>
            <div class="box-body">
                {{ $form->field($model, 'enable_acquiring')->dropDownList([0 => 'Отключен', 1 => 'Включен'])  }}
                {{ $form->field($model, 'online_pay_delivery')->dropDownList([0 => 'Нет', 1 => 'Да']) }}
                {{ $btnSubmit }}
            </div>
        </div>
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Онлайн консультант</h3>
            </div>
            <div class="box-body">
                {{ $form->field($model->consult, 'is_enable')->dropDownList([0 => 'Отключен', 1 => 'Включен'])  }}
                {{ $form->field($model->consult, 'city_key') }}
                <div class="hidden">
                    {{ $form->field($model->consult, 'city_id') }}
                </div>
                {{ $btnSubmit }}
            </div>
        </div>
    </div>
    <div class="col-xs-6">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Менеджер</h3>
            </div>
            <div class="box-body">
                {{ $form->field($model, 'email_manager') }}
                {{ $form->field($model, 'phone_manager') }}
                {{ $form->field($model, 'work_begin') }}
                {{ $form->field($model, 'work_end') }}
                {{ $form->field($model, 'default_manager')->dropDownList(ArrayHelper::map($employers, 'id', 'name')) }}
                {{ $btnSubmit }}
            </div>
        </div>
    </div>
</div>

{{ $form->end() }}
@stop