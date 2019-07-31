@extends('layouts.' . ($isAjax ? 'post' : 'admin'))

@section('content')

    @use('helpers\Html')

    <div class="row">
        <div class="col-xs-9">
            <div class="box">
                <div class="box-body table-responsive">
                    {{ \widgets\GridView::widget(['model' => \models\Users::className()]) }}
                </div>
            </div>
        </div>
        <div class="col-xs-3">
            {{ Html::a('<i class="glyphicon glyphicon-plus-sign"></i> Добавить пользователя', '/admin/users/create',
                ['class' => 'btn btn-primary btn-block', 'data-target' => '#userModal', 'data-ajax-action' => 'content',
                'data-ajax-modal' => '1']) }}
            <br/>

            <form>
                <ul class="nav nav-pills nav-stacked nav-categories">
                    @foreach ($groups as $group)
                        <li class="{{ @\Input::get('UsersSearch')['group_id'] ==  $group->id ? 'active' : '' }}">{{
                    Html::a($group->name, \Request::url() . '?UsersSearch[group_id]='. $group->id , ['class' => '']) }}
                            <a href="/admin/users/groupupdate/{{ $group->id }}" class="category-cog pull-right"
                               data-target="#groupModal" data-ajax-action="content"
                               data-ajax-modal="1"><i class="glyphicon glyphicon-cog"></i>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </form>
        </div>
    </div>

    <!-- USER MODAL -->
    <div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">Loading..</div>
            </div>
        </div>
    </div>
    <!-- /USER MODAL -->

    <!-- GROUP MODAL -->
    <div class="modal fade" id="groupModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">Loading..</div>
            </div>
        </div>
    </div>
    <!-- /GROUP MODAL -->
@stop