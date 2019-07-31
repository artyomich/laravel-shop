@extends('layouts.' . ($isAjax ? 'post' : 'default'))

@section('content')

    @use('helpers\Html')

    <div class="lk-page">
        <h1>Личный кабинет</h1>

        <div class="lk-partner-name">
            <p class="top-buffer">
                Здравствуйте, уважаемый <b>{{ $model->first_name }}</b>.
            </p>

            <p>

                <?php
                $firm = ['firm', 'inn', 'ogrn', 'kpp', 'rs', 'ks', 'bik', 'bank', 'last_name', 'first_name', 'email', 'address', 'actual_address'];
                ?>

                Вы зарегистрированы как юридическое лицо: <b>{{ $model->firm }}</b>
                    <button id="partnerLkEdit" class="btn btn-default-outline btn-xs" data-toggle="collapse" data-target=".box-body-myself"
                            aria-expanded="false" aria-controls="box-body-myself">Редактировать личные данные
                    </button>

            <div id="partnerLkForm" class="row lk-edit-form clearfix active">
                <?php
                $form = \widgets\ActiveForm::begin([
                        'action' => '/user/office/',
                        'options' => ['data-ajax' => '1']
                ]);
                ?>
                {{ $form->field($model, 'type')->hiddenInput([ 'value'=> 'firm'])->label(false) }}
                @foreach($firm as $value)
                    <div class="col-sm-6">
                        {{$form->field($model, $value)->input('text')}}
                    </div>
                @endforeach
                <div class="col-sm-6">
                    {{ $form->field($model, 'city_name')->input('text',['id' => 'users-city_name']) }}
                    {{ $form->field($model, 'cdek_id')->hiddenInput(['id' => 'deliveryCityID'])->label(false)}}
                </div>
                <div class="col-sm-6">
                    {{ $form->field($model, 'phone')->input('text', ['placeholder' => 'Телефон в формате*: +79123456789:']) }}
                </div>
                <div class="col-sm-6">
                    {{$form->field($model, 'password')->passwordInput(['value' => ''])}}
                </div>
                <div class="col-xs-12">
                    {{Form::submit('Сохранить' , array('class' => 'btn btn-success top-buffer bottom-buffer'))}}
                </div>
                {{ $form->end() }}
            </div>
            </p>
        </div>
        <div class="lk-partner-stat">
            <h3>Статистика</h3>

            <div class="row">
                <div class="col-xs-12 col-sm-4">
                    Дата регистрации: <u>{{ date('d.m.Y', strtotime($model->created_at)) }}</u><br/>
                    @if ($firmStat['ordersCount'] > 0)
                        Заказов: <u>{{ $firmStat['ordersCount'] }}</u> на сумму: <u>{{ $firmStat['ordersTotal'] }}
                            руб.</u><br/>
                        Отгружено товара на сумму: <u>{{ $firmStat['ordersTotalClose'] }}</u> руб.<br/>
                    @else
                        Заказов не было.<br/>
                    @endif
                    Ваш тип цен: <u>{{$firmStat['typeCost']}}</u>
                </div>
                <div class="col-xs-12 col-sm-3">
                    <u>Последняя отгрузка:</u><br/>
                    @if (isset($firmStat['lastOrder']->id))
                        Заказ № {{ $firmStat['lastOrder']->id }}
                        <br/>
                        от {{ date('d-m-Y', strtotime($firmStat['lastOrder']->date_create)) }}
                        <br/>
                        на сумму {{ $firmStat['lastOrder']->cost }} руб.
                    @else
                        Отгрузок не было.<br/>
                        Вы можете сделать заказ прямо сейчас.
                    @endif
                </div>
                <div class="col-xs-12 col-sm-5">
                    @if($ordersCount > 0)
                        <?php
                        $form = \widgets\ActiveForm::begin([
                                'action' => '/events/',
                                'options' => ['id' => 'verifyReportRequest', 'class' => 'form-inline']
                        ]);
                        ?>
                        <input type="hidden" name="Events[type]" value="2">
                        <div>
                            <u>Заказать акт сверки:</u><br/>
                            <a href="#" class="reportTime" data-report-days="30">за месяц</a>
                            <a href="#" class="reportTime" data-report-days="90">за квартал</a>
                            <a href="#" class="reportTime" data-report-days="365">за год</a>
                        </div>
                        <?php $date = date("Y-m-d");?>
                        <div class="form-group">
                            <label for="dateStart">c:</label>

                            <div class="input-group date datetimepicker">
                                <input type="text" class="form-control" name="Events[begin]" value="{{ $date }}" required/>
                                <span class="input-group-addon">
                                  <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="dateStop">по:</label>

                            <div class="input-group date datetimepicker">
                                <input type="text" class="form-control" name="Events[end]" value="{{ $date }}" required/>
                                <span class="input-group-addon">
                                  <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                        <input class="btn btn-primary-2d pull-right" type="submit" name="verifyReportRequest"
                               value="Заказать"/>
                        </form>
                        <br/>
                    @endif
                </div>
            </div>
        </div>
        <div class="lk-partner-orders">
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#panel1">История заказов</a></li>
                <li><a data-toggle="tab" href="#panel2">История отгрузок</a></li>
                <li><a data-toggle="tab" href="#panel3">Сертификаты PDF</a></li>
                <li><a data-toggle="tab" href="#panel4">Каталоги PDF</a></li>
            </ul>
            <div class="tab-content">
                <div id="panel1" class="tab-pane fade in active">
                    <h3>История заказов</h3>

                    <div>
                        {{ \modules\main\widgets\OrderList::widget(['orders' => $orders]) }}
                        @if($ordersCount > 5)
                            <a href='/user/orderlist' class='btn btn-success top-buffer'>
                                Посмотреть все заказы ({{ $ordersCount }})
                            </a>
                        @endif
                    </div>
                </div>
                <div id="panel2" class="tab-pane fade">
                    <h3>История отгрузок</h3>

                    <div>
                        {{ \modules\main\widgets\OrderList::widget(['orders' => $closedOrders]) }}
                        @if($closedOrdersCount > 5)
                            <a href='/user/order-close-list' class='btn btn-success top-buffer'>
                                Посмотреть все отгрузки ({{ $closedOrdersCount }})
                            </a>
                        @endif
                    </div>
                </div>
                <div id="panel3" class="tab-pane fade">
                    <h3>Сертификаты</h3>

                    <div>
                        {{ \helpers\Image::certificateFiles() }}
                    </div>
                </div>
                <div id="panel4" class="tab-pane fade">
                    <h3>Каталоги</h3>

                    <div>
                        <p>Каталог продукции диски КРКЗ: <a href="/files/krkz.pdf" target="_blank">скачать</a><br />
                            Каталог продукции диски МЕФРО: <a href="/files/mafro.pdf" target="_blank">скачать</a><br />
                            Каталог продукции диски ПРОНАР Польша: <a href="/files/pronar-poland.pdf" target="_blank">скачать</a><br />
                            Каталог продукции диски ПРОНАР Польша 2: <a href="/files/pronar-poland-2.pdf" target="_blank">скачать</a><br />
                            Каталог продукции диски ЧКПЗ: <a href="/files/chkpz.pdf" target="_blank">скачать</a><br />
                            Каталог продукции шины Advance: <a href="/files/Advance.pdf" target="_blank">скачать</a><br />
                            Каталог продукции шины Cordiant TyrexAllStee2010: <a href="/files/Cordiant TyrexAllStee2010.pdf" target="_blank">скачать</a><br />
                            Каталог продукции шины Cordiant Легковые и ЛГ 2010: <a href="/files/Cordiant legk 2010.pdf" target="_blank">скачать</a><br />
                            Каталог продукции шины Cordiant СРШ Грузовые и СХ: <a href="/files/Cordiant-gruz.pdf" target="_blank">скачать</a><br />
                            Каталог продукции шины HENGFENG: <a href="/files/HENGFENG.pdf" target="_blank">скачать</a><br />
                            Каталог продукции шины HIFLY: <a href="/files/HIFLY.pdf" target="_blank">скачать</a><br />
                            Каталог продукции шины Huasheng: <a href="/files/Huasheng.pdf" target="_blank">скачать</a><br />
                            Каталог продукции шины KrKZ: <a href="/files/KrKZ.pdf" target="_blank">скачать</a><br />
                            Каталог продукции шины Nortec: <a href="/files/Nortec.pdf" target="_blank">скачать</a><br />
                            Каталог продукции шины Triangle New Products: <a href="/files/Triangle New Products.pdf" target="_blank">скачать</a><br />
                            Каталог продукции шины Voltyre: <a href="/files/Voltyre.pdf" target="_blank">скачать</a><br />
                            Каталог продукции шины WindForce-TBR 2015: <a href="/files/WindForce-TBR 2015.pdf" target="_blank">скачать</a><br />
                            Каталог продукции шины АШК: <a href="/files/ashk.pdf" target="_blank">скачать</a></p>

                        <p>Каталоги до 2016 года</p>

                        <p>Каталог продукции Triangle: <a href="/files/triangle-new.pdf" target="_blank">скачать</a><br />
                            Каталог продукции Yokohama: <a href="/files/yokohama-2012-2013.pdf" target="_blank">скачать</a><br />
                            Каталог продукции Yoto: <a href="/files/yoto.pdf" target="_blank">скачать</a><br />
                            Каталог продукции Annaite: <a href="/files/annaite.pdf" target="_blank">скачать</a></p>

                    </div>
                </div>
            </div>
        </div>
        <br/><br/>

        <div class="lk-partner-info">
            <p class="top-buffer">
                Мы рады сообщить вам, что очень скоро мы сделаем из нашего сайта
                настоящий многофункциональный портал, на котором вы сможете:</p>
            <ul>
                <li>Видеть персональные скидки;</li>
                <li>Получать рекомендации и выбирать товары по срокам доставки;</li>
                <li>Видеть аналитику цен на все позиции;</li>
                <li>Читать и писать отзывы о товарах, а также смотреть рейтинги и обзоры шин;</li>
                <li>Покупать не только шины, но и много других товаров автомобильной тематики (Литые диски, аккумуляторы
                    и
                    т.д.);
                </li>
                <li>Общаться в персональным менеджером, закрепленным за вами, в режиме реального времени;</li>
                <li>Получите доступ к более чем 100 000 товарам ежедневно;</li>
                <li>Просматривать ваши персональные заказы, резервировать товар и отслеживать весь путь заказа до ваших
                    дверей;
                </li>
                <li>Получать дополнительные баллы, которые вы сможете тратить в наших магазинах или шиномонтаже;</li>
            </ul>
            <br/>

            <table>
                <tbody>
                <tr>
                    <td class="bg-line">
                        <img src="/img/scissors.png"/>
                    </td>
                </tr>
                </tbody>
            </table>
            <p>
                Мы провели огромную работу по анализу других сайтов и можем с уверенностью сказать, такой площадки
                как у нас - Не будет ни у кого.
                <br/>
                На данный момент наши программисты работают большой командой и каждый
                день трудятся, для того чтобы вы смогли пользоваться всеми необходимыми функциями, которые превратят
                покупку
                у нас в один простой клик мышкой.
            </p>

            <p>
                О доступности и запуске каждого пункта мы будем оповещать вас посредством электронной почты. А также, по
                мере запуска, опираясь на ваши комментарии по удобству
                использования системы, будем делать ее еще проще и функциональнее.
                <br/>
                Давайте дружить!
            </p>
            <table>
                <tbody>
                <tr>
                    <td class="bg-line">
                        <img src="/img/scissors.png"/>
                    </td>
                </tr>
                </tbody>
            </table>
            <br/><br/>

            <p>
                <span>
                    <b>
                        P.S. Мы будем
                        искренне рады, если вы расскажете о нашей задумке друзьям и коллегам :)
                    </b>
                </span>
            </p>

            <p>
                <span>
                    <b>
                        Чем больше пользователей оценят удобство сайта, тем еще лучше мы поймем удобство пользования
                        сайтом {{ \Config::get('app.host') }}
                    </b>
                </span>
            </p>
            {{ \HTML::script(URL::asset('http://yastatic.net/share/share.js')) }}
            <div class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="link"
                 data-yashareQuickServices="vkontakte,facebook,odnoklassniki,moimir,gplus"></div>
        </div>
        <div class="footer"></div>
    </div>
    {{ \HTML::script(URL::asset('/packages/moment/min/moment-with-locales.min.js')) }}
    {{ \HTML::script(URL::asset('/packages/datetimepicker/src/js/bootstrap-datetimepicker.js')) }}
    <script type="text/javascript">
        $(function () {
            bindAjaxSelectCity('#users-city_name', '#deliveryCityID');
        });
    </script>
@stop