@extends('layouts.' . ($isAjax ? 'post' : 'default'))

@section('content')

<div class="row content">
    <section class="col-lg-9 col-md-9 col-sm-12 content-tires">
        <h1 class="font-bold">Калькулятор параметров легковых шин</h1>
        <p>
            Калькулятор шин поможет вам узнать, как изменится клиренс, показания спидометра и линейные размеры новых колес, если вы решили поставить на свой автомобиль шины другого размера.
        </p>
        <form action="/catalog/" method="GET" id="formTiresCalc">
            <table class="table table-tire-calc">
                <thead>
                <tr>
                    <th class="col-xs-2"></th>
                    <th class="col-xs-1"><span>A</span> (ширина), мм</th>
                    <th class="col-xs-1"><span>B</span> (высота), %</th>
                    <th class="col-xs-1"><span>С</span> (диаметр), дюйм</th>
                    <th class="col-xs-1"><span>С</span> (диаметр), мм</th>
                    <th class="col-xs-1"><span>D</span>, мм</th>
                    <th class="col-xs-1">Скорость*, км/ч</th>
                    <th class="col-xs-1">Изменение клиренса, мм</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>Старый размер</td>
                    <td class="select-group">
                        <select class="form-control" id="calcRow1Col1">
                            @foreach(array_keys($filter['Ширина']) as $item)
                                @if(!empty($item) && $item > 100)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                @endif
                            @endforeach
                        </select>
                        /
                    </td>
                    <td class="select-group">
                        <select class="form-control" id="calcRow1Col2">
                            @foreach(array_keys($filter['Высота']) as $item)
                                @if(!empty($item))
                                    <option value="{{ $item / 100 }}">{{ $item }}</option>
                                @endif
                            @endforeach
                            <option value="1">-</option>
                        </select>
                        R
                    </td>
                    <td>
                        <select class="form-control" id="calcRow1Col3">
                            @foreach(array_keys($filter['Диаметр']) as $item)
                                @if(!empty($item) && $item < 100)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                @endif
                            @endforeach
                        </select>
                    </td>
                    <td class="disabled">
                        <input class="form-control" type="text" readonly="readonly" value="" id="calcRow1Col4">
                    </td>
                    <td class="disabled">
                        <input class="form-control" type="text" readonly="readonly" value="" id="calcRow1Col5">
                    </td>
                    <td>
                        <input class="form-control" type="text" value="90" id="calcRow1Col6">
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td>Новый размер</td>
                    <td class="select-group">
                        <select class="form-control" id="calcRow2Col1">
                            @foreach(array_keys($filter['Ширина']) as $item)
                                @if(!empty($item) && $item > 100)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                @endif
                            @endforeach
                        </select>
                        /
                    </td>
                    <td class="select-group">
                        <select class="form-control" id="calcRow2Col2">
                            @foreach(array_keys($filter['Высота']) as $item)
                                @if(!empty($item))
                                    <option value="{{ $item / 100 }}">{{ $item }}</option>
                                @endif
                            @endforeach
                            <option value="1">-</option>
                        </select>
                        R
                    </td>
                    <td>
                        <select class="form-control" id="calcRow2Col3">
                            @foreach(array_keys($filter['Диаметр']) as $item)
                                @if(!empty($item) && $item < 100)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                @endif
                            @endforeach
                        </select>
                    </td>
                    <td class="disabled">
                        <input class="form-control" type="text" readonly="readonly" value="" id="calcRow2Col4">
                    </td>
                    <td class="disabled">
                        <input class="form-control" type="text" readonly="readonly" value="" id="calcRow2Col5">
                    </td>
                    <td>
                        <input class="form-control" type="text" value="" id="calcRow2Col6">
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td>Изменение</td>
                    <td class="disabled">
                        <input class="form-control" type="text" readonly="readonly" value="" id="calcRow3Col1">
                    </td>
                    <td class="disabled">
                        <input class="form-control" type="text" readonly="readonly" value="" id="calcRow3Col2">
                    </td>
                    <td class="disabled">
                        <input class="form-control" type="text" readonly="readonly" value="" id="calcRow3Col3">
                    </td>
                    <td class="disabled">
                        <input class="form-control" type="text" readonly="readonly" value="" id="calcRow3Col4">
                    </td>
                    <td class="disabled">
                        <input class="form-control" type="text" readonly="readonly" value="" id="calcRow3Col5">
                    </td>
                    <td class="disabled">
                        <input class="form-control" type="text" readonly="readonly" value="" id="calcRow3Col6">
                    </td>
                    <td class="disabled">
                        <input class="form-control" type="text" readonly="readonly" value="" id="calcRow3Col7">
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
        <p>
            <span class="text-danger">*</span> Вы можете поменять значение в графе "Показания спидометра" для того, чтобы посмотреть погрешность при разных скоростях.
        </p>
        <form id="formSelectType">
            <div class="row bottom-buffer">
                <div class="col-xs-3 text-right"><br>Подобрать шины</div>
                <div class="col-xs-3">
                    <div class="radio">
                        <label>
                            <input type="radio" name="optionsRadios" id="optionsRadios1" value="new" checked="">
                            нового размера
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="optionsRadios" id="optionsRadios2" value="old">
                            старого размера
                        </label>
                    </div>
                </div>
                <div class="col-xs-6">
                    <input type="submit" value="ПОДБОР" class="btn btn-primary" style="margin-top: 12px">
                </div>
            </div>
        </form>
        <p>
            Данный расчет размеров шин носит теоретический характер.<br>
            Без крайней необходимости не устанавливайте шины, не рекомендованные производителем. Перед установкой шин следует:
        </p>
        <ul>
            <li>удостовериться, что желаемое изменение не вызовет никаких проблем (габариты, механика, кузов...)</li>
            <li>следить, чтобы вновь монтируемые шины соответствовали действующим правилам</li>
            <li>проверить, чтобы диаметр и ширина обода соответствовали параметрам шины</li>
            <li>помните также, что при изменении размеров шины, как правило, изменяется индекс нагрузки</li>
        </ul>
        <p></p>
    </section>
    <aside class="col-lg-3 col-md-3 hidden-sm">
        <!-- ASIDE ITEM -->
        <div class="aside-item text-center">
            <img src="/img/calc.aside.png" alt="">
        </div>
        <!-- /ASIDE ITEM -->
    </aside>
</div>

<div class="footer"></div>

@stop