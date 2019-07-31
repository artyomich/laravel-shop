@extends('layouts.' . ($isAjax ? 'post' : 'default'))

@section('content')

    @use('helpers\Html')

    <div>
        @if(!isset(\Sentry::getUser()->is_firm))
            <div class="undecided">
                <p><strong>Уважаемый клиент, Вы проходите регистрацию в нашей системе - вам необходимо заполнить
                        поля:</strong><br/>Пожалуйста заполните нужную вкладку, по которой вы в дальнейшем сможете
                    работать.<br/><br/>
                </p>

                <div>
                    <table border="0" style="width: 100%;">
                        <tbody>
                        <tr>
                            <td style="background: url('/img/line.png') center;"><img
                                        src="/img/scissors.png" width="26" height="23"/></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <p><span style="color: #ff6600;"><strong><span style="text-decoration: underline;">Обращаем ваше внимание: </span></strong><span
                                style="text-decoration: underline;">редактирование типа клиента в дальнейшем возможно только с согласования менеджера.</span></span>
                </p>

                <p><span style="color: #ff6600;"><span style="text-decoration: underline;"></span><strong><span
                                    style="text-decoration: underline;">Самостоятельно исправить тип клиента вы уже не сможете.</span></strong></span><span><o:p></o:p></span>
                </p>
            </div>
        @endif

        <div class="bottom-buffer-sm">
                @if(!is_null($isFirm))
                    Вы зарегистрированы как
                @if ($isFirm === false)
                        физическое лицо
                @elseif($isVendor === false)
                        юридическое лицо
                @elseif($isVendor === true)
                        поставщик
                @endif
            @endif
        </div>

        <div class="bottom-buffer">
                @if(isset($employers) and $employers->name)
                <button tabindex="0" class="popover-dismissible btn btn-default-outline btn-xs"
                        data-toggle="popover"
                        data-trigger="focus"
                        data-html="true"
                        data-content="
            <div class='row manager_cart'>
            <div class='col-lg-3 col-md-3 col-sm-3 col-xs-3 thumbnail'>
                @if($employers->image_id)
                                <img src='{{ \helpers\Image::url($employers->image->filename, 150, 150) }}'/>
                @endif
                                    </div>
                                    <div class='col-lg-9 col-md-9 col-sm-9 col-xs-9'>
                                    <b>{{explode ('–',$employers->name )['0']}}</b>
                       <br/>
                       @if($employers->phone)
                                Телефон: <b>{{ $employers->phone }}</b>
                       <br/>
                       @endif
                        @if($employers->email)
                                E-mail: {{ $employers->email }}
                                <br/>
                                @endif
                        @if($employers->icq)
                                ICQ: {{ $employers->icq }}
                                <br/>
                        @endif
                                <hr />
                                {{ $isWork }}
                                    </div>
                            </div>">
                    Ваш персональный менеджер
                </button>
            @endif
            @if($isFirm and $isVendor)
                <a class="btn btn-primary-outline btn-xs" href="/user/vendor/">Добавить ваши товары</a>
            @endif
            @if(!is_null($model->type))
                <button class="btn btn-default-outline btn-xs" data-toggle="collapse" data-target=".box-body-myself"
                        aria-expanded="false" aria-controls="box-body-myself">Редактировать личные данные
                </button>
            @endif
        </div>
        <div class="box collapsed-box">
            <div class="nav-tabs-custom">
                {{ !is_null($isFirm) ? '' :'<ul class="nav nav-tabs">' }}
                @if(!$isFirm)
                    <li class='active'
                        style="{{!is_null($isFirm)?'list-style-type: none':''}}"><a href="#tab_1" data-toggle="tab">
                            @if(is_null($isFirm))
                                Для физических лиц
                            @endif
                        </a>
                    </li>
                @endif
                @if(is_null($isFirm) or ($isFirm and !$isVendor))
                    <li style="{{!is_null($isFirm)?'list-style-type: none':''}}">
                        <a href="#tab_2" data-toggle="tab">
                            @if(is_null($isFirm))
                                Для юридических лиц
                            @endif

                        </a>
                    </li>
                @endif
                @if(is_null($isFirm) or $isVendor)
                    <li style="{{!is_null($isFirm)?'list-style-type: none':''}}">
                        <a href="#tab_3" data-toggle="tab">
                            @if(is_null($isFirm))
                                Для юридических лиц (поставщиков)
                            @endif

                        </a>
                    </li>
                @endif
                {{ !is_null($isFirm) ? '' :'</ul>' }}
                <?php
                $physical = ['last_name', 'first_name', 'email', 'address'];
                $firm = ['firm', 'inn', 'ogrn', 'kpp', 'rs', 'ks', 'bik', 'bank', 'last_name', 'first_name', 'email', 'address', 'actual_address'];
                ?>
                <div class="tab-content">
                    @if(!$isFirm)
                        <div class="box-body-myself tab-pane active"
                             style="display:{{ !is_null($isFirm) ? '' : '' }}"
                             id="tab_1">
                            <?php
                            $form = \widgets\ActiveForm::begin([
                                    'action' => '/user/office/',
                                    'options' => ['data-ajax' => '1']
                            ]);
                            ?>
                            {{ $form->field($model, 'type')->hiddenInput(['value'=> 'physical'])->label(false) }}
                            @foreach($physical as $value)
                                <div class="col-sm-6">
                                    {{$form->field($model, $value)->input('text')}}
                                </div>
                            @endforeach
                            <div class="col-sm-6">
                                {{ $form->field($model, 'city_name')->input('text',['id' => 'users-city_name_physical']) }}
                                {{ $form->field($model, 'cdek_id')->hiddenInput(['id' => 'deliveryCityID_physical'])->label(false)}}
                            </div>
                            <div class="col-sm-6">
                                {{ $form->field($model, 'phone')->input('text', ['placeholder' => 'Телефон в формате*: +79123456789:']) }}
                            </div>
                            <div class="col-sm-6">
                                {{$form->field($model, 'password')->passwordInput(['value' => ''])}}
                            </div>
                            <div class="col-xs-12">
                                {{Form::submit('Сохранить' , array('class' => 'btn btn-success top-buffer bottom-buffer'))}}
                            </div>
                            {{ $form->end() }}
                        </div>
                    @endif
                    @if(is_null($isFirm) or ($isFirm and !$isVendor))
                        <div class="box-body-myself tab-pane"
                             style="display:{{ !is_null($isFirm) ? '' : '' }}"
                             id="tab_2">
                            <?php
                            $form = \widgets\ActiveForm::begin([
                                    'action' => '/user/office/',
                                    'options' => ['data-ajax' => '1']
                            ]);
                            ?>
                            {{ $form->field($model, 'type')->hiddenInput([ 'value'=> 'firm'])->label(false) }}
                            @foreach($firm as $value)
                                    <div class="col-sm-6">
                                        {{$form->field($model, $value)->input('text')}}
                                    </div>
                            @endforeach
                            <div class="col-sm-6">
                                {{ $form->field($model, 'city_name')->input('text',['id' => 'users-city_name_firm']) }}
                                {{ $form->field($model, 'cdek_id')->hiddenInput(['id' => 'deliveryCityID_firm'])->label(false)}}
                            </div>
                            <div class="col-sm-6">
                                {{ $form->field($model, 'phone')->input('text', ['placeholder' => 'Телефон в формате*: +79123456789:']) }}
                            </div>
                            <div class="col-sm-6">
                                {{$form->field($model, 'password')->passwordInput(['value' => ''])}}
                            </div>
                            <div class="col-xs-12">
                                {{Form::submit('Сохранить' , array('class' => 'btn btn-success top-buffer bottom-buffer'))}}
                            </div>
                            {{ $form->end() }}
                        </div>
                    @endif
                    @if(is_null($isFirm) or $isVendor)
                        <div class="box-body-myself tab-pane"
                             style="display:{{ !is_null($isFirm) ? '' : '' }}"
                             id="tab_3">
                            <?php
                            $form = \widgets\ActiveForm::begin([
                                    'action' => '/user/office/',
                                    'options' => ['data-ajax' => '1']
                            ]);
                            ?>
                            {{ $form->field($model, 'type')->hiddenInput([ 'value'=> 'vendor'])->label(false) }}
                            @foreach($firm as $value)
                                    <div class="col-sm-6">
                                        {{$form->field($model, $value)->input('text')}}
                                    </div>
                            @endforeach
                            <div class="col-sm-6">
                                {{ $form->field($model, 'city_name')->input('text',['id' => 'users-city_name_vendor']) }}
                                {{ $form->field($model, 'cdek_id')->hiddenInput(['id' => 'deliveryCityID_vendor'])->label(false)}}
                                </div>
                            <div class="col-sm-6">
                                {{ $form->field($model, 'phone')->input('text', ['placeholder' => 'Телефон в формате*: +79123456789:']) }}
                                </div>
                            <div class="col-sm-6">
                                {{$form->field($model, 'password')->passwordInput(['value' => ''])}}
                            </div>
                            <div class="col-xs-12">
                                {{Form::submit('Сохранить' , array('class' => 'btn btn-success top-buffer bottom-buffer'))}}
                            </div>
                            {{ $form->end() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="box collapsed-box orders">
            <div class="box-body">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class='{{!is_null($isFirm)?'active':'hide'}}'>
                            <a href="#tab_4" data-toggle="tab">
                                Список заказов
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane {{!is_null($isFirm) ?'active':'hide'}}"
                             id="tab_4">
                            {{ \modules\main\widgets\OrderList::widget(['orders'=>$orders]) }}
                            @if($ordersCount>5)
                                <a href='/user/orderlist' class='btn btn-success top-buffer'>
                                    Посмотреть все заказы ({{$ordersCount}})
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>


        @if(!is_null($isFirm))
            <p class="top-buffer"><b>Здравствуйте, уважаемый {{$model->first_name}}.</b></p><p><br/> Спасибо за
                регистрацию в системе покупок
                Первой
                объединенной шинной компании.<br/> Мы рады сообщить вам, что очень скоро мы сделаем из нашего сайта
                настоящий многофункциональный портал, на котором вы сможете:</p>
            <ul>
                <li>Видеть персональные скидки;</li>
                <li>Получать рекомендации и выбирать товары по срокам доставки;</li>
                <li>Видеть аналитику цен на все позиции;</li>
                <li>Читать и писать отзывы о товарах, а также смотреть рейтинги и обзоры шин;</li>
                <li>Покупать не только шины, но и много других товаров автомобильной тематики (Литые диски, аккумуляторы
                    и
                    т.д.);
                </li>
                <li>Общаться в персональным менеджером, закрепленным за вами, в режиме реального времени;</li>
                <li>Получите доступ к более чем 100 000 товарам ежедневно;</li>
                <li>Просматривать ваши персональные заказы, резервировать товар и отслеживать весь путь заказа до ваших
                    дверей;
                </li>
                <li>Получать дополнительные баллы, которые вы сможете тратить в наших магазинах или шиномонтаже;</li>
            </ul><p><b><b><br/></b></b></p>
            <div>
                <table border="0" style="width: 100%;">
                    <tbody>
                    <tr>
                        <td style="background: url('/img/line.png') center;"><img
                                    src="/img/scissors.png" width="26" height="23"/></td>
                    </tr>
                    </tbody>
                </table>
            </div><p>Мы провели огромную работу по анализу других сайтов и можем с уверенностью сказать, такой площадки
                как
                у нас - Не будет ни у кого.<br/> На данный момент наши программисты работают большой командой и каждый
                день
                трудятся, для того чтобы вы смогли пользоваться всеми необходимыми функциями, которые превратят покупку
                у
                нас в один простой клик мышкой.</p><p>О доступности и запуске каждого пункта мы будем оповещать вас
                посредством электронной почты. А также, по мере запуска, опираясь на ваши комментарии по удобству
                использования системы, будем делать ее еще проще и функциональнее.<br/> Давайте дружить!</p>
            <table border="0" style="width: 100%;">
                <tbody>
                <tr>
                    <td style="background: url('/img/line.png') center;"><img
                                src="/img/scissors.png"
                                width="26" height="23"/></td>
                </tr>
                </tbody>
            </table><p><span style="color: #ff9900;"><b><br/></b></span></p><p><span
                        style="color: #ff9900;"><b><br/></b></span></p><p><span style="color: #ff9900;"><b>P.S. Мы будем
                        искренне рады, если вы расскажете о нашей задумке друзьям и коллегам :)</b></span></p>
            <p><span
                        style="color: #ff9900;"><b>Чем больше пользователей оценят удобство сайта, тем еще лучше мы
                        поймем
                        удобство пользования сайтом {{ \Config::get('app.host') }}</b></span></p>
            <script type="text/javascript" src="//yastatic.net/share/share.js" charset="utf-8"></script>
            <div class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="link"
                 data-yashareQuickServices="vkontakte,facebook,odnoklassniki,moimir,gplus"></div>
        @endif
        {{ Form::close() }}
        <div class="footer"></div>
    </div>
    <script type="text/javascript">
        $(function () {
            bindAjaxSelectCity('#users-city_name_physical', '#deliveryCityID_physical');
            bindAjaxSelectCity('#users-city_name_firm', '#deliveryCityID_firm');
            bindAjaxSelectCity('#users-city_name_vendor', '#deliveryCityID_vendor');
        });
    </script>
@stop