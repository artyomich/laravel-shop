@extends('layouts.' . ($isAjax ? 'post' : 'admin'))

@section('content')

@use('helpers\Html')

<div class="row">
    <div class="col-xs-9">
        <div class="box">
            <div class="box-body table-responsive">
                {{ \widgets\GridView::widget([
                'model' => \models\Products::className()
                ]) }}
            </div>
        </div>
    </div>
    <div class="col-xs-3">
        <form>
            {{--*/ $getProductsSearch = \Input::get('ProductsSearch'); /*--}}
            <ul class="nav nav-pills nav-stacked nav-categories category-sortable">
                <li class="{{ !isset($getProductsSearch['category_id']) ? 'active ' : ''
                    }}sortable-disabled">{{ Html::a('Все товары', '/admin/products', ['class' => 'product-switch-category']) }}
                </li>
                @foreach ($categories as $category)
                <li
                {{ $getProductsSearch['category_id'] == $category->id ? ' class="active"' : ''}}>{{
                Html::a($category->name,
                '/admin/products?ProductsSearch[category_id]=' . $category->id, [
                'class' => 'product-switch-category' . ($category->is_visible ? '' : ' inactive')
                ]) }}<a href="/admin/categories/update/{{ $category->id }}" class="category-cog pull-right"
                        data-target="#categoryModal" data-ajax-action="content"
                        data-ajax-modal="1"><i class="glyphicon glyphicon-cog"></i></a>
                <input type="hidden" name="sort[]" value="{{ $category->id }}"/>
                </li>
                @endforeach
            </ul>
            <br/>
            @if (!\Input::get('showDoubles'))
                {{ Html::a('Показать дубли', '/admin/products/?showDoubles=1',
                   ['class' =>
                   'btn btn-default btn-block']) }}
            @else
                {{ Html::a('Показать каталог', '/admin/products/',
                   ['class' =>
                   'btn btn-primary btn-block']) }}
            @endif
        </form>
    </div>
</div>

<!-- CATEGORY MODAL -->
<div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-hidden="true"
     aria-labelledby="myModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">Loading..</div>
        </div>
    </div>
</div>
<!-- /CATEGORY MODAL -->
@stop