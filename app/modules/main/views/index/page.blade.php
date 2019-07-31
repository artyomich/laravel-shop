@extends('layouts.' . ($isAjax ? 'post' : 'default'))

@section('content')
    @use('helpers\Html')

        <!-- CATALOG -->
{{ \modules\main\widgets\FilterSimple::widget(['categoryAlias' => '/']) }}
        <!-- /CATALOG -->

{{ Breadcrumbs::render('page', $page) }}

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