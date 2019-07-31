@extends('layouts.' . ($isAjax ? 'post' : 'default'))

@section('content')
        <!-- CATALOG -->
{{ \modules\main\widgets\FilterSimple::widget(['categoryAlias' => isset($category) ? $category->alias : '']) }}
        <!-- /CATALOG -->
{{ Breadcrumbs::render('news', (isset($page) ? $page: '')) }}

<div class="content">
    <article>
        {{ $page->body }}
        <br/>
        {{ \widgets\Photos::widget([
        'model' => $page,
        'template' => 'pages'
        ]); }}
    </article>
</div>
<div class="footer"></div>

@stop