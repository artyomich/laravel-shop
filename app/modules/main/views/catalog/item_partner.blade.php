<?php
/**
 * @var \models\Products $product
 */
?>
@extends('layouts.' . ($isAjax ? 'post' : 'default'))

@section('content')

        <!-- CATALOG -->
{{ \modules\main\widgets\FilterSimple::widget(['categoryAlias' => $product->categories->alias]) }}
        <!-- /CATALOG -->
<br/>
<!-- CONTENT -->
<div class="row">

    <section class="col-xs-12 content-tires" itemscope itemtype="http://schema.org/Product">
        <h1 class="font-bold" itemprop="name">{{ $product->name }}</h1>

        <form class="content product-card-partner" action="/cart/add/{{ $product->id }}/">
            <div class="row bottom-buffer ">
                <div class="col-xs-3 text-center">
                    @if (isset($product->images[0]))
                        <a href="{{ \helpers\Image::url($product->images[0]->filename, 1152, 768, ['watermark' => true]) }}"
                           class="hidden"
                           itemprop="image" title="{{ $product->name }}"></a>
                        <a href="#" class="border-none" data-toggle="modal" data-target="#cicModal">
                            {{ \helpers\Image::img($product->images[0]->filename, 136, 200, ['alt' => $product->name]) }}
                        </a>
                    @else
                        <img src="/img/no_photo.jpg" class="productPhoto"/>
                    @endif

                    {{ \helpers\Image::certificateIcon($product->properties->brand) }}

                    <?php
                    switch ($product->properties->season) {
                        case 'Лето':
                            echo '<span class="icon-season season-summer " title="Летняя шина" data-toggle="tooltip"  data-placement="left"></span>';
                            break;
                        case 'Зима':
                            echo '<span class="icon-season season-winter" title="Зимняя шина" data-toggle="tooltip"  data-placement="left"></span>';
                            break;
                        case 'Всесезонный':
                            echo '<span class="icon-season season-all-season" title="Всесезонная шина" data-toggle="tooltip"  data-placement="left"></span>';
                            break;
                    }

                        echo $product->properties->spikes ? '<span class="icon-season season-spikes" title="Шипованная шина" data-toggle="tooltip"  data-placement="left"></span>' : '';
                    ?>
                    @if ($product->balance && $product->is_visible && $product->balance->cost0 > $product->balance->cost)
                        <div class="cost0">
                            Розница:<br/> {{$product->balance->cost0}} руб.
                        </div>
                    @endif

                </div>
                <div class="col-xs-9">

                    <dl class="dl-horizontal prop-cart-partner">
                        @foreach ($product->properties->getColumnsNames() as $propName)
                            @if (!empty($product->properties->$propName) && $propName != 'product_id')

                                <dt>{{ $product->properties->getAttributeLabel($propName) }}:</dt>
                                <dd>{{ $product->properties->$propName }}</dd>

                            @endif
                        @endforeach

                        <dt>Код номенклатуры:</dt>
                        <dd>{{ $product->id_1c }}</dd>

                    </dl>


                    <div class="partner-cert">
                        @if (strtolower($product->properties->brand) === 'nortec')
                            {{ \widgets\modals\NortecAdModal::a(['class' => 'thumbnail videoLink']) }}
                        @endif
                        <a href="/files/reliable_supplier.jpg"
                           data-lightbox="image-1"
                           data-toggle="tooltip" data-placement="right" data-html="true"
                           data-template="<div class='tooltip' role='tooltip'><div class='tooltip-arrow'></div><div class='tooltip-inner' id='certTooltip'></div></div>"
                           title="" class="thumbnail certLink"
                           data-original-title="По результатам оценки деятельности поставщиков продукции (работ, услуг), участвующих в Государственных и коммерческих програмах (проектах), ООО &Prime;
                               Первая Объединенная Шинная Компания&Prime; признана Надежной Российской компанией и внесена в
                            Федеральный реестр Надежных Российских Компаний" aria-describedby="tooltip241533">Надежный
                            поставщик</a>
                        {{ \helpers\Image::certificate($product->properties->brand) }}
                    </div>


                </div>
            </div>


            <ul class="day-cost product-inline">
                <li class="ul-theader">
                    <div class="row-tir-days">Наличие</div>
                    <div class="tir-balance">Доступность</div>
                    <div class="tir-cost">Цена</div>
                    <div class="tir-col">Заказ</div>
                    <div class="tir-total">Общая сумма</div>
                    <div class="row-itog">Итого</div>
                    <div class="sub-button"></div>
                </li>
                <?php
                $product->balance && $product->is_visible ? $balance[] = [
                        'day' => 0,
                        'balance_full' => $product->balance->balance,
                        'balance' => $product->balance->balance_full,
                        'cost' => $product->balance->cost,
                        'transportCost' => 0,
                        'vendor' => null] : '';
                if ($city->name === 'Москва' and !isset($balance[0])) {

                    foreach ($product->vendorsBalances as $item) {
                        $balance[] = [
                                'day' => $item->deliveryPeriodMax,
                                'balance_full' => $item->balance,
                                'balance' => $item->balance,
                                'cost' => $item->costMarkUp,
                                'transportCost' => $item->delivery,
                                'vendor' => $item->vendor_id,
                        ];
                    }
                }
                ?>
                @if(isset($balance))
                    @for($i=0; $i < count($balance);$i++)
                        <li class="{{ $balance[$i]['day'] == 0 ? 'my-row' : 'order-row' }}">

                            <div class="row-tir-days" style="{{$balance[$i]['day'] == 0?'color:green;':''}}">

                                {{ $balance[$i]['day'] == 0 ? 'Есть' : $balance[$i]['day'] .' дн' }}

                            </div>
                            <div class="tir-balance">
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
                                    {{$balance[$i]['balance']}} в наличии
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
                                    @if($balance[$i]['balance_full'] < $balance[$i]['balance'])
                                            ({{$balance[$i]['balance']-$balance[$i]['balance_full']}} резерв)
                                    @endif
                                </span>

                            </div>
                            <div class="tir-cost">
                                <span class="tir-total-cost">{{ $balance[$i]['cost'] }} руб.</span>

                            </div>
                            <div class="tir-col">
                                <input type="number"
                                       size="3"
                                       value=""
                                       tirCost="{{ $balance[$i]['cost'] }}"
                                       transportCost="{{ $balance[$i]['transportCost'] }}"
                                       cityName="{{$city->name}}"
                                       min="0"
                                       max="{{ $balance[$i]['balance'] }}" class="form-control total-tir-to-cart"
                                       placeholder="0"/>
                                <input type="hidden" vendor="vendor" value="{{$balance[$i]['vendor']}}">
                            </div>
                            <div class="tir-total">
                                <span class="total-tir-cost">0 руб.</span>
                                @if ($balance[$i]['day'] != 0)
                                    <span class="total-tir-transport-cost"> + <span data-toggle="tooltip"
                                                                                    data-placement="bottom"
                                                                                    data-original-title="Стоимость доставки в г.{{$city->name}}"
                                                                                    style="border-bottom: 1px dashed; cursor:pointer">0 руб.</span></span>
                                @endif

                            </div>
                            <div class="row-itog">&nbsp;&nbsp;Итого: 0 руб.</div>
                            <div class="sub-button">
                            <span class="input-group-btn">
                                <input type="submit" name="addToCart" value="В корзину" title="Добавить в корзину"
                                       class="btn disabled"/>
                            </span>
                            </div>

                        </li>
                    @endfor
                    <li class="tir-row-total">
                        <span class="font-bold">Всего: </span>
                        <span class="all-tir-row-col">0 шин</span>
                        &nbsp;
                        <span class="font-bold">На сумму: </span>
                        <span class="all-tir-sum">0 руб</span>
                        &nbsp;
                        <span class="font-bold">Доставка: </span>
                        <span class="all-tir-transport"> 0 руб</span>
                        &nbsp;
                        <span class="font-bold">Итого: </span>
                        <span class="all-row-sum">0 руб</span>

                        {{--<div style="text-align: right; width: 100%;">
                            <input type="button" name="addAllRowToCard" class="btn" value="Добавить все"/>
                        </div>--}}

                    </li>
            </ul>
            @else
                <p class="text-center ">нет в наличии</p>
            @endif

        </form>
        <!-- ANALOGS -->
        @if (count ($analogs))
            <br/>
            <h3>Аналогичные шины</h3>
            <div id="productAnalogs" class="owl-carousel owl-theme">
                @foreach ($analogs as $analog)
                    <div class="item">
                        @include('catalog.row_item', ['product' => $analog, 'isAnalogs' => true])
                    </div>
                @endforeach
            </div>
            {{ HTML::style(URL::asset('/js/owl-carousel/owl.carousel.css?v=' . $version)) }}
            {{ HTML::style(URL::asset('/js/owl-carousel/owl.theme.css?v=' . $version)) }}
            {{ HTML::script(URL::asset('js/owl-carousel/owl.carousel.js')) }}
            <script type="text/javascript">
                $(document).ready(function () {

                    var owl = $("#productAnalogs");

                    owl.owlCarousel({
                        items: 6, //10 items above 1000px browser width
                        itemsDesktop: [1000, 4], //5 items between 1000px and 901px
                        itemsDesktopSmall: [900, 3], // betweem 900px and 601px
                        itemsTablet: [600, 2], //2 items between 600 and 0
                        itemsMobile: false, // itemsMobile disabled - inherit from itemsTablet option
                        navigation: false,
                        pagination: false
                    });

                    // Navigation buttons
                    @if (count ($analogs) > 4)
                            $('<a class="next"></a>').appendTo(owl).click(function () {
                        owl.trigger('owl.next');
                    });
                    $('<a class="prev"></a>').appendTo(owl).click(function () {
                        owl.trigger('owl.prev');
                    });
                    @endif
                });
            </script>
            @endif
                    <!-- /ANALOGS -->

            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                @if (!empty($product->description))
                    <li class="active"><a href="#productDesc" role="tab" data-toggle="tab">Описание</a></li>
                    <li><a href="#productOpinions" role="tab" data-toggle="tab">Отзывы</a></li>
                @else
                    <li class="active"><a href="#productOpinions" role="tab" data-toggle="tab">Отзывы
                            ({{ count($checkedOpinions) }})</a></li>
                @endif
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                @if (!empty($product->description))
                    <article role="tabpanel" class="tab-pane active" id="productDesc" itemprop="description">
                        {{ $product->description }}
                    </article>
                @endif

                <div role="tabpanel" class="tab-pane @if (empty($product->description)) active @endif"
                     id="productOpinions">
                    <div class="average-rating">
                        <button class="btn btn-primary-2d pull-right" data-toggle="modal"
                                data-target="#leaveOpinionModal">Оставить отзыв
                        </button>
                        @if (count($checkedOpinions))
                            Средняя оценка товара:
                            <span class="opinion-stars">
                                <div class="opinion-stars-bg opinion-stars-empty"></div>
                                <div class="opinion-stars-bg opinion-stars-notempty"
                                     style="width:{{ round($product->getAverageRating() / 5 * 100) }}%;"></div>
                            </span>
                        @else
                            К сожалению отзывов о данном товаре еще нет :(
                        @endif
                    </div>

                    {{--*/ $lastMarketId = null; /*--}}

                    @foreach($checkedOpinions as $item)
                        <div class="panel">
                            <div>
                                <span class="text-bold">{{ !empty($item->user_fullname) ? $item->user_fullname : 'Анонимный' }}</span>,
                                <small class="text-muted">{{ date('d.m.Y', strtotime($item->date_create)) }}</small>
                            </div>
                        <span class="opinion-stars">
                            <div class="opinion-stars-bg opinion-stars-empty"></div>
                            <div class="opinion-stars-bg opinion-stars-notempty"
                                 style="width:{{ round($item->rating / 5 * 100) }}%;"></div>
                        </span>

                            <div class="opinion-body bottom-buffer">
                                <div>
                                    <span class="text-bold">Достоинства</span>: {{ !empty($item->user_advantages) ? $item->user_advantages : 'Нет' }}
                                </div>
                                <div>
                                    <span class="text-bold">Недостатки</span>: {{ !empty($item->user_disadvantages) ? $item->user_disadvantages : 'Нет' }}
                                </div>
                                @if (!empty($item->user_comment))
                                    <div>
                                        <span class="text-bold">Комментарий</span>: {{ $item->user_comment }}
                                    </div>
                                @endif
                                @if ($item->market_model_id)
                                    {{--*/ $lastMarketId = $item->market_model_id; /*--}}
                                @endif
                            </div>
                        </div>
                    @endforeach

                    @if ($lastMarketId)
                        <br/>
                        <a href="https://market.yandex.ru/product/{{$lastMarketId}}/reviews"
                           target="_blank" rel="nofollow" class="btn btn-block btn-link">
                            Все отзывы на Яндекс.Маркете
                        </a>
                    @endif
                </div>
            </div>
    </section>


</div>
<!-- /CONTENT -->
<div class="footer"></div>

<!-- TELL ME MODAL -->
<div class="modal fade" id="conditionsModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                            class="sr-only">Close</span></button>
                <h4 class="modal-title">{{ $pages[0]->description }}</h4>
            </div>
            <div class="modal-body">
                <div id="conditionsModalCarousel" class="carousel">
                    <!-- Wrapper for slides -->
                    <div class="carousel-inner" role="listbox">
                        @foreach($pages as $k => $page)
                            <div class="item{{ $k == 0 ? ' active' : '' }}" data-modal-title="{{ $page->description }}">
                                {{ $page->body }}
                            </div>
                        @endforeach
                    </div>

                    <!-- Controls -->
                    <a class="left carousel-control" href="#conditionsModalCarousel" role="button" data-slide="prev">
                        <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                        <span class="sr-only"></span>
                    </a>
                    <a class="right carousel-control" href="#conditionsModalCarousel" role="button" data-slide="next">
                        <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                        <span class="sr-only"></span>
                    </a>

                    <!-- Indicators -->
                    <ul class="carousel-indicators">
                        @foreach($pages as $k => $page)
                            <li data-target="#conditionsModalCarousel"
                                data-slide-to="{{ $k }}"{{ $k == 0 ? ' class="active"' : '' }}>
                                <a href="#{{ $page->name }}">{{ $page->name }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- /TELL ME MODAL -->

<!-- CARD IN CARD MODAL -->
<div class="modal fade" id="cicModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content product-card" action="/cart/add/{{ $product->id }}/">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                            class="sr-only">Close</span></button>
                <h3 class="modal-title">{{ $product->name }}</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-6">
                        <div class="thumbnail border-none">
                            @if (isset($product->images[0]))
                                {{ \helpers\Image::img($product->images[0]->filename, 1152, 768) }}
                            @endif
                        </div>
                    </div>
                    <div class="col-xs-6">
                        @if ($product->balance)
                            <div class="row bottom-buffer">
                                <div class="col-xs-6 form-control-static">Количество шин:</div>
                                <div class="col-xs-6">
                                    <input type="number" class="form-control product-num-tires" name="count" value="4"
                                           min="1" step="1"
                                           data-cost="{{ $product->balance->cost }}"/>
                                </div>
                            </div>
                            <div class="row bottom-buffer">
                                <div class="col-xs-6">Итоговая стоимость:</div>
                                <div class="col-xs-6 font-bold product-total-cost">{{ $product->balance->cost * 4 }}руб.
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary btn-cart-add btn-cart-add-redirect">В корзину
                            </button>
                        @else

                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- /CARD IN CARD MODAL -->

<!-- LEAVE OPINION MODAL -->
<div class="modal fade" id="leaveOpinionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                            class="sr-only">Close</span></button>
                <h4 class="modal-title">Оставить отзыв</h4>
            </div>
            <div class="modal-body">
                @include('catalog.opinion', ['product' => $product, 'opinion' => new \models\ProductsOpinions()])
            </div>
        </div>
    </div>
</div>
<!-- /LEAVE OPINION MODAL -->

<!-- B2B MODAL -->
<div class="modal fade" id="b2bModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">Закрыть <span aria-hidden="true">&times;</span><span
                            class="sr-only">Close</span></button>
                <h4 class="modal-title">ДЛЯ ВАШЕГО БИЗНЕСА</h4>
                <small>POSHK B2B - Специальные условия для корпоративных клиентов</small>
            </div>
            <div class="modal-body">
                <div class="info-item">
                    <strong>Что мы предлагаем корпоративным клиентам</strong>
                    <ul>
                        <li>Огромный ассортимент товаров в наличии</li>
                        <li>Выгодная цена на любой товар</li>
                        <li>Профессиональная и персональная консультация</li>
                        <li>Доставка по России</li>
                    </ul>
                </div>
                <div class="info-item">
                    <strong>Как стать клиентом</strong>
                    <ul>
                        <li>Зарегистрируйтесь</li>
                        <li>Заполните все поля, необходимые для выставления счета (они отмечены *)</li>
                        <li>Выберите товар и положите его в корзину</li>
                        <li>Оформите заказ и получите счет</li>
                    </ul>
                </div>
                <div class="info-item">
                    <strong>Как оплатить заказ</strong>
                    <ul>
                        <li>Форма оплаты: наличная и безналичная</li>
                        <li>Безналичный расчет по счету или в рамках договора поставки товара</li>
                    </ul>
                </div>
                <br/>

                <div class="row">
                    <div class="col-xs-4">
                        <a href="#" class="btn btn-primary btn-block" data-dismiss="modal" data-toggle="modal"
                           data-target="#AuthorizationModal">ЗАРЕГИСТРИРОВАТЬСЯ</a>
                    </div>
                    <div class="col-xs-7 col-xs-offset-1 text-muted">
                        Нажимая кнопку "Зарегистрироваться", я подтверждаю свое согласие с публичной афертой.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /LEAVE OPINION MODAL -->


@stop