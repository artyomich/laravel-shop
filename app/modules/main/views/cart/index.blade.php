@extends('layouts.' . ($isAjax ? 'post' : 'default'))

@section('content')


    <div class="content">
        <h1 class="font-bold">Корзина</h1>
        <p>
            Выбранный город: {{ \widgets\modals\ChooseCityModal::a(['class' => 'cart-city']) }}
        </p>


        <ul class="nav nav-tabs shoping-cart-tabs">
            <li {{ Input::has('changeDefaultForm') ? '' : 'class="active"' }}><a href="#">Быстрый заказ</a></li>
            <li {{ Input::has('changeDefaultForm') ? 'class="active"' : '' }}><a href="#">Стандартная форма заказа</a>
            </li>
        </ul>
        <div class="cart-forms tab-content">
            <div class="quick-form" {{Input::has('changeDefaultForm') ? 'style="display:none;"' : '' }}>
                {? $form = \widgets\ActiveForm::begin(['type' => 'horizontal', 'options' => ['id' => 'quickCartForm']]);
                ?}
                {{ Form::hidden('Orders[city_id]', $city->id) }}
                {{ Form::hidden('make_order', 1) }}
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th class="col-xs-6">Название</th>
                        <th class="col-xs-2">Цена</th>
                        <th class="col-xs-1">Количество</th>
                        <th class="col-xs-2">Общая цена</th>
                        <th class="col-xs-1"></th>
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
                                <div class="form-control-static">
                                    {{ $item['model']->getBalances($city->id) ? ($item['model']->getBalances($city->id)->cost . ' руб.') : '0' }}
                                </div>
                            </td>
                            <td>
                                {{ Form::number("count[".$item['model']->id ."][0]", $item['count'], ['data-limit' => is_object($item['model']->balance) ? $item['model']->balance->balance : 0, 'data-cost' => isset($item['model']->balance) ? ($item['model']->balance->cost . ' руб.') : '0', 'class' => 'form-control' ])  }}
                            </td>
                            <td>
                                <div class="form-control-static">{{ (isset($item['model']->balance) ? ($item['model']->balance->cost) : '0') *$item['count'] }}
                                    руб.
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="form-control-static">
                                    <a href="/cart/remove/{{ $item['model']->id }}/"
                                       class="glyphicon glyphicon-trash text-danger btn-remove"></a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="5" class="text-right">
                            Итого<span id="totalCostLabel" class="orange" style="display: none"> (без стоимости доставки)</span>:
                            <strong id="totalCost">{{ $cart['total'] }} руб.</strong>

                            <div style="display:none">Стоимость доставки: <strong id="deliveryLabel"></strong></div>
                        </td>
                    </tr>
                    </tfoot>
                </table>

                <div id="notEnough" style="display: none">
                    <span class="label label-warning">На складе не достаточно шин. Пожалуйста, уточняйте сроки их поставки у менеджера.</span>
                    <br/><br/>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        <?php
                        if (!empty($user)) {
                            $userName = $user['first_name'] . ' ' . $user['last_name'];
                            $userPhone = $user['phone'];
                            $userEmail = $user['email'];
                        } else {
                            $userName = '';
                            $userPhone = '';
                            $userEmail = '';
                        } ?>
                        {{ $form->field($model, 'user_name', ['hint' => 'Например: Пертов Василий Сергеевич'])->textInput(['value' => Input::get('Orders.user_name', $userName)]) }}
                        {{ $form->field($model, 'phone', ['hint' => 'Например: +79874561234. Нужен менеджеру для связи с вами.','addon' => ['prepend' => ['content' => '+7']]])->textInput(['value' => Input::get('Orders.phone', $userPhone)]) }}
                        {{ $form->field($model, 'email', ['hint' => 'Например: petrov@email.ru. На ваш почтовый адрес будет отправлено письмо с ссылкой для отслеживания состояния вашего заказа.'])->textInput(['value' => Input::get('Orders.email', $userEmail)]) }}
                        {{ Form::hidden('onlinepay', 'cash') }}
                    </div>
                    <div class="col-xs-6">
                        {{ Form::hidden('delivery', 'warehouse') }}
                        {{ Form::hidden('Orders[address]', '') }}
                        {{ Form::hidden('Orders[delivery_city_id]', '') }}
                        {{ Form::hidden('Orders[delivery_cost]', '') }}
                        <div class="quick-order-discount">
                            Уважаемый покупатель! <br/>
                            При заказе шин через интернет-магазин Вам предоставляется скидка на товар (цена на сайте
                            сейчас показана
                            со скидкой).<br/>
                            <b>При обращении в офис компании сообщите пожалуйста номер Вашего заказа! </b>
                        </div>
                        <div class="quick-order-info">
                            Укажите свое имя и номер телефона. Мы с удовольствием проконсультируем Вас по всем
                            интересующим вопросам и поможем с выбором шин. Наши сотрудники подробно изучат ваши
                            требования и, проанализировав ситуацию, предложат оптимальные условия для покупки.
                        </div>
                    </div>
                </div>
                <div class="text-center order-make-field">
                    Оформите заказ сейчас. Нам доверились уже {{ $countOrders }} покупателей. Присоединяйтесь!<br/><br/>
                    <button type="submit" class="btn btn-cart-oformit">ОФОРМИТЬ ЗАКАЗ</button>
                </div>
                <div class="text-center color999">
                    <br>
                    <small>Нажимая кнопку "Оформить заказ" вы подтверждаете взаимное согласие с тем, что Продавец не выставляет счета-фактуры в адрес Покупателя на основании пункта 3 статьи 169 Налогового кодекса РФ.</small>
                </div>
                </form>
            </div>
            <div class="default-form" {{Input::has('changeDefaultForm') ? 'style="display:block;"' : '' }}>
                {? $form = \widgets\ActiveForm::begin(['type' => 'horizontal', 'options' => ['id' => 'cartForm']]); ?}
                {{Form::hidden('Orders[city_id]', $city->id)}}
                {{Form::hidden('make_order', 1)}}
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th class="col-xs-6">Название</th>
                        <th class="col-xs-2">Цена</th>
                        <th class="col-xs-1">Количество</th>
                        <th class="col-xs-2">Общая цена</th>
                        <th class="col-xs-1"></th>
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
                                <div class="form-control-static">
                                    {{ isset($item['model']->balance) ? ($item['model']->balance->cost . ' руб.') : '0' }}
                                </div>
                            </td>
                            <td>
                                {{ Form::number("count[".$item['model']->id."][0]", $item['count'], ['data-limit' => is_object($item['model']->balance) ? $item['model']->balance->balance : 0, 'data-cost' => isset($item['model']->balance) ? ($item['model']->balance->cost . ' руб.') : '0', 'class' => 'form-control'] ) }}
                            </td>
                            <td>
                                <div class="form-control-static">{{ (isset($item['model']->balance) ? ($item['model']->balance->cost) : '0') *(isset($item['model']->balance) ? ($item['model']->balance->cost):0) }}
                                    руб.
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="form-control-static">
                                    <a href="/cart/remove/{{ $item['model']->id }}/"
                                       class="glyphicon glyphicon-trash text-danger btn-remove"></a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="5" class="text-right">
                            Итого<span id="totalCostLabel" class="orange" style="display: none"> (без стоимости доставки)</span>:
                            <strong id="totalCost">{{ $cart['total'] }} руб.</strong>

                            <div style="display:none">Стоимость доставки: <strong id="deliveryLabel"></strong></div>
                        </td>
                    </tr>
                    </tfoot>
                </table>
                <div id="notEnough" style="display: none">
                    <span class="label label-warning">На складе не достаточно шин. Пожалуйста, уточняйте сроки их поставки у менеджера.</span>
                    <br/><br/>
                </div>
                {{ Form::hidden('changeDefaultForm', 1) }}
                <div class="row">
                    <div class="col-xs-6">
                        <div class="page-header">
                            <h2 class="font-bold">Контактные данные</h2>
                        </div>
                        {{ $form->field($model, 'user_name', ['hint' => 'Например: Пертов Василий Сергеевич'])->textInput(['value' => Input::get('Orders.user_name', $userName)]) }}
                        {{ $form->field($model, 'phone', ['hint' => 'Например: +79874561234. Нужен менеджеру для связи с вами.','addon' => ['prepend' => ['content' => '+7']]])->textInput(['value' => Input::get('Orders.phone', $userPhone)]) }}
                        {{ $form->field($model, 'email', ['hint' => 'Например: petrov@email.ru. На ваш почтовый адрес будет отправлено письмо с ссылкой для отслеживания состояния вашего заказа.'])->textInput(['value' => Input::get('Orders.email', $userEmail)]) }}
                        <br/><br/>

                        <div class="page-header">
                            <h2 class="font-bold">Способ оплаты</h2>
                        </div>
                        {{--*/ $isChecked = false; /*--}}
                        @foreach($pays as $k => $pay)
                            @if ($pay::paymentIsEnable())
                                <div class="radio">
                                    <label>
                                        {{Form::radio('onlinepay', $pay::getPaymentAlias(), !$isChecked ?  ['checked' => 'checked'] : [])}}
                                        <strong>{{ $pay::getPaymentName() }}</strong><br/>
                                        <small class="text-muted">{{ $pay::getPaymentDesc() }}</small>
                                    </label>
                                </div>
                                {{--*/ $isChecked = true; /*--}}
                                <p>&nbsp;</p>
                            @endif
                        @endforeach
                    </div>
                    <div class="col-xs-6">
                        <div class="page-header">
                            <h2 class="font-bold">Доставка</h2>
                        </div>
                        <div class="radio bottom-buffer">
                            <label>
                                {{ Form::radio('delivery', 'warehouse', ['checked' => 'checked']) }}
                                Самовывоз<br/>
                                <small class="text-muted">
                                    г.{{ $city->name }}
                                    , {{ $city->address_storage?$city->address_storage:$city->address }}<br/>
                                    Отдел продаж: {{ $city->phones }}<br/>
                                    Режим работы: <span class="correct-moscow-hours">{{ $city->work_begin }}</span>:00 - <span
                                            class="correct-moscow-hours">{{ $city->work_end }}</span>:00
                                </small>
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                {{ Form::radio('delivery', 'customer') }}
                                Доставка в ваш город<br/>
                                <small class="text-muted">
                                    Проверим, упакуем и отправим: доставка осуществляется до терминала ТК (адрес
                                    получения вам
                                    назовет менеджер)
                                </small>
                            </label>
                        </div>
                        <div class="bottom-buffer left-pad20">
                            {{ Form::text('Orders[address]', '', ['placeholder' => 'Населенный пункт доставки', 'class' => 'form-control', 'id' => 'deliveryCityName']) }}
                            {{ Form::hidden('Orders[delivery_city_id]', '', ['id' => 'deliveryCityID']) }}
                            {{ Form::hidden('Orders[delivery_cost]', '', ['id' => 'deliveryCost']) }}
                        </div>
                        <div class="text-center order-make-field">
                            Оформите заказ сейчас. Нам доверились уже {{ $countOrders }} покупателей. Присоединяйтесь!
                            <br><br>
                            <button type="submit" class="btn btn-cart-oformit">ОФОРМИТЬ ЗАКАЗ</button>
                        </div>
                        <div class="text-center color999">
                            <br>
                            <small>Нажимая кнопку "Оформить заказ" вы подтверждаете взаимное согласие с тем, что Продавец не выставляет счета-фактуры в адрес Покупателя на основании пункта 3 статьи 169 Налогового кодекса РФ.</small>
                        </div>
                    </div>
                </div>
                </form>
            </div>
        </div>

    </div>
    <!-- /CONTENT -->
    <div class="footer"></div>

    <script type="text/javascript">
        // привязываем автозаполнение города
        (function ($) {
            $(function () {
                bindAjaxSelectCity('#deliveryCityName', '#deliveryCityID');
            });
            <?php $deliveryDate = md5(date('Y-m-d')); ?>
                        window.calcDeliveryForCart = function () {
                receiverCityId = parseInt($('#deliveryCityID').val());
                var dataJson = {
                    "dateExecute": "{{ $deliveryDate }}",
                    "authLogin": "{{ \Config::get('cdek.account') }}",
                    "secure": "{{ md5($deliveryDate.'&'.\Config::get('cdek.secure_password')) }}",
                    "senderCityId": "{{ $current_cdek_id }}",
                    "receiverCityId": receiverCityId,
                    "goods": []
                };
                var goodsCount = 0;
                $('#cartForm input[data-cost]').each(function () {
                    goodsCount += parseInt($(this).val());
                });
                for (i = 1; i <= goodsCount; i++) {
                    dataJson.goods.push({
                        "weight": "5",
                        "length": "50",
                        "width": "50",
                        "height": "18"
                    });
                }

                dataJson.receiverCityId && calcDeliveryCDEK(dataJson, '#deliveryCost');

            }
        })(jQuery)
    </script>
    <div class="modal fade" id="deliveryAttention" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    Внимание!
                </div>
                <div class="modal-body">
                    При данном способе оплаты Вы оплачиваете только стоимость товара. <br/>Стоимость доставки
                    оплачивается при получении заказа.
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary">Ок</button>
                </div>
            </div>
        </div>
    </div>
@stop