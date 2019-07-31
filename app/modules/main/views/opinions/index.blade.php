@extends('layouts.' . ($isAjax ? 'post' : 'default'))

@section('content')
        <!-- CATALOG -->
{{ \modules\main\widgets\FilterSimple::widget(['categoryAlias' => isset($category) ? $category->alias : '']) }}
        <!-- /CATALOG -->
<br/>
    <h1>Наши отзывы</h1>

    <div class="row">
        <div class="col-xs-12 bottom-buffer">
            <div class="embed-responsive embed-responsive-16by9">
                <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/97iVM_HyBZo" frameborder="0"
                        allowfullscreen></iframe>
            </div>
        </div>

        <div class="col-xs-6 bottom-buffer">
            <div class="embed-responsive embed-responsive-16by9">
                <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/TRjhHZnBwok" frameborder="0"
                        allowfullscreen></iframe>
            </div>
        </div>
        <div class="col-xs-6 bottom-buffer">
            <div class="embed-responsive embed-responsive-16by9">
                <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/2gD28gk6Fd4" frameborder="0"
                        allowfullscreen></iframe>
            </div>
        </div>
        <div class="col-xs-6 bottom-buffer">
            <div class="embed-responsive embed-responsive-16by9">
                <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/3lHHbjXD60o" frameborder="0"
                        allowfullscreen></iframe>
            </div>
        </div>
        <div class="col-xs-6 bottom-buffer">
            <div class="embed-responsive embed-responsive-16by9">
                <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/jG4W0Pt1HjU" frameborder="0"
                        allowfullscreen></iframe>
            </div>
        </div>

        <div class="col-xs-6 bottom-buffer">
            <div class="embed-responsive embed-responsive-16by9">
                <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/9OWbMmWyvis" frameborder="0"
                        allowfullscreen></iframe>
            </div>
        </div>

        <div class="col-xs-6 bottom-buffer">
            <div class="embed-responsive embed-responsive-16by9">
                <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/gae4BFvGSe0" frameborder="0"
                        allowfullscreen></iframe>
            </div>
        </div>
        <div class="col-xs-6 bottom-buffer">
            <div class="embed-responsive embed-responsive-16by9">
                <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/AX3yEmiqxnk" frameborder="0"
                        allowfullscreen></iframe>
            </div>
        </div>
        <div class="col-xs-6 bottom-buffer">
            <div class="embed-responsive embed-responsive-16by9">
                <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/I6dU3hDBgGw" frameborder="0"
                        allowfullscreen></iframe>
            </div>
        </div>
        <div class="col-xs-6 bottom-buffer">
            <div class="embed-responsive embed-responsive-16by9">
                <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/5lh3MFhNV-8" frameborder="0"
                        allowfullscreen></iframe>
            </div>
        </div>
        <div class="col-xs-6 bottom-buffer">
            <div class="embed-responsive embed-responsive-16by9">
                <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/houvq27ES3w" frameborder="0"
                        allowfullscreen></iframe>
            </div>
        </div>
        <div class="col-xs-6 bottom-buffer">
            <div class="embed-responsive embed-responsive-16by9">
                <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/Nk_N7Saa6UY" frameborder="0"
                        allowfullscreen></iframe>
            </div>
        </div>
        <div class="col-xs-6 bottom-buffer">
            <div class="embed-responsive embed-responsive-16by9">
                <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/iVv8iTwA3Bo" frameborder="0"
                        allowfullscreen></iframe>
            </div>
        </div>
        <div class="col-xs-6 bottom-buffer">
            <div class="embed-responsive embed-responsive-16by9">
                <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/QEOdbXcmb3A" frameborder="0"
                        allowfullscreen></iframe>
            </div>
        </div>

    </div>

    <div class="row content">
        <article>
            {{ \widgets\Photos::widget([
            'model' => \models\Opinions::className(),
            'template' => 'opinions'
            ]); }}
        </article>
    </div>
    <div class="footer"></div>

@stop