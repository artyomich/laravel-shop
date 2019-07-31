<?php
/**
 * @var \models\Products $product
 */
?>
<div class="col-xs-12 product-inline">
    <div class="row" prodRows="{{$product->id}}">
        <form class="col-tire" action="/cart/add/{{ $product->id  }}/">
            <div class="col-sm-1 col-xs-5">
                <a href="/catalog/{{ $product->categories->alias }}/{{ $product->alias }}/" class="thumbnail">
                @if (isset($product->images[0]))
                    {{ \helpers\Image::img($product->images[0]->filename, 136, 200, [
                    'alt' => $product->name,
                    'strict' => true,
                    'title' => \helpers\StringHelper::hasTubeRimeTape($product->properties->completeness),
                    'data-toggle' => 'tooltip',
                    'data-placement' =>  isset($isAnalogs) ? 'bottom' : 'top'
                    ]) }}
                @else
                    <img src="/img/no_photo.jpg"
                         title="{{  \helpers\StringHelper::hasTubeRimeTape($product->properties->completeness) }}"
                         data-toggle="tooltip" data-placement="{{ isset($isAnalogs) ? 'bottom' : 'top' }}"/>
                @endif
                    <div class="thumbnail-pre"></div>

                <?php
                switch ($product->season) {
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

                if ($product->properties->spikes) {
                    echo '<span class="icon-season season-spikes" title="Шипованная шина" data-toggle="tooltip"  data-placement="left"></span>';
                }
                ?>
                {{ \helpers\Image::certificateIcon($product->properties->brand) }}
                <span class="num-list-comments">
                <i class="glyphicon glyphicon-comment"></i> ({{ $product->getNumCheckedOpinions() }})
                </span>
                </a>
            </div>
            <div class="col-sm-3 col-xs-7 prop-list">
                <a href="/catalog/{{ $product->categories->alias }}/{{ $product->alias }}/"><p>{{$product->name}}</p></a>
                <?php $firstNameProp = ['season', 'index_speed', 'index_load', 'layouts_normal']; ?>

                <div style="min-height: 67px;">
                    <dl class="dl-horizontal prop-inline-list">
                        @foreach ($firstNameProp as $first)
                            @if (!empty($product->properties->$first))
                                <dt>{{ $product->properties->getAttributeLabel($first) }}:</dt>
                                <dd>{{ $product->properties->$first }}</dd>
                            @endif
                        @endforeach
                        @foreach ($product->properties->getColumnsNames() as $propName)
                            @if (!empty($product->properties->$propName) && $propName != 'product_id' && !in_array($propName, $firstNameProp))
                                <dt class="more-prop">{{ $product->properties->getAttributeLabel($propName) }}:</dt>
                                <dd class="more-prop">{{ $product->properties->$propName }}</dd>
                            @endif
                        @endforeach
                        <dt class="more-prop">Код номенклатуры:</dt>
                        <dd class="more-prop">{{ $product->id_1c }}</dd>
                    </dl>
                </div>
                <div class="open-more-prod-info" prodRow="{{$product->id}}">
                    <span class="open-more-prop">Все характеристки</span>
                    <span class="close-more-prop">Свернуть</span>
                </div>

            </div>
            <div class="col-sm-8 col-xs-12 day-cost-list">
                @if ($product->balance && $product->is_visible && $product->balance->cost0 > $product->balance->cost)
                    <div class="cost0">
                        Розница : {{$product->balance->cost0}} руб.
                    </div>
                @endif
                <?php
                if (($product->balance)) {
                    $balance[] = ['day' => 0, 'balance' => $product->balance->balance_full,'balance_full' => $product->balance->balance, 'cost' => $product->balance->cost, 'transportCost' => 0, 'vendor' => null];
                }

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
                    <ul class="day-cost">
                        @for($i=0; $i < count($balance);$i++)
                            <li {{$i > 2 ? 'class="more-prop"' : ''}}>
                                <div class="row-tir-days" style="<?php if ($balance[$i]['day'] == 0) {
                                    echo 'color:green;';
                                }?>">
                                    {{ $balance[$i]['vendor'] == 0 ? 'Есть' : ($balance[$i]['day'] == 0 ? 1:$balance[$i]['day']) .' дн' }}
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
                                    <span class="tir-total-cost">{{ $balance[$i]['cost']}} руб.</span>
                                    &nbsp;<b>х</b>&nbsp;
                                </div>
                                <div class="tir-col">
                                    <input type="number"
                                           size="3"
                                           value=""
                                           tirCost="{{ $balance[$i]['cost'] }}"
                                           transportCost="{{ $balance[$i]['transportCost'] }}"
                                           cityName="{{$city->name}}"
                                           min="0"
                                           max="{{ $balance[$i]['balance'] }}"
                                           class="form-control total-tir-to-cart"
                                           style="height: 25px; font-size: 12px; padding: 0;text-align: right"
                                           placeholder="0"/>
                                    <input type="hidden" vendor="vendor" value="{{$balance[$i]['vendor']}}">
                                </div>
                                <div class="tir-total">
                                    &nbsp;&nbsp;=&nbsp;
                                    <span class="total-tir-cost">0 руб.</span>
                                    @if ($balance[$i]['day'] != 0)
                                        <span class="total-tir-transport-cost"> + <span data-toggle="tooltip"
                                                                                        data-placement="bottom"
                                                                                        data-original-title="Стоимость доставки в г.{{$city->name}}">0 руб.</span></span>
                                    @endif
                                </div>
                                <div class="row-itog">&nbsp;&nbsp;Итого: 0 руб.</div>
                                <div class="sub-button">
                                <span class="input-group-btn">
                                    <input type="submit" name="addToCart" value="+" title="Добавить в корзину"
                                           class="btn disabled"/>
                                </span>
                                </div>
                                <div style="clear: both; float: none;"></div>
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
                            {{--<div style="text-align: right; width: 100%;">--}}
                            {{--<input type="button" name="addAllRowToCard" class="btn" value="Добавить все" />--}}
                            {{--</div>--}}

                        </li>
                    </ul>
                @endif
                @if(isset($balance) and count($balance)>3)
                    <div class="open-more-dop-info" prodRow="{{$product->id}}"><span
                                class="open-more-prop">Еще варианты</span></div>
                @endif
            </div>
        </form>
    </div>
<hr>
</div>
