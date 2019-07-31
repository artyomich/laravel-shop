@extends('layouts.' . ($isAjax ? 'post' : 'admin'))

@section('content')

    @use('helpers\Html')

    <div class="row">
        <div class="col-xs-12">
            <ul class="nav nav-tabs" role="tablist" id="ordersTabs">
                <li class="active">
                    <a href="#opShop" role="tab" data-toggle="tab">Отзывы о магазине</a>
                </li>
                <li>
                    <a href="#opProducts" role="tab" data-toggle="tab">Отзывы о товаре</a>
                </li>
            </ul>
            <div class="tab-content">
                <!-- TAB SHOP OPINIONS -->
                <div class="tab-pane active" id="opShop">
                    <div class="box">
                        <div class="box-body table-responsive">
                            {{ Html::a('<i class="glyphicon glyphicon-plus-sign"></i> Добавить отзыв', '/admin/opinions/create/',
                            ['class' => 'btn btn-primary']) }}
                            {{ \widgets\GridView::widget(['model' => \models\Opinions::className()]) }}
                        </div>
                    </div>
                </div>
                <!-- /TAB SHOP OPINIONS -->
                <!-- TAB PRODUCTS OPINIONS -->
                <div class="tab-pane" id="opProducts">
                    <div class="box">
                        <div class="box-body table-responsive">
                            {{ \widgets\GridView::widget(['model' => \models\ProductsOpinions::className()]) }}
                        </div>
                    </div>
                </div>
                <!-- /TAB PRODUCTS OPINIONS -->
            </div>
        </div>
    </div>

@stop