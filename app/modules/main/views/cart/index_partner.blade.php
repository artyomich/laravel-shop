@extends('layouts.' . ($isAjax ? 'post' : 'default'))

@section('content')
    <div class="content cart-data default-form" style="display: block">
        {? $form = \widgets\ActiveForm::begin(['type' => 'horizontal', 'options' => ['id' => 'cartForm']]); ?}
        <input type="hidden" name="Orders[city_id]" value="{{ $city->id }}"/>
        <input type="hidden" name="make_order" value="1"/>

        <h1 class="font-bold">Заказ от {{date("d.m.Y")}}</h1>
        <?php

        ?>
        <table class="zakaz-header">
            <tr>
                <td>
                    {{--<b>ТИП ЗАКАЗА:</b> Сборный--}}
                    {{--<span class="glyphicon glyphicon-question-sign" data-toggle="tooltip" data-placement="bottom" data-original-title="Что такое &quot;Сборный заказ&quot;"></span>--}}
                    {{--<br />--}}
                    {{--<small><em>Весь заказ будет готов к вывозу через 14 дней</em></small>--}}

                </td>
                <td>
                </td>
            </tr>
        </table>
        <table class="partner-data">
            <tbody>
            <tr>
                <td>
                    <span class="font-bold">Заказчик: </span> <span class="firm-name">{{$user['firm']}}</span><br/>
                    <span class="color999">Адрес: </span><span class="firm-address color999">{{$user['address']}}</span><br/>
                    <span class="color999">ИНН / КПП: </span><span class="firm-inn-kpp color999">{{$user['inn']}}
                        / {{$user['kpp']}}</span><br/>
                    <span>Контактное лицо: </span> <span
                            class="contact-name">{{$user['first_name']}} {{$user['last_name']}}</span>
                    <span class="btn btn-default-outline btn-xs open-contact-form" data-toggle="collapse" data-target=".box-body-myself"
                            aria-expanded="false" aria-controls="box-body-myself">Изменить
                    </span>
                    <br/>
                    <span>Телефон: </span><span class="contact-phone">{{$user['phone']}}</span>

                    <div class="edit-contact-form">
                        {{ $form->field($model, 'user_name', ['hint' => 'Например: Пертов Василий Сергеевич'])->textInput(['value' => isset($user['first_name'])  ? ($user['first_name'].' '.$user['last_name']) : '']) }}
                        {{ $form->field($model, 'email', ['hint' => 'Например: petrov@email.ru. На ваш почтовый адрес будет отправлено письмо с ссылкой для отслеживания состояния вашего заказа.'])->textInput(['value' => isset($user['email'])  ? $user['email']: '']) }}
                        {{ $form->field($model, 'phone', [
                            'hint' => 'Например: +79874561234. Нужен менеджеру для связи с вами.',
                            'addon' => ['prepend' => ['content' => '+7']]
                        ])->textInput(['value' => isset($user['phone'])  ? $user['phone']: '']) }}

                        <input type="button" class="btn" value="Сохранить" id="okEditContactForm"/>
                    </div>
                    <br/><br/>
                    <span class="font-bold">Способ оплаты: </span><br/>
                    {{--*/ $isChecked = false; /*--}}
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
                            {{--*/ $isChecked = true; /*--}}

                        @endif
                    @endforeach
                </td>
                <td>

                    <span class="font-bold">Получение товара</span><br/>

                    <div class="radio">
                        <label>
                            <input type="radio" name="delivery" value="warehouse" checked
                                   onclick="$('#deliveryCityName,#deliveryCityID,#deliveryCost').val('')">
                            Самовывоз<br/>
                            <small><em>
                                    г.{{ $city->name }}
                                    , {{ $city->address_storage?$city->address_storage:$city->address }}<br/>
                                    Отдел продаж: {{ $city->phones }},
                                    Режим работы: <span class="correct-moscow-hours">{{ $city->work_begin }}</span>:00 - <span
                                            class="correct-moscow-hours">{{ $city->work_end }}</span>:00
                                </em></small>
                        </label>
                    </div>
                    {{--<div class="radio">--}}
                        {{--<label>--}}
                            {{--<input type="radio" name="delivery" value="customer">--}}
                            {{--Доставка в ваш город<br/>--}}
                            {{--<small class="text-muted">--}}
                                {{--Проверим, упакуем и отправим: доставка осуществляется до терминала ТК (адрес получения--}}
                                {{--вам--}}
                                {{--назовет менеджер)--}}
                            {{--</small>--}}
                        {{--</label>--}}
                    {{--</div>--}}
                    {{--<input id="deliveryCityName" type="text" class="form-control"--}}
                           {{--onfocus="$('input[name=delivery][value=customer]').prop('checked',true)"--}}
                           {{--placeholder="Населенный пункт доставки" name="Orders[address]"/>--}}
                    {{--<input id="deliveryCityID" type="hidden" name="Orders[delivery_city_id]"/>--}}
                    {{--<input id="deliveryCost" type="hidden" name="Orders[delivery_cost]"/>--}}

                </td>
            </tr>
            </tbody>
        </table>

        <div class="zakaz-items-title">
            <b>СОСТАВ ЗАКАЗА</b>
        </div>
        <?php
        $isVendor = false;
        $isWe = false;
        $weCount = 0;
        $vendorsCount = 0;
        $tiresVendorsCount = 0;
        $weAmount = 0;
        $vendorsAmount = 0;
        $vendorsAmountDelievery = 0;
        $notEnough = false;
        $delieveryMax = 0;
        foreach ($cart['items'] as $items) {
            if (isset($items['count'])) {
                $isWe = true;
            }
            if (isset($items['vendor'])) {
                $isVendor = true;
            }
        }
        ?>
        @if($isWe)

            <div>
                <span>В наличии</span>
                <span class="glyphicon glyphicon-question-sign" data-toggle="tooltip" data-placement="bottom"
                      data-original-title="Указанные ниже товары есть в наличиии на нашем складе. Их можно забрать на следующий день после оформления заказа"
                      style="cursor: pointer;"></span>
                <br/>

                <small><em>г.{{ $city->name }}, {{ $city->address_storage?$city->address_storage:$city->address }},
                        Часы работы: c <span class="correct-moscow-hours">{{ $city->work_begin }}</span>:00 до <span
                                class="correct-moscow-hours">{{ $city->work_end }}</span>:00</em></small>
            </div>
            <table class="table cart-list-table out-of-stock">
                <thead>
                <tr>
                    <td>Наименование</td>
                    <td>Количество</td>
                    <td>Цена</td>
                    <td>Общая сумма</td>
                    <td></td>
                </tr>
                </thead>
                <tbody>
                <?php
                $allCol = 0;
                $allSum = 0;


                ?>
                @foreach($cart['items'] as $item)
                    @if(isset($item['count']))
                        <?php
                        $cost = isset($item['model']->getBalances($city->id)->cost)?$item['model']->getBalances($city->id)->cost:0;
                        if(!$cost){
                        $notEnough = true;
                        }
                        ?>
                        <tr tirId="{{ $item['model']->id }}" class="{{$cost?'':'danger'}}">
                            <td class="cart-tir-name">
                                <a href="/catalog/{{ $item['model']->categories->alias }}/{{ $item['model']->alias }}/"
                                   title="Открыть карочку товара в новом окне." target="_blank"><span
                                            class="glyphicon glyphicon-new-window"></span></a>
                                <span>{{ $item['model']->name }}</span>

                                <span class="cart-quantity">
 <span class="balance-green"
         {{--data-lightbox="image-1"--}}
       data-toggle="tooltip"
       data-placement="bottom"
         {{--data-html="true"--}}
       data-template="<div class='tooltip' role='tooltip'><div class='tooltip-arrow'></div><div class='tooltip-inner'></div></div>"
       title=""
       data-original-title="Общее количество позиции на складе. Обращаем ваше внимание,
                                          что при наличии резерва доступно для заказа может быть меньшее количество."

         >
                                    {{$item['model']->balance->balance_full}} шт. на складе.
                                        </span>
                                    <span class="balance-full"
                                            {{--data-lightbox="image-1"--}}
                                          data-toggle="tooltip"
                                          data-placement="bottom"
                                            {{--data-html="true"--}}
                                          data-template="<div class='tooltip' role='tooltip'><div class='tooltip-arrow'></div><div class='tooltip-inner'></div></div>"
                                          title=""
                                          data-original-title="Данное количество находиться сейчас в резерве на нашем складе. Обращаем ваше внимание,
                                        что в некоторых случаях резерв может быть снят или отменен. Если вы хотите купить товар, но он находится в резерве -
                                        оформите заказ и мы попробуем как-нибудь уладить эту ситуацию."

                                            >
                                    @if($item['model']->balance->balance < $item['model']->balance->balance_full)
                                            @if($item['model']->balance->balance_full === 0)
                                                (все в резерве)
                                            @else
                                                ({{$item['model']->balance->balance_full - $item['model']->balance->balance}} шт. в резерве)
                                            @endif

                                        @endif
                                </span>
                                </span>
                            </td>
                            <td class="cart-tir-count">
                                <input type="number"
                                       name="count[{{ $item['model']->id }}][0]"
                                       value="{{ $cost?$item['count']:0 }}"
                                       min="1"
                                       data-cost="{{ isset($item['model']->getBalances( $city->id)->cost) ? ($item['model']->getBalances($city->id)->cost) : '0' }}"
                                       tirCost="{{isset($item['model']->getBalances( $city->id)->cost) ? ($item['model']->getBalances($city->id)->cost) : '0' }}"
                                       thisRow="{{ $item['model']->id }}"
                                       max="{{ is_object($item['model']->balance) ? $item['model']->balance->balance_full : 0 }}"/>
                            </td>
                            <td class="cart-tir-cost">
                                <?php
                                ?>
                                {{isset($item['model']->getBalances( $city->id)->cost) ? ($item['model']->getBalances($city->id)->cost) : '0' }}
                                руб.
                            </td>
                            <td class="cart-tir-itog">
                                {{isset($item['model']->getBalances( $city->id)->cost) ? ($item['model']->getBalances($city->id)->cost*$item['count']) : '0'}}
                                руб.
                            </td>
                            <td class="cart-tir-del">
                                <div class="form-control-static">
                                    <a href="/cart/remove/{{ $item['model']->id }}"
                                       class="glyphicon glyphicon-remove"></a>
                                </div>
                            </td>
                        </tr>
                        <?php
                        if($cost){
                        $weCount =$weCount +$item['count'];
                        $weAmount = $weAmount + $cost*$item['count'];
                        }

                        ?>
                    @endif
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <td>
                        <span>ИТОГО:</span>
                    </td>
                    <td class="total-col">
                        <span>{{$weCount}} шт</span>
                    </td>
                    <td>

                    </td>
                    <td class="total-sum">
                        <span>{{$weAmount}} руб</span>
                    </td>
                    <td></td>
                </tr>

                </tfoot>
            </table>
        @endif
        @if($isVendor)
            <div class="not-of-stock-header">
                <span>Под заказ</span>
                <span class="glyphicon glyphicon-question-sign" data-toggle="tooltip" data-placement="bottom"
                      data-original-title="Указанные ниже товары сейчас отсутствуют на складе и будут доставлены в течении указанного времени. После чего их можно будет забрать со склада."
                      style="cursor: pointer;"></span>
                {{--<span>Максимальный срок поступления </span><span class="delievery-max"> </span> д.--}}
                <br/>
                <small><em>до склада в г.{{ $city->name }}
                        , {{ $city->address_storage?$city->address_storage:$city->address }}</em></small>
            </div>

            <table class="cart-list-table not-in-stock">
                <thead>
                <tr>
                    <td>Наименование</td>
                    <td>Количество</td>
                    <td>Цена</td>
                    <td>Общая сумма</td>
                    <td></td>
                </tr>
                </thead>
                <tbody>
                @foreach($cart['items'] as $keyProduct =>$item)
                    @if(isset($item['vendor'] ))
                        <input type="hidden" name="delieveryMax" value="{{$delieveryMax}}" disabled="disabled"/>
                        <tr>
                            <td class="cart-tir-name">
                                <a href="/catalog/{{ $item['model']->categories->alias }}/{{ $item['model']->alias }}/"
                                   title="Открыть карочку товара в новом окне." target="_blank"><span
                                            class="glyphicon glyphicon-new-window"></span></a>
                                <span>{{ $item['model']->name }}</span>
                                <span class="details" title="Подробности...">Скрыть</span>
                                <ul>
                                    <?php $i=1; ?>
                                        @foreach($item['vendor']  as $itemVendor)
                                            <li tirRow="{{$i}}">
                                                @if(isset($itemVendor["delivery"]))
                                                    @if($itemVendor["deliveryPeriodMin"] === $itemVendor["deliveryPeriodMax"])
                                                        @if($itemVendor["deliveryPeriodMin"] === 0)
                                                            Срок поставки 1 д.
                                                        @else
                                                            Срок поставки {{$itemVendor["deliveryPeriodMax"]}} д.
                                                        @endif

                                                    @else
                                                        Срок поставки от {{$itemVendor["deliveryPeriodMin"]}}
                                                        до {{$itemVendor["deliveryPeriodMax"]}} д.
                                                    @endif
                                                @endif

                                                    <span class="balance-green"
                                                          {{--data-lightbox="image-1"--}}
                                                          data-toggle="tooltip"
                                                          data-placement="bottom"
                                                          {{--data-html="true"--}}
                                                          data-template="<div class='tooltip' role='tooltip'><div class='tooltip-arrow'></div><div class='tooltip-inner'></div></div>"
                                                          title=""
                                                          data-original-title="Общее количество позиции на складе. Обращаем ваше внимание,
                                          что при наличии резерва доступно для заказа может быть меньшее количество."

                                                    >
                                                {{$itemVendor['balance']}} шт. на складе.
                                                    </span>
                                            </li>
                                            <?php $i++; ?>
                                        @endforeach
                                </ul>
                            </td>
                            <td class="cart-tir-count">
                                <span> - </span>
                                <ul>
                                    <?php $i=1;?>
                                    @foreach($item['vendor'] as $vendorId => $itemVendor)
                                        <li tirRow="{{$i}}">
                                            <input type="number"
                                                   name="count[{{ $item['model']->id }}][{{$vendorId }}]"
                                                   value="{{$itemVendor['count']}}"
                                                   data-cost="{{$itemVendor['costMarkUp']}}"
                                                   min="1"
                                                   max="{{ $itemVendor['balance'] }}"
                                                   tirCost="{{$itemVendor['costMarkUp']}}"
                                                   tirTransport="{{isset($itemVendor['delivery'])?$itemVendor['delivery']:''}}"
                                            />
                                        </li>
                                        <?php
                                        $i++;
                                        $vendorsCount=$vendorsCount+$itemVendor['count'];
                                        ?>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="cart-tir-cost">
                                -
                                <ul>
                                    <?php $i=1;?>
                                    @foreach($item['vendor'] as $itemVendor)
                                        <li tirRow="{{$i}}">
                                            {{$itemVendor["costMarkUp"]}}
                                            руб.
                                        </li>
                                        <?php $i++; ?>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="cart-tir-itog">
                                -
                                <ul>
                                    <?php $i=1; ?>
                                    @foreach($item['vendor'] as $itemVendor)
                                        <li tirRow="{{$i}}">
                                            <span class="vendor-cost">
                                            {{$itemVendor["costMarkUp"] * $itemVendor['count']}}
                                            </span>
                                             руб.
                                            @if($itemVendor['delivery']<> 0)
                                            +
                                        <span class="vendor-delivery" data-toggle="tooltip" data-placement="bottom"
                                              data-original-title="Стоимость доставки в г.{{$city->name}}">
                                            {{$itemVendor['delivery'] * $itemVendor['count']}}
                                            </span>
                                            @endif
                                        </li>
                                        <?php
                                        $i++;
                                        $vendorsAmount = $vendorsAmount+$itemVendor["costMarkUp"] * $itemVendor['count'];
                                        $vendorsAmountDelievery = $vendorsAmountDelievery + (isset($itemVendor['delivery']) ? $itemVendor['delivery'] * $itemVendor['count'] : 0);
                                        ?>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="cart-tir-del">
                                -
                                <ul>
                                    <?php $i=1; ?>
                                    @foreach($item['vendor'] as $keyVendor=>$itemVendor)
                                        <li tirRow="{{$i}}">
                                            <div class="form-control-static">
                                                <a href="/cart/remove/{{ $keyProduct }}/{{$keyVendor}}"
                                                   class="glyphicon glyphicon-remove"></a>
                                            </div>
                                        </li>
                                        <?php $i++; ?>
                                    @endforeach
                                </ul>
                            </td>
                        </tr>
                    @endif
                @endforeach

                </tbody>
                <tfoot>
                <tr>
                    <td>
                        <span>ИТОГО:</span>
                    </td>
                    <td class="cart-tir-count">
                        {{$vendorsCount}} шт.
                    </td>
                    <td>

                    </td>
                    <td class="cart-tir-itog">
                        <span>
                            {{$vendorsAmount}} руб.
                            @if($vendorsAmountDelievery)
                            + <span data-toggle="tooltip" data-placement="bottom"
                                    data-original-title="Стоимость доставки в г.{{$city->name}}">{{$vendorsAmountDelievery}}
                                руб.</span>
                        </span>
                        @endif
                    </td>
                    <td></td>
                </tr>
                </tfoot>
            </table>
        @endif
        <table class="zakaz-itog">
            <tbody>
            @if ($isVendor)
                <tr>
                    <td class="cart-tir-name">
                        <strong>ВСЕГО ПО ЗАКАЗУ:</strong>
                    </td>
                    <td class="cart-tir-count">
                        <strong>{{$weCount+$vendorsCount}} шт</strong>
                    </td>
                    <td class="cart-tir-cost">

                    </td>
                    <td class="cart-tir-itog">
                        <strong>
                            {{$weAmount+$vendorsAmount}}
                            @if($vendorsAmountDelievery)
                                + <span data-toggle="tooltip" data-placement="bottom"
                                        data-original-title="Стоимость доставки в г.{{$city->name}}">{{$vendorsAmountDelievery}}
                                    руб.</span>
                            @endif
                        </strong><br/>

                    </td>
                </tr>
            @endif
            <tr>
                <td colspan="3"></td>
                <td colspan="1" class="partner-discount">
                    <div>
                        <b>
                            <span class="cart-saving">Итого: </span><span class="total">{{$weAmount+$vendorsAmount+$vendorsAmountDelievery}}</span> р.
                        </b>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
        <div id="notEnough" style="display: none">
            <span class="label label-warning">На складе не достаточно шин. Пожалуйста, уточняйте сроки их поставки у менеджера.</span>
            <br/><br/>
        </div>

        <br/>

        <div class="row submit-block">
            <div class="col-xs-12 text-center">
                Оформите заказ сейчас. Нам доверились уже {{ $countOrders }} покупателей. Присоединяйтесь!<br/><br/>

                <a href="/catalog/" class="btn btn-grey"> НАЗАД В КАТАЛОГ</a>
                <button type="submit" class="btn btn-cart-oformit">ОФОРМИТЬ ЗАКАЗ</button>
            </div>
        </div>
        </form>
    </div>

    <!-- /CONTENT -->
    <div class="footer"></div>

    <script type="text/javascript">
        // привязываем автозаполнение города
        (function ($) {
            $('.delievery-max').text({{$delieveryMax}})
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
                    <button class="btn btn-primary" onclick="$('#deliveryAttention').modal('hide')">Ок</button>
                </div>
            </div>
        </div>
    </div>
@stop