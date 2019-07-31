@extends('layouts.' . ($isAjax ? 'post' : 'admin'))

@section('content')

{{--*/
use helpers\Html;
/*--}}

<div class="row">
    <div class="col-xs-9">
        <div class="box">
            <div class="box-body table-responsive">
                {{ \widgets\SortView::widget([
                'model' => \models\Menus::className()
                ]) }}
            </div>
        </div>
    </div>
    <div class="col-xs-3">
        <div class="btn-group btn-block buffer-bottom">
            {{ Html::a('<i class="glyphicon glyphicon-plus-sign"></i> Добавить пункт меню', '/admin/menus/create',
            ['class' =>
            'btn btn-primary']) }}
            {{ Html::a('<span class="caret"></span><span class="sr-only"></span>', '#', ['class' =>
            'btn btn-primary dropdown-toggle', 'data-toggle' => 'dropdown']) }}
            <ul class="dropdown-menu pull-right" role="menu">
                <li>{{ Html::a('Добавить меню', '/admin/menusTypes/create', [
                    'data-ajax-modal' => '1', 'data-target' => '#menusTypesModal', 'data-ajax-action' => 'content'
                    ]) }}
                </li>
            </ul>
        </div>
        <br/>

        <form>
            {{--*/ $getProductsSearch = \Input::get('MenusSearch'); /*--}}
            <ul class="nav nav-pills nav-stacked nav-categories">
                @foreach ($types as $type)
                <li
                {{ $getProductsSearch['menu_id'] == $type->id ? ' class="active"' : ''}}>{{
                Html::a($type->name,
                '#', ['class' => 'fake-menus-types']) }}<a href="/admin/menusTypes/update/{{ $type->id }}"
                                                           class="category-cog pull-right"
                                                           data-target="#menusTypesModal" data-ajax-action="content"
                                                           data-ajax-modal="1"><i
                        class="glyphicon glyphicon-cog"></i></a>
                <input type="hidden" name="sort[]" value="{{ $type->id }}"/>
                </li>
                @endforeach
            </ul>
        </form>
    </div>
</div>

<!-- MENU MODAL -->
<div class="modal fade" id="menusTypesModal" tabindex="-1" role="dialog" aria-hidden="true"
     aria-labelledby="myModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">Loading..</div>
        </div>
    </div>
</div>
<!-- /MENU MODAL -->
@stop