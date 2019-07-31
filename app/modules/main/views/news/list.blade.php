@extends('layouts.' . ($isAjax ? 'post' : 'default'))

@section('content')

        <!-- CATALOG -->
{{ \modules\main\widgets\FilterSimple::widget(['categoryAlias' => isset($category) ? $category->alias : '']) }}
        <!-- /CATALOG -->

{{ Breadcrumbs::render('news', '') }}

@foreach ($pages as $page)


    <div class="row blog-item">
    <div class="col-xs-2">
        @if ($page->image_id)
        <a href="{{ $page->alias }}" class="thumbnail">
            {{ \helpers\Image::img($page->image->filename, 145, 145, ['crop' => true]) }}
        </a>
        @endif
    </div>
    <div class="col-xs-6">
        <small class="text-muted">{{ date("d.m.Y", strtotime($page->date_create)) }}</small>
        <h2><a href="{{ $page->alias }}">{{{ $page->name }}}</a></h2>

        <p>{{ $page->description }}</p>
    </div>
</div>

@endforeach
<div class="footer"></div>

@stop