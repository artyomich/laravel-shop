<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

    <title>{{ $title }}</title>
    <meta name="keywords" content="{{ $keywords }}">
    <meta name="description" content="{{ $description }}">

    <link rel="icon" href="http://{{ \Config::get('app.host') }}/favicon.ico" type="image/x-icon">
    @section('styles')
    {{ HTML::style(URL::asset('packages/jqueryui/themes/base/jquery-ui.min.css')) }}
    {{ HTML::style(URL::asset('styles/style.css?v=' . $version)) }}
    @show

    @section('scripts')
    {{ HTML::script(URL::asset('packages/jquery/jquery.min.js')) }}
    {{ HTML::script(URL::asset('packages/jqueryui/jquery-ui.min.js')) }}
    {{ HTML::script(URL::asset('js/jquery.cookie.js')) }}
    {{ HTML::script(URL::asset('js/lightbox.min.js')) }}
    {{ HTML::script(URL::asset('js/main.js?v=' . $version)) }}
    {{ HTML::script(URL::asset('packages/bootstrap/dist/js/bootstrap.min.js')) }}
    @show
            <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    @if(! \App::environment('local'))

            <!-- Yandex.Metrika counter -->
    <script type="text/javascript">
        $(window).load(function () {
            (function (d, w, c) {
                (w[c] = w[c] || []).push(function () {
                    try {
                        window.yandexMetrika = w.yaCounter12381976 = new Ya.Metrika({
                            id: 12381976,
                            webvisor: true,
                            clickmap: true,
                            trackLinks: true,
                            accurateTrackBounce: true
                        });
                    } catch (e) {
                    }
                });

                var n = d.getElementsByTagName("script")[0],
                        s = d.createElement("script"),
                        f = function () {
                            n.parentNode.insertBefore(s, n);
                        };
                s.type = "text/javascript";
                s.async = true;
                s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

                if (w.opera == "[object Opera]") {
                    d.addEventListener("DOMContentLoaded", f, false);
                } else {
                    f();
                }
            })(document, window, "yandex_metrika_callbacks");
        });
    </script>
    <noscript>
        <div><img src="//mc.yandex.ru/watch/12381976" style="position:absolute; left:-9999px;" alt=""/></div>
    </noscript>
    <!-- /Yandex.Metrika counter -->


    <script>
        $(window).load(function () {
            (function (i, s, o, g, r, a, m) {
                i['GoogleAnalyticsObject'] = r;
                i[r] = i[r] || function () {
                            (i[r].q = i[r].q || []).push(arguments)
                        }, i[r].l = 1 * new Date();
                a = s.createElement(o),
                        m = s.getElementsByTagName(o)[0];
                a.async = 1;
                a.src = g;
                m.parentNode.insertBefore(a, m)
            })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

            ga('create', 'UA-28980532-1', 'auto');
            ga('send', 'pageview');
        });
    </script>
    @endif

    <script>
        window.ONLINE_CONSULT_GROUP_ALIAS = "{{ \models\Cities::getCurrentCity()->id }}";
        window.ONLINE_CONSULT_HOST_NAME = "{{ \Config::get('jabber.host') }}";
    </script>
</head>
<body>
<header>
    <div class="h-bg gradient-gray header-bg-color"></div>

    <div class="container h-bg-body">
        <div class="row">
            <div class="col-md-6 col-md-offset-0 col-sm-11 col-sm-offset-1 col-xs-11 header-logo-menu">
                <a href="/" class="brand">Первая Объединённая <br> Шинная Компания</a>

                <div class="header-nav header-menu">
                    {{ \widgets\Menu::widget(['type' => 'main']) }}
                </div>
            </div>
            <div class="col-md-3 col-md-offset-0 col-sm-5 col-sm-offset-1 col-xs-5 header-adress-auth">
                <div class="address-city{{\Sentry::getUser() && \Sentry::getUser()->type === 'firm' ? ' no-active' : ''}}">
                    <i class="icon icon-mark"></i> Ваш город
                    {{ \widgets\modals\ChooseCityModal::a([
                        'data-html' => 'true',
                        'data-placement' => 'bottom',
                        'data-content' => 'Вы находитесь в г. ' . $city->name . ' ?
                            <br/>
                            <p>
                            <input type="button" class="btn btn-primary btn-xs popover-hide" value="Да">
                            <input data-toggle="modal" data-target="#ChooseCityModal"  type="button" class="btn btn-primary btn-xs popover-hide" value="Нет"></p>']) }}


                    <i class="{{\Sentry::getUser() && \Sentry::getUser()->type === 'firm' ? '' : 'caret'}}"></i>
                    </a>
                </div>
                <div class="address-street">

                    <i class="icon icon-home"></i> <a class="adress-map"
                                                      href="/{{ $city->alias }}/find_us/">{{ $city->address }}</a></span>

                </div>
            </div>
            <div class="col-md-3 col-md-offset-0 col-sm-5 col-sm-offset-1 col-xs-5 col-xs-offset-1 header-phone-cart">
                <div class="contacts-phone">
                    <i class="icon icon-phone"></i> <span>{{ $city->phones }}</span><br/>
                </div>
                <div class="call-me">
                    {{ \widgets\modals\ReqCallModal::a() }}&nbsp;&nbsp;&nbsp;
                    {{ \widgets\modals\SupportModal::a() }}
                </div>
                <div>
                    {{ \modules\main\widgets\Cart::widget() }}
                </div>
            </div>
        </div>
    </div>

    <!-- TOP NAVBAR -->
    <div class="container">
        @if (\Session::has('success'))
            <br/><br/>
            <div class="alert alert-success">{{ \Session::get('success') }}</div>
        @endif
    </div>
    <!-- /TOP NAVBAR -->
</header>

<div class="header-content-margin-lg visible-md visible-lg"></div>

<div class="container site-search">
    <div class="row">
        <div class="col-xs-12">
            <form action="/catalog/" method="get">
                <div class="input-group">
                    <input type="text" class="form-control" name="Filter[name]" placeholder="Поиск по сайту"
                           value="{{ Input::get('Filter.name')  }}"/>
                    <span class="input-group-btn">
                        <input class="btn btn-primary" type="submit" value="Искать"/>
                    </span>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="container">
    @yield('content')
</div>
<!-- FOOTER -->
<footer>
    <div class="container">
        <div class="row dev-comment-link">
            <div class="col-xs-12">
                <span class="glyphicon glyphicon-exclamation-sign"></span>
                {{ \widgets\modals\ImprovePageModal::a() }}
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-4">
                <div class="title">Контакты</div>
                <div class="col-xs-6">{{ $city->phones }}</div>
                <div class="col-xs-6">
                    @if(\Config::get('settings.authorization.enabled'))
                        <div class="address-authorization">
                            <i class="glyphicon glyphicon-user" style="color: #FCC524"></i>
                            {{ \modules\main\widgets\HeaderAuth::widget() }}
                        </div>
                    @endif
                </div>
                <div class="col-xs-6"><a href="#" data-toggle="modal"
                   data-target="#SupportModal">Обратная связь</a></div>
                <div class="contacts-8800">
                    {{ \widgets\modals\CallToLeadershipModal::a(['class' => 'btn btn-primary-2d btn-xs hidden-xs']) }}
                </div>

                <div class="row hidden-xs">
                    <div class="col-xs-3"><span class="thumbnail" style="margin-bottom:0"><img
                                    src="/img/onlinepay/1.gif"/></span></div>
                    <div class="col-xs-3"><span class="thumbnail" style="margin-bottom:0"><img
                                    src="/img/onlinepay/2.jpg"/></span></div>
                    <div class="col-xs-3"><span class="thumbnail" style="margin-bottom:0"><img
                                    src="/img/onlinepay/3.gif"/></span></div>
                    <div class="col-xs-3"><span class="thumbnail" style="margin-bottom:0"><img
                                    src="/img/onlinepay/4.jpg"/></span></div>
                </div>
            </div>
            <div class="hidden-xs col-sm-4">
                <div class="title">Навигация</div>
                {{ \widgets\Menu::widget(['type' => 'footer1', 'template' => 'simple']) }}
            </div>
            <div class="col-xs-8 col-sm-4">
                <div class="title">Быстрый доступ</div>
                {{ \widgets\Menu::widget(['type' => 'footer2', 'template' => 'simple']) }}
            </div>
            <div style="clear:both;">Вся представленная на сайте информация, касающаяся технических характеристик, наличия на складе, стоимости товаров, носит информационный характер и ни при каких условиях не является публичной офертой, определяемой положениями Статьи 437(2) Гражданского кодекса РФ</div>
            <div>© 2014-<?= date('Y') ?> "POSHK", Первая объединенная шинная компания. {{ $city->address }}</div>
        </div>
    </div>
</footer>
<!-- /FOOTER -->

<!-- ADD TO CART MODAL -->
<div class="modal fade" id="addToCartModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                            class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">Корзина</h4>
            </div>
            <div class="modal-body">
                Товар был добавлен в вашу корзину!
            </div>
            <div class="modal-footer">
                <a href="/cart/" class="btn btn-cart-oformit pull-left">Оформить заказ</a>
                <button type="button" class="btn btn-link pull-left" data-dismiss="modal">Продолжить покупки</button>
            </div>
        </div>
    </div>
</div>
<!-- /ADD TO CART MODAL -->

{{ \modules\main\widgets\Modal::widget() }}

@if(isset($isAvailableOC) && $isAvailableOC)
    {{ HTML::script('http://' . \Config::get('jabber.host') . ':8080/socket.io/socket.io.js', [], false) }}
    {{ HTML::script('http://' . \Config::get('jabber.host') . ':8080/js/core.js', [], false) }}
@endif

</body>
</html>