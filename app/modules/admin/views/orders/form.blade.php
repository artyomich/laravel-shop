@extends('layouts.' . ($isAjax ? 'post' : 'admin'))

@section('content')

    @use('helpers\Html')
    @use('helpers\ArrayHelper')

    {?
    $btnSubmit = $model->isNewRecord() ?
    Html::submitButton('Создать', ['class' => 'btn btn-success']) :
    Html::submitButton('Сохранить', ['class' => 'btn btn-primary']);

    $form = \widgets\ActiveForm::begin([
    'action' => ($model->id ? ('/admin/orders/update/' . $model->id) : ''),
    'options' => ['enctype' => 'multipart/form-data']
    ]);

    if ($isDisabled) {
    $btnSubmit = '';
    }

    $fieldParams = [
    'disabled' => $isDisabled
    ];
    ?}

    @if (\Session::has('success'))
        <div class="alert alert-success">{{ \Session::get('success') }}</div>
    @endif

    <div class="row">
        <div class="col-xs-8">
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-4">
                            {{ $form->field($model, 'status')->dropDownList($model->statuses, $fieldParams) }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- СПИСОК ТОВАРОВ --}}
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Список товаров</h3>
                </div>
                <div class="box-body">
                    @if(count($model->items))
                        <table class="table order-items-table">
                            @foreach($model->items as $item)
                                <tr>
                                    <td class="col-xs-6"><a href="/admin/products/update/{{ $item->id }}/"
                                                            target="_blank">{{ $item->name }}</a></td>
                                    <td class="col-xs-2">
                                        <div class="input-group">
                                            <input type="number" name="amount[{{ $item->id }}]"
                                                   value="{{ $item->amount }}" class="form-control"
                                                   @if($isDisabled) disabled="disabled" @endif />
                                            <span class="input-group-addon">шт.</span>
                                        </div>
                                    </td>
                                    <td class="col-xs-1 text-center">
                                        <a href="#" class="btn btn-link btn-delete-order-item"><i
                                                    class="glyphicon glyphicon-trash text-danger"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    @else
                        <p>Пусто</p>
                    @endif
                    <br/>
                    @if(!$isDisabled)
                        <div class="row">
                            <div class="col-xs-7">
                                {{ $btnSubmit }}
                            </div>
                            <div class="col-xs-5">
                                <div class="input-group order-add-product-form">
                                    <input type="text" name="1cid" value="" class="form-control" placeholder="1C ID"
                                           data-order-id="{{ $model->id }}"/>
                            <span class="input-group-btn">
                                <a href="#" class="btn btn-primary btn-add-order-item pull-right">Добавить товар</a>
                            </span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-4">
                            {{ $form->field($model, 'discount', ['addon' => ['append' => ['content' => '%']]])->textInput($fieldParams) }}
                        </div>

                        <div class="col-xs-8 text-center">
                            <div class="form-control-static">
                                <h3>Итого: <strong class="text-danger">{{ $model->cost }}</strong> руб.</h3>
                            </div>
                        </div>
                    </div>
                    {{ $btnSubmit }}
                </div>
            </div>

            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">
                        Яндекс Директ
                    </h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-2">Кампания:</div>
                        <div class="col-xs-10">{{ $model->direct_campaign }}</div>
                    </div>
                    <div class="row">
                        <div class="col-xs-2">Объявление:</div>
                        <div class="col-xs-10">{{ $model->direct_ad_id }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-4">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Покупатель</h3>
                </div>
                <div class="box-body">
                    {{ $form->field($model, 'user_name')->textInput($fieldParams) }}
                    {{ $form->field($model, 'phone')->textInput($fieldParams) }}
                    {{ $form->field($model, 'email')->textInput($fieldParams) }}
                    {{ $form->field($model, 'address')->textarea($fieldParams) }}
                    {{ $form->field($model, 'comments')->textarea($fieldParams) }}
                    {{ $btnSubmit }}
                </div>
            </div>
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Примечание</h3>
                </div>
                <div class="box-body">
                    {{ $form->field($model, 'note')->textarea($fieldParams) }}
                    {{ $btnSubmit }}
                </div>
            </div>
        </div>
    </div>


    {{ $form->end() }}
@stop