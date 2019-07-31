@extends('layouts.' . ($isAjax ? 'post' : 'default'))

@section('content')

{? $form = \widgets\ActiveForm::begin(['type' => 'horizontal']); ?}

<input type="hidden" name="Orders[city_id]" value="{{ $city->id }}" />
<input type="hidden" name="make_order" value="1" />

<div class="row content">
    <section class="col-lg-9 col-md-9 col-sm-12">
        <div class="page-header">
            <h1 class="font-bold">Создание заказа <small>Контактные данные</small></h1>
        </div>

        {{ $form->field($model, 'user_name', ['hint' => 'Например: Пертов Василий Сергеевич']) }}
        {{ $form->field($model, 'email', ['hint' => 'Например: petrov@email.ru. На ваш почтовый адрес будет отправлено письмо с ссылкой для отслеживания состояния вашего заказа.']) }}
        {{ $form->field($model, 'phone', [
            'hint' => 'Например: +79874561234. Нужен менеджеру для связи с вами.',
            'addon' => ['prepend' => ['content' => '+7']]
        ]) }}

        <div class="form-group row">
            <label class="col-sm-2 control-label form-control-static" for="orders-email">Город</label>
            <div class="col-sm-10 form-control-static">{{ $city->name }}</div>
        </div>

        <br/>
        <br/>
        <br/>


        <div class="page-header">
            <h1 class="font-bold">Содержимое заказа</h1>
        </div>

        <table class="table table-striped">
            <thead>
            <tr>
                <th class="col-xs-6">Название</th>
                <th class="col-xs-2">Цена</th>
                <th class="col-xs-1">Количество</th>
                <th class="col-xs-2">Общая цена</th>
            </tr>
            </thead>
            <tbody>
            @foreach($cart['items'] as $item)
            <tr{{ !isset($item['model']->balance) || !$item['count'] ? ' class="danger"' : '' }}>
                <td>
                    <div class="form-control-static">
                        <a href="/catalog/{{ $item['model']->categories->alias }}/{{ $item['model']->alias }}/">
                            {{ $item['model']->name }}
                        </a>
                    </div>
                </td>
                <td>
                    <div class="form-control-static">{{ isset($item['model']->balance) ?
                        ($item['model']->balance->cost . ' руб.') : '0' }}</div>
                </td>
                <td>
                    <input type="hidden" name="count[{{ $item['model']->id }}]" class="form-control"
                           value="{{ $item['count'] }}" data-cost="{{ isset($item['model']->balance) ?
                                                       ($item['model']->balance->cost . ' руб.') : '0' }}">
                    <div class="form-control-static">{{ $item['count'] }}</div>
                </td>
                <td>
                    <div class="form-control-static">{{ $item['cost'] }} руб.</div>
                </td>
            </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <td colspan="5" class="text-center">Итого: <strong id="totalCost" class="text-danger">{{ $cart['total'] }} руб.</strong></td>
            </tr>
            </tfoot>
        </table>

        <br/>
        <br/>

            <div class="page-header">
                <h1 class="font-bold">Оплата</h1>
            </div>


            @foreach($pays as $k => $pay)
                @if ($pay::paymentIsEnable())
                    <div class="radio">
                        <label>
                            <input type="radio" name="onlinepay"
                                   value="{{ $pay::getPaymentAlias() }}"{{ !$isChecked ? ' checked' : '' }}>
                            <strong>{{ $pay::getPaymentName() }}</strong><br/>
                            <small class="text-muted">{{ $pay::getPaymentDesc() }}</small>
                        </label>
                    </div>

                <p>&nbsp;</p>
                @endif
            @endforeach

            <br/>
            <br/>
            <br/>

        <div class="page-header">
            <h1 class="font-bold">Доставка</h1>
        </div>

        <div class="radio">
            <label>
                <input type="radio" name="delivery" value="warehouse" checked>
                Самовывоз<br/>
                <small class="text-muted">С вами свяжется менеджер и скажет где забрать ваш заказ.</small>
            </label>
        </div>

        <br/>
        <br/>

        <div class="text-right">
            <a href="/cart/" class="btn btn-link">Вернуться в корзину</a>
            @if (\Request::get('is_test'))
                <button type="submit" class="btn btn-primary">ПЕРЕЙТИ К ОПЛАТЕ</button>
            @else
                <button type="submit" class="btn btn-cart-oformit">ОФОРМИТЬ ЗАКАЗ</button>
            @endif
        </div>
    </section>
</div>

{{ $form->end() }}

<div class="footer"></div>
@stop
