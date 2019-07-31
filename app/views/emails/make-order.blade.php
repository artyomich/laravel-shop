<p>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</p>
<table border="0" cellpadding="5" cellspacing="5" align="center"
       style="width: 60%; background-image: url('http://poshk.ru/files/uploads/back2222.png');">
    <tbody>
    <tr>
        <td>
            <table border="0" cellpadding="1" cellspacing="10" align="left" style="width: 100%;">
                <tbody>
                <tr valign="middle">
                    <td><a href="http://poshk.ru" target="_blank"><img src="http://poshk.ru/files/uploads/logo_1.png"
                                                                       width="100"
                                                                       height="38" style="float: left;"/></a></td>
                    <td><p style="text-align: left;"><span style="font-size: medium; color: #000000;"><strong>
                                    ПЕРВАЯ ОБЪЕДИНЕННАЯ ШИННАЯ КОМПАНИЯ</strong></span></p></td>
                    <td align="right" valign="middle"><p><span
                                    style="text-decoration: underline; font-size: small; color: #333333;">{{ $order->phones }}</span>
                        </p></td>
                </tr>
                </tbody>
            </table>
            <p><span style="color: #ffffff;">.</span></p>

            <p></p>
            <table border="0" align="left" style="width: 100%;">
                <tbody>
                <tr>
                    <td>
                        <table border="0" cellpadding="2" cellspacing="3" align="right"
                               style="width: 35%; background-image: url('http://poshk.ru/files/uploads/back11.png'); background-repeat: round;">
                            <tbody>
                            <tr>
                                <td style="text-align: left;"><p><b></b></p>

                                    <p><b></b> Ваш заказ номер<strong> {{ $order->id }}</strong></p></td>
                            </tr>
                            <tr>
                                <td style="text-align: left;"><p><strong><span
                                                    style="color: #000000;"></span></strong><span
                                                style="color: #000000;">Статус обработки:<strong><span>Новый</span></strong></span>
                                    </p>

                                    <p><span style="color: #000000;"><strong><span><br/></span></strong></span></p>

                                    <p><span style="color: #000000;"><strong><span><br/></span></strong></span></p>

                                    <p><span style="color: #ffffff;"><strong><a
                                                        href="http://poshk.ru/order/state/{{ $order->code }}/"><img
                                                            src="http://poshk.ru/files/uploads/button1.png" width="232"
                                                            height="39"
                                                            style="display: block; margin-left: auto; margin-right: auto;"/></a></strong></span>
                                    </p>

                                    <p><span style="color: #ffffff;"><strong><br/></strong></span></p></td>
                            </tr>
                            </tbody>
                        </table>
                        <table border="0" align="left" style="width: 60%;">
                            <tbody>
                            <tr>
                                <td><p>
                                        <span style="color: #000000;">Уважаемый <strong>{{ $order->user_name }}</strong>, Вами оформлен заказ в магазине ПОШК.</span>
                                    </p>

                                    <p><span style="color: #000000;">Состав заказа:</span><br/><span
                                                style="color: #000000;">
                                            @foreach($order->items as $item)
                                                {{ $item->name }}, {{ $item->amount }}шт.
                                                <strong>{{ $item->amount * $item->cost }} руб.</strong>

                                                @if($item->vendor_id != 0 )
                                                    <?php
                                                    $current_balanss = \models\ProductsBalances::where('products_balances.product_id', $item->id)
                                                            ->where('products_balances.vendor_id', $item->vendor_id)
                                                            ->join('users', 'users.id', '=', 'products_balances.vendor_id')
                                                            ->join('products_properties', 'products_properties.product_id', '=', 'products_balances.product_id')
                                                            ->select('users.cdek_id', 'products_balances.*', 'products_properties.diameter_inch', 'products_properties.width_mm', 'products_properties.series', 'products_properties.diameter_outside')
                                                            ->get()
                                                            ->toArray();
                                                    $current_balanss = array_shift($current_balanss);
                                                    $cdek = \models\Products::getPriceCdek($current_balanss, \models\Cities::getCurrentCity())->result;
                                                    ?>
                                                    <strong>+ {{$cdek->priceByCurrency}} руб.</strong>
                                                    (доставка) @endif
                                                </span><br/><span style="color: #000000;">
                                            @endforeach
                                        </span></p>

                                    <p></p>

                                    <p></p></td>
                            </tr>
                            </tbody>
                        </table>
                        <p><span><span><span><br/></span></span></span></p>

                        <p><span style="color: #ffffff;">.</span></p>

                        <p><span style="color: #ffffff;">.</span></p>

                        <p><span style="color: #ffffff;">.</span></p>

                        <p><span style="color: #ffffff;">.</span></p>

                        <p><span style="color: #ffffff;">.</span></p>

                        <p><a
                                    href="https://market.yandex.ru/shop/214753/reviews/add?hid&retpath=https%3A%2F%2Fmarket.yandex.ru%2Fshop%2F214753%2Freviews&track=rev_mc_write">
                                <img src="http://poshk.ru/files/uploads/feedback.png" width="153" height="38"
                                     background-repeat="round"/>
                            </a>
                        </p>
                    </td>
                </tr>
                </tbody>
            </table>
            <p></p>

            <p></p>

            <p></p>

            <p></p>

            <p></p>

            <p></p>

            <p></p>

            <p><b>Внимание!</b></p>

            <p><span>Если вы сделали заказ в вечерние, ночные часы или в выходной день - менеджер свяжется с вами сразу с наступлением рабочего времени. Режим работы на странице</span><strong><a
                            href="http://poshk.ru/%7B%7B%20$order-%3Ecity-%3Ealias%20%20%7D%7D/find_us/">"Найти нас"</a></strong>.
            </p>

            <p><a href="http://poshk.ru/%7B%7B%20$order-%3Ecity-%3Ealias%20%20%7D%7D/find_us/"></a><span>Пожалуйста, при обращении к администрации сайта ПОШК ОБЯЗАТЕЛЬНО указывайте номер Вашего заказа - {{ $order->id }}
                    .</span><br/><span>Адрес магазина: г.{{ $order->city->name }}
                    , {{ $order->city->address }}. Тел./факс {{ $order->city->phones }}</span></p>

            <p><span style="color: #ffffff;">.</span></p>

            <p></p>
            <table border="0" cellpadding="5" cellspacing="5" align="center"
                   style="width: 100%; background-image: url('http://poshk.ru/files/uploads/back.png');">
                <tbody>
                <tr>
                    <td><h1><span style="color: #ffffff;"><strong>ДЛЯ ВАШЕГО БИЗНЕСА POSHK B2B</strong></span></h1>

                        <p><span style="color: #000000; background-color: #ffcc00;">Специальные условия для корпоративных клиентов</span>
                        </p></td>
                </tr>
                <tr>
                    <td>
                        <div class="info-item"><span style="color: #ffffff;"><strong>Что мы предлагаем корпоративным
                                    клиентам</strong></span>
                            <ul>
                                <li><span style="color: #ffffff;">Огромный ассортимент товаров в наличии</span></li>
                                <li><span style="color: #ffffff;">Выгодная цена на любой товар</span></li>
                                <li><span style="color: #ffffff;">Профессиональная и персональная консультация</span>
                                </li>
                                <li><span style="color: #ffffff;">Доставка по России</span></li>
                            </ul>
                        </div>
                        <div class="info-item"><span style="color: #ffffff;"><strong>Как стать клиентом</strong></span>
                            <ul>
                                <li><span style="color: #ffffff;">Зарегистрируйтесь</span></li>
                                <li><span style="color: #ffffff;">Выберите товар и положите его в корзину</span></li>
                                <li><span style="color: #ffffff;">Оформите заказ и получите счет</span></li>
                            </ul>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><br/><br/></td>
                </tr>
                </tbody>
            </table>
            <table border="0" align="left">
                <tbody>
                <tr>
                    <td><p style="text-align: center;"><span style="font-size: medium;"><strong><a
                                            href="http://poshk.ru/opinions/" target="_blank">Отзывы о нашей компании</a></strong></span>
                        </p>
                        <table border="0" align="center">
                            <tbody>
                            <tr>
                                <td><a href="https://www.youtube.com/watch?v=9OWbMmWyvis" target="_blank"><img
                                                src="http://poshk.ru/files/uploads/youtube1.png" width="250"
                                                height="138"
                                                alt="Видеоотзыв наших клиентов"/></a></td>
                                <td><a href="https://www.youtube.com/watch?v=jG4W0Pt1HjU" target="_blank"><img
                                                src="http://poshk.ru/files/uploads/youtube2.png" width="250"
                                                height="138"
                                                alt="Видеоотзыв наших клиентов"/></a></td>
                                <td><a href="https://www.youtube.com/watch?v=QtuiIliX1wo" target="_blank"><img
                                                src="http://poshk.ru/files/uploads/youtube3.png" width="250"
                                                height="138"
                                                alt="Видеоотзыв наших клиентов"/></a></td>
                            </tr>
                            </tbody>
                        </table>
                        <p></p></td>
                </tr>
                </tbody>
            </table>
            <p><br/><br/></p>

            <p>Спасибо за покупку!<br/><span
                        style="color: #999999;">Данное предложение не является публичной офертой.</span></p>

            <div><span><br/></span></div>
        </td>
    </tr>
    </tbody>
</table>