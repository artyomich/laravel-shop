@extends('layouts.' . ($isAjax ? 'post' : 'default'))

@section('content')

@use('helpers\Html')


<!-- CATALOG -->
{{ \modules\main\widgets\FilterSimple::widget(['categoryAlias' => isset($category) ? $category->alias : '']) }}
<!-- /CATALOG -->

<!-- CONTENT -->
<div class="row content">
    <section class="col-lg-9 col-md-9 col-sm-12 content-tires">
        <!-- TIRES -->
        <div class="row">
            @foreach($products as $product)
            <!-- TIRE ITEM -->
            @include('catalog.row_item')
            <!-- /TIRE ITEM -->
            @endforeach
        </div>
        <!-- /TIRES -->
        <!-- TEXT -->
        <article class="sub-text">
            {{ isset($page) ? $page->body : '' }}
        </article>
        <!-- /TEXT -->
        <br /><br />
    </section>
    <aside class="col-lg-3 col-md-3 hidden-sm hidden-xs">
        <!-- ASIDE ITEM -->
        <section class="aside-item gradient-gray" style="background: url('/img/calculator.jpg') no-repeat; width: 245px; height: 100px;">
            <a href="/catalog/calculator/" title="" data-toggle="tooltip" style="text-decoration: none;" data-placement="bottom" data-original-title="Здесь вы можете самостоятельно рассчитать возможные варианты шин для вашего автомобиля">
                <div style="margin-left:125px; padding-top: 25px; font-size: 13px;"><span style="border-bottom: 1px dashed">Новый характер вашего авто за пару кликов</span></div>
            </a>
        </section>
        <!-- /ASIDE ITEM -->
        {{ \modules\main\widgets\Banners::widget(['group' => 'Главная']) }}
    </aside>
</div>
<!-- /CONTENT -->
<div class="footer"></div>

@stop