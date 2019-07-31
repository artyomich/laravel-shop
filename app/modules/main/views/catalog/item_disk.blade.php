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

{{ Breadcrumbs::render('catalog', $product) }}

<br>
<!-- CONTENT -->
<div class="row">
    <article class="content-tires" itemscope itemtype="http://schema.org/Product">
        <h1 class="font-bold" itemprop="name">Диск {{ $product->properties->construction }} {{ $product->name }}</h1>

        <form class="content product-card" action="/cart/add/{{ $product->id }}/">
            <span class="tire-name hidden">{{ $product->name_short }}</span>
            <div class="row">
                <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 text-center">
                    @if (isset($product->images[0]))
                        <a href="{{ \helpers\Image::url($product->images[0]->filename, 1152, 768, ['watermark' => true]) }}"
                           class="hidden"
                           itemprop="image" title="{{ $product->name }}"></a>
                        <a href="#" class="border-none" data-toggle="modal" data-target="#cicModal">
                            {{ \helpers\Image::img($product->images[0]->filename, 136, 200, ['alt' => $product->name, 'class' => 'productPhoto']) }}
                        </a>
                    @else
                        <img src="/img/no_disk.jpg" class="productPhoto"/>
                    @endif

                    {{ \helpers\Image::certificateIcon($product->properties->brand) }}

                    <div itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                        <div class="price-stock bottom-buffer"
                             data-count="{{ $product->balance ? $product->balance->balance : 0 }}">
                            @if ($product->balance && $product->is_visible)
                                <link itemprop="availability" href="http://schema.org/InStock"/>
                                в наличии
                                <div>{{ \models\ProductsBalances::formatBalance($product->balance->balance) }}</div>
                            @else
                                <link itemprop="availability" href="http://schema.org/OutOfStock"/>
                                нет в наличии
                            @endif
                        </div>
                        @if (strtolower($product->properties->brand) === 'nortec')
                            {{ \widgets\modals\NortecAdModal::a(['class' => 'thumbnail videoLink']) }}
                        @endif
                        {{ \helpers\Image::certificate($product->properties->brand) }}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 text-center">
                    @if ($product->balance && $product->is_visible)
                        <link itemprop="availability" href="http://schema.org/InStock"/>
                        <div class="valueBlock col-xs-12">
                            <div class="col-xs-12 col-sm-8 col-md-8 col-lg-6">
                                <div class="price">
                                        <span class="m-t-2" itemprop="price"
                                              content="{{ $product->balance->cost }}">{{ $product->balance->cost }}</span>
                                    <span itemprop="priceCurrency" content="RUB" class="rur m-t-4">a</span>
                                    <span> × </span>
                                    <span class="quantity">
                                        <input type="number" class="form-control product-num-tires" name="count"
                                               value="4"
                                               min="1" step="1" data-cost="{{ $product->balance->cost }}"/>
                                    </span>
                                    <span> = </span>
                                    <span class="m-t-2">{{ $product->balance->cost * 4 }}</span>
                                    <span class="rur m-t-4">a</span>
                                </div>
                            </div>
                            <div class="buy col-xs-12 col-sm-4 col-md-4 col-lg-6">
                                <button type="button" class="btn btn-primary btn-cart-add font12">В корзину</button>
                            </div>
                            <div class="message">
                                <span id="notEnough" style="display: none">На складе не достаточно дисков.<br>Пожалуйста, уточняйте сроки их поставки у менеджера.</span>
                            </div>
                        </div>
                    @else
                        <link itemprop="availability" href="http://schema.org/OutOfStock"/> нет в наличии
                    @endif
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <table class="table table-condensed table-striped table-properties table-hover">
                                    <tbody>
                                    @foreach ($product->properties->getColumnsNames() as $property)
                                        @if (!empty($product->properties->$property) && $property != 'product_id' && $property != 'brand' && $property != 'manufacturer' && $property != 'model' && $property != 'size')
                                            <tr>
                                                <td>{{ $product->properties->getAttributeLabel($property) }}:</td>
                                                <td>{{ $product->properties->$property }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                </div>
                <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 m-t-4">
                    @if ($product->balance)
                        <div>
                        <span class="glyphicon glyphicon-chevron-left hidden-xs"
                              style="float:left; font-size:42px; color:#fbbd09;"></span>
                        <span class="glyphicon glyphicon-chevron-up hidden-sm hidden-md hidden-lg"
                              style="float:left; font-size:40px; color:#fbbd09;"></span>
                            <div>
                                <div class="small">Цена действительна при покупке через интернет-магазин, сумма
                                    скидки <span
                                            class="font-bold product-cost-no-discount">{{ ((ceil($product->balance->cost * 102.5 / 1000) *  10) - $product->balance->cost)*4 }}</span><span
                                            class="rur">a</span></div>
                                <div class="small">Цена в магазине без заказа на сайте <span
                                            class="font-bold product-total-cost-no-discount">{{ (ceil($product->balance->cost * 102.5 / 1000) *  10) * 4}}</span><span
                                            class="rur">a</span></div>
                            </div>
                        </div>
                        @endif
                                <!-- Другие размеры модели -->
                        @if (count ($sizes))
                            <br>
                            <h4>Другие типоразмеры автошины {{ $product->name_short }}:</h4>
                            <div>
                                <table class="table table-condensed table-striped table-properties table-hover">
                                    <tbody>
                                    @foreach ($sizes as $size)
                                        <tr>
                                            <td><a href="/catalog/{{ $size->categories->alias }}/{{ $size->alias }}/"
                                                   class="">
                                                    {{ $size->size }}
                                                    @if (!empty($size->properties->layouts_normal))
                                                        / н.с. {{ $size->properties->layouts_normal }}
                                                    @endif
                                                    @if (!empty($size->properties->completeness))
                                                        / {{ $size->properties->completeness }}
                                                    @endif
                                                    - {{ $size->cost }} <span class="rur">a</span>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @endif
                                    <!-- /Другие размеры модели -->
                </div>
            </div>
        </form>
    </article>
    <!-- TABS -->
    <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9 content-tires">
        <div class="product-tabs">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                @if (!empty($analogs))
                    <li class="small active"><a href="#productAnalogs" role="tab" data-toggle="tab">Аналоги</a></li>
                    @if (!empty($product->description))
                        <li class="small"><a href="#productDesc" role="tab" data-toggle="tab">Описание</a></li>
                    @endif
                    <li class="small"><a href="#productOpinions" role="tab" data-toggle="tab">Отзывы</a></li>
                @elseif (!empty($product->description))
                    <li class="small active"><a href="#productDesc" role="tab" data-toggle="tab">Описание</a></li>
                    <li class="small"><a href="#productOpinions" role="tab" data-toggle="tab">Отзывы</a></li>
                @else
                    <li class="small active"><a href="#productOpinions" role="tab" data-toggle="tab">Отзывы</a></li>
                @endif
                <li class="small"><a href="#productOptions" role="tab" data-toggle="tab">Характеристики</a></li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content tab-content-border">
                <!-- ANALOGS -->
                @if (!empty($analogs))
                    <div role="tabpanel" class="tab-pane active" id="productAnalogs">
                        <div class="owl-carousel owl-theme" id="productAnalogsInner">
                            @foreach ($analogs as $analog)
                                <div class="item">
                                    @include('catalog.row_item', ['product' => $analog, 'isAnalogs' => true])
                                </div>
                            @endforeach
                        </div>
                        {{ HTML::style(URL::asset('/js/owl-carousel/owl.carousel.css?v=' . $version)) }}
                        {{ HTML::style(URL::asset('/js/owl-carousel/owl.theme.css?v=' . $version)) }}
                        {{ HTML::script(URL::asset('/js/owl-carousel/owl.carousel.js')) }}
                        <script type="text/javascript">
                            $(document).ready(function () {

                                var owl = $("#productAnalogsInner");

                                owl.owlCarousel({
                                    items: 4, //10 items above 1000px browser width
                                    itemsDesktop: [1000, 4], //5 items between 1000px and 901px
                                    itemsDesktopSmall: [900, 3], // between 900px and 601px
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
                    </div>
                    @endif
                            <!-- /ANALOGS -->

                    @if (!empty($product->description))
                        <article role="tabpanel" class="tab-pane @if (count ($analogs)<1) active @endif"
                                 id="productDesc" itemprop="description">
                            {{ $product->description }}
                        </article>
                    @endif

                    <div role="tabpanel" class="tab-pane @if (empty($product->description) && empty($analogs)) active @endif" id="productOpinions">
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
                            <br>
                            <a href="https://market.yandex.ru/product/{{$lastMarketId}}/reviews"
                               target="_blank" rel="nofollow" class="btn btn-block btn-link">
                                Все отзывы на Яндекс.Маркете
                            </a>
                        @endif
                    </div>
                    <div role="tabpanel" class="tab-pane"
                         id="productOptions">

                        <table class="table table-condensed table-striped table-properties table-hover">
                            <tbody>
                            @foreach ($product->properties->getColumnsNames() as $propName)
                                @if (!empty($product->properties->$propName) && $propName != 'product_id')
                                    <tr>
                                        <td>{{ $product->properties->getAttributeLabel($propName) }}:</td>
                                        <td>{{ $product->properties->$propName }}</td>
                                    </tr>
                                @endif
                            @endforeach
                            <tr>
                                <td>Код номенклатуры:</td>
                                <td>{{ $product->id_1c }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
            </div>
        </div>
        <div class="" id="productInfoColumn">
            <section class="col-xs-6 col-sm-6 col-md-6 col-lg-6" id="productInfoB2b">
                <div class="row bottom-buffer text-center font13" id="productConditionsBlock">
                    Условия:
                    <a href="#Оплата" data-toggle="modal" data-target="#conditionsModal"
                       class="text-dashed">Оплата</a>,
                    <a href="#Доставка" data-toggle="modal" data-target="#conditionsModal"
                       class="text-dashed">Доставка</a>,
                    <a href="#Гарантии" data-toggle="modal" data-target="#conditionsModal"
                       class="text-dashed">Гарантия</a>
                </div>
                <div class="row bottom-buffer text-center font13">
                    @if (!empty($city->phones))
                        Офис: {{ $city->phones }}
                    @endif
                </div>
                <div class="bottom-buffer text-center">
                    {{ \widgets\modals\ReqCallModal::a([], ['buttonName' => 'Заказать звонок специалиста']) }}
                </div>
            </section>
            <section class="col-xs-6 col-sm-6 col-md-6 col-lg-6" id="productInfoCalc">
                <a href="/catalog/calculator/" title="" data-toggle="tooltip" data-placement="bottom"
                   data-original-title="Здесь вы можете самостоятельно рассчитать возможные варианты шин для вашего автомобиля">
                    <div style="margin-left:125px; padding-top: 25px; font-size: 13px;">
                        <span style="border-bottom: 1px dashed">Новый характер вашего авто за пару кликов</span>
                    </div>
                </a>
            </section>
        </div>
    </div>
    <!-- TABS -->
    <section class="col-xs-12 col-sm-12 col-md-3 col-lg-3" id="productInfoDostavka">
        {{ \modules\deliverycalc\widgets\Calculator::widget(['product' => $product]) }}
        <div class="hidden-xs hidden-sm" id="banners">
            {{ \modules\main\widgets\Banners::widget() }}
        </div>
    </section>

    {{ \modules\main\widgets\ProductInstructions::widget(['product' => $product, 'pageAlias'=>"kak-kupit-disk"]); }}
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
                                <div class="col-xs-6 font-bold product-total-cost">{{ $product->balance->cost * 4 }}
                                    <span class="rur">a</span>
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
@stop