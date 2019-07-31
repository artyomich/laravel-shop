@extends('layouts.' . ($isAjax ? 'post' : 'default'))

@section('content')

{{ \modules\main\widgets\FilterSimple::widget(['categoryAlias' => isset($category) ? $category->alias : '', 'input' => isset($input) ? $input : []]) }}

{{ Breadcrumbs::render('catalog', (isset($category) ? $category: '')) }}

<!-- CONTENT -->
<div class="row content">
    <section class="{{ $partner ? 'col-sx-12 content-tires' : 'col-lg-9 col-md-9 col-sx-12 content-tires' }}">
        <!-- TIRES -->
        <div class="row">
            @if(!count($products))
            <br/>
            <br/>
            <h4 class="text-center text-danger">
                К сожалению по данному запросу ничего не найдено. Пожалуйста, обратитесь к нашим специалистам по
                адресу магазина в вашем городе:
                г. {{ $city->name }}, {{ $city->address }}, либо позвоните по тел: <span
                    class="show-phone">{{ $city->phones }}</span>
            </h4>
            @endif
            @if ($partner && count($products))
                <div class="catalog-header">
                    <div class="headers-foto">Фото</div>
                    <div class="headers-name">Наименование</div>
                    <div class="headers-existence">Наличие</div>
                    <div class="headers-reserve">Доступность</div>
                    <div class="headers-cost">Стоимость</div>
                    <div class="headers-quantity">Заказ</div>
                    <div class="headers-amount">Сумма</div>
                    <div class="headers-shipping">Доставка</div>
                    <div class="headers-total">Итого</div>
                </div>
            @endif
            @foreach($products as $product)
                <div class="{{ ($template === 'row_item_inline' or $template === 'row_item_inline_disk') ? '' : 'col-sm-3 col-xs-6' }}">
                    @include('catalog.'.$template)
                </div>
            @endforeach
        </div>

        {{ $products->appends(\Input::get())->links() }}

        @if(!empty($additional) && $category->type == \models\Categories::TYPE_TIRES)
        <div class="well text-center">
            <h3>Так же вы можете просмотреть шины тех же размеров в других категориях</h3>
        </div>
        @foreach($additional as $k => $col)
        <h3><a href="{{ $col['url'] }}">{{ $col['category']->name }} ({{ count($col['products']) }})</a></h3>
        <div class="row">
            @foreach($col['products'] as $k => $product)
                @if($k > $numPerPage)
                    @break;
                @endif
                <div class="{{ $partner ? '' : 'col-sm-3 col-xs-6' }}">
                    @include('catalog.'.$template)
                </div>
            @endforeach
        </div>

        <a href="{{ $col['url'] }}" class="btn btn-primary">Показать {{ $col['category']->name }}
            ({{ count($col['products']) }})</a>
        @endforeach
        @endif

        <!-- /TIRES -->
    </section>
    @if (!$partner)
    <aside class="col-lg-3 col-md-3 hidden-sm">
        <!-- ASIDE ITEM -->
        <section class="aside-item gradient-gray"
                     style="background: url('/img/calculator.jpg') no-repeat; width: 245px; height: 100px;">
                <a href="/catalog/calculator/" style="text-decoration: none;" title="" data-toggle="tooltip"
                   data-placement="bottom"
                   data-original-title="Здесь вы можете самостоятельно рассчитать возможные варианты шин для вашего автомобиля">
                <div style="margin-left:125px; padding-top: 25px; font-size: 13px;"><span
                                style="border-bottom: 1px dashed">Новый характер вашего авто за пару кликов</span></div>
                </a>
        </section>
        <!-- /ASIDE ITEM -->
        {{ \modules\main\widgets\Banners::widget() }}
    </aside>
    @endif
</div>

@if(!empty($pageDesc))
<div class="description">
    <?php echo $pageDesc ?>
</div>
@endif
<!-- /CONTENT -->
<div class="footer"></div>


<script type="text/javascript">
    $(function () {
        window.onscroll = function() {
            var scrolled = window.pageYOffset || document.documentElement.scrollTop;
            if (scrolled > 433) {
                $('.catalog-header').css('position', 'fixed');
                $('.catalog-header').css('top', $('.header-bg-color').height());
                $('.catalog-header').css('width', $('.container').css('width'));
            } else {
                $('.catalog-header').css('position', 'static');
                $('.catalog-header').css('width', '100%');
            }
                $('.catalog-header').css('height', '40px');
                $('.catalog-header').css('padding-top', '10px');
        }

    });

</script>
@stop