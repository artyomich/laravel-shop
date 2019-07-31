@extends('layouts.' . ($isAjax ? 'post' : 'admin'))

@section('content')

@use('helpers\Html')

<form>
<div class="row">
    <div class="col-xs-9">
        <div class="box">
            <div class="box-body table-responsive">
                {{ \widgets\SortView::widget(['model' => \models\Banners::className(), 'items' => $banners]) }}
            </div>
        </div>
    </div>
    <div class="col-xs-3">{{--
        {{ Html::a('<i class="glyphicon glyphicon-plus-sign"></i> Добавить банер', '/admin/banners/create/',
        ['class' => 'btn btn-primary btn-block buffer-bottom']) }}--}}
        <div class="btn-group btn-block buffer-bottom">
            {{ Html::a('<i class="glyphicon glyphicon-plus-sign"></i> Добавить банер', '/admin/banners/create',
                ['class' => 'btn btn-primary']) }}
            {{ Html::a('<span class="caret"></span><span class="sr-only"></span>', '#',
                ['class' => 'btn btn-primary dropdown-toggle', 'data-toggle' => 'dropdown']) }}
            <ul class="dropdown-menu pull-right" role="menu">
                <li>{{ Html::a('Добавить группу', '/admin/banners/groupcreate', [
                    'data-ajax-modal' => '1', 'data-target' => '#bannersGroupsModal', 'data-ajax-action' => 'content'
                    ]) }}
                </li>
            </ul>
        </div>
        <br/>

        <form>
            <ul class="nav nav-pills nav-stacked nav-categories">
                @foreach (\models\BannersGroups::all() as $group)
                <li>
                    {{ Html::a($group->name, '#', ['class' => 'fake-menus-types']) }}
                    <a href="/admin/banners/groupupdate/{{ $group->id }}"
                       class="category-cog pull-right"
                       data-target="#bannersGroupsModal" data-ajax-action="content"
                       data-ajax-modal="1"><i class="glyphicon glyphicon-cog"></i></a>
                    <input type="hidden" name="sort[]" value="{{ $group->id }}"/>
                </li>
                @endforeach
            </ul>
        </form>
    </div>
</div>
</form>

<!-- GROUPS MODAL -->
<div class="modal fade" id="bannersGroupsModal" tabindex="-1" role="dialog" aria-hidden="true"
     aria-labelledby="myModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">Loading..</div>
        </div>
    </div>
</div>
<!-- /GROUPS MODAL -->
@stop