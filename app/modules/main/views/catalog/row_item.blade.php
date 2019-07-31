<?php
/**
 * @var \models\Products $product
 */
?>

<form class="col-tire" action="/cart/add/{{ $product->id  }}/">
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
        <div class="tire-name">{{ $product->name_short }}</div>
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

    <div class="tire-size">{{ $product->size }}</div>
    @if ($product->properties->layouts_normal)
        <div class="tire-norm" data-toggle="tooltip" data-placement="top" title="Норма слойности">
            н.с. {{ $product->properties->layouts_normal }}</div>
    @else
        <div class="tire-norm"></div>
    @endif

    <small class="balance">В наличии: {{ \models\ProductsBalances::formatBalance($product->balance) }}</small>
    @if ($product->isSpecCost())
        <div class="lowprice"></div>
    @endif
    <div class="cost0">
        @if (isset($product->cost0) AND $product->cost0 > $product->cost)
            {{ $product->cost0 }} руб.
        @endif
    </div>
    <div class="cost">{{ $product->cost }} руб.</div>
    {{--<div class="row count">
        <label>
            <div class="col-xs-10 col-xs-offset-1">Количество:</div>
            <div class="col-xs-6 col-xs-offset-3"><input type="number" name="count" class="form-control" value="4"/></div>
        </label>
    </div>
    <input type="submit" value="{{ $product->balance > 0 ? 'КУПИТЬ' : 'ЗАКАЗАТЬ' }}"
    class="btn btn-primary btn-cart-add"/>--}}

    <div class="row cost">
        <div class="col-xs-10 col-xs-offset-1">
            <div class="input-group">
                <input type="number" name="count" class="form-control" value="4"/>
                <span class="input-group-btn">
                    <input type="submit"
                           value="{{ $product->balance > 0 ? 'В КОРЗИНУ' : 'ЗАКАЗАТЬ' }}"
                           class="btn btn-primary btn-cart-add hidden-xs"/>
                    <button type="submit" class="btn btn-primary btn-cart-add visible-xs"><i
                                class="glyphicon glyphicon-shopping-cart"></i></button>
                </span>
            </div>
            <!-- /input-group -->
        </div>
    </div>
</form>
