@extends('layouts.' . ($isAjax ? 'post' : 'admin'))

@section('content')

{{--*/
use helpers\Html;
/*--}}

<div class="row">
    <div class="col-xs-9">
        <div class="box">
            <div class="box-body table-responsive">
                {{ \widgets\GridView::widget([
                'model' => \models\Pages::className()
                ]) }}
            </div>
        </div>
    </div>
    <div class="col-xs-3">
        <div class="btn-group btn-block buffer-bottom">
            {{ Html::a('<i class="glyphicon glyphicon-plus-sign"></i> Добавить страницу', '/admin/pages/create',
            ['class' => 'btn btn-primary']) }}
            {{ Html::a('<span class="caret"></span><span class="sr-only"></span>', '#', ['class' =>
            'btn btn-primary dropdown-toggle', 'data-toggle' => 'dropdown']) }}
            <ul class="dropdown-menu pull-right" role="menu">
                <li>{{ Html::a('Добавить категорию', '/admin/categories/create?model=\models\PagesCategories', [
                    'data-ajax-modal' => '1', 'data-target' => '#categoryModal', 'data-ajax-action' => 'content'
                    ]) }}
                </li>
            </ul>
        </div>
        <br/>

        <form>
            {{--*/ $getPagesSearch = \Input::get('PagesSearch'); /*--}}
            <ul class="nav nav-pills nav-stacked nav-categories category-sortable" data-model="\models\PagesCategories">
                <li class="{{ !isset($getPagesSearch['category_id']) ? 'active ' : ''
                    }}sortable-disabled">{{ Html::a('Все товары', '/admin/pages', ['class' =>
                    'product-switch-category']) }}
                </li>
                @foreach ($categories as $category)
                <li
                {{ $getPagesSearch['category_id'] == $category->id ? ' class="active"' : ''}}>{{
                Html::a($category->name,
                '/admin/pages?PagesSearch[category_id]=' . $category->id, [
                'class' => 'pages-switch-category' . ($category->is_visible ? '' : ' inactive')
                ]) }}<a href="/admin/categories/update/{{ $category->id }}" class="category-cog pull-right"
                        data-target="#categoryModal" data-ajax-action="content"
                        data-ajax-modal="1"><i class="glyphicon glyphicon-cog"></i></a>
                <input type="hidden" name="sort[]" value="{{ $category->id }}"/>
                </li>
                @endforeach
            </ul>
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