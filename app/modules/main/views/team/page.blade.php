@extends('layouts.' . ($isAjax ? 'post' : 'default'))

@section('content')
    @use('helpers\Html')

    {{ Breadcrumbs::render('team', '') }}

    <div class="row content">
    <article>
        <div class="col-lg-9 col-md-9 col-sm-12 content-tires">
            @foreach($employers as $employer)
            <!-- HUMAN -->
            <div class="row bottom-buffer">
                @if(isset($employer->image))
                <div class="col-xs-2 thumbnail">
                    {{ \helpers\Image::img($employer->image->filename, 150, 150) }}
                </div>
                @endif
                <div class="col-xs-10">
                    <strong>{{$employer->name}}</strong>
                    <p>
                        @if($employer->phone)
                            Тел.: {{$employer->phone}}<br/>
                        @endif
                        @if($employer->email)
                            Email: {{$employer->email}}<br/>
                        @endif
                        @if($employer->icq)
                            ICQ: {{$employer->icq}}
                        @endif
                    </p>
                </div>
            </div>
            <!-- /HUMAN -->
            @endforeach
        </div>
    </article>
</div>
<div class="footer"></div>

@stop