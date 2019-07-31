@if(Sentry::check())
    <a href="/user/office">
        {{ empty(Sentry::getUser()->first_name) ? 'Клиент' : Sentry::getUser()->first_name }}
    </a>
    @if(!$is_firm)
        <img class="warning" src="/img/message.gif" alt="Внимание"/>
    @endif
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <a href="#" class="logout" style="color: #FDB41A">
        Выход
    </a><br/>
    @if(isset($employer) and $employer->name)
        <div class="personal-manager">
            <i class="icon icon-manager"></i><a href="#" tabindex="0" class="popover-dismissible"
                                                data-toggle="popover"
                                                data-trigger="focus"
                                                data-html="true"
                                                data-content="
                    <div class='row manager_cart'>
                    <div class='col-lg-3 col-md-3 col-sm-3 col-xs-3 thumbnail'>
                    @if($employer->image_id)
                                                        <img src='{{ \helpers\Image::url($employer->image->filename, 150, 150) }}'/>
                    @endif
                                                        </div>
                                                        <div class='col-lg-9 col-md-9 col-sm-9 col-xs-9'>
                                                        <b>{{explode ('–',$employer->name )['0']}}</b>
                               <br/>
                               @if($employer->phone)
                                                        Телефон: <b>{{ $employer->phone }}</b>
                               <br/>
                               @endif
                                                @if($employer->email)
                                                        E-mail: {{ $employer->email }}
                                                        <br/>
                                                        @endif
                                                @if($employer->icq)
                                                        ICQ: {{ $employer->icq }}
                                                        <br/>
                                                @endif
                                                        <hr />

                                                        {{ $is_work }}
                                                        </div>
                                                </div>
                                                                      ">Ваш менеджер</a>

        </div>

    @endif
@else
    <a href="#" data-toggle="modal" data-target="#AuthorizationModal">
        Авторизация
    </a>
@endif
