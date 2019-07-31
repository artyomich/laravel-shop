<!-- FILTER -->
<form method="get" action="/catalog/{{ $categoryAlias }}/">
    <div class="row filter-tire">
        <!-- FILTER COLUMN -->
        <div class="col-sm-2 col-xs-6">
            <div class="filter-col">
                <div class="filter-col-title">Диаметр</div>
                <div class="filter-col-container tab-content">
                    <div class="tab-pane{{ !empty($input['Диаметр, дюймы']) || empty($input['Диаметр, мм']) ? ' active' : '' }}"
                         id="filterCol1_1">
                        <ul class="nav">
                            @foreach($filter['Диаметр']['дюймы'] as $key => $item)
                                @if($key)
                                    <li><label><input name="Filter[Диаметр, дюймы][]"
                                                      value="{{ str_replace('.', ',', $key) }}"
                                                      type="checkbox" <?php echo (isset($input['Диаметр, дюймы']) && in_array((string)$key, str_replace(',', '.', $input['Диаметр, дюймы']), true)) ? 'checked="checked"' : '' ?>/> {{ $key }}
                                        </label></li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                    <div class="tab-pane{{ !empty($input['Диаметр, мм']) ? ' active' : '' }}" id="filterCol1_2">
                        <ul class="nav">
                            @foreach($filter['Диаметр']['мм'] as $key => $item)
                                @if($key)
                                    <li><label><input name="Filter[Диаметр, мм][]" value="{{ $key }}"
                                                      type="checkbox" <?php echo (isset($input['Диаметр, мм']) && in_array((string)$key, str_replace(',', '.', $input['Диаметр, мм']), true)) ? 'checked="checked"' : '' ?>/> {{ $key }}
                                        </label></li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
                <ul class="nav nav-pills">
                    <li{{ !empty($input['Диаметр, дюймы']) || empty($input['Диаметр, мм']) ? ' class="active"' : '' }}><a href="#filterCol1_1" role="pill" data-toggle="pill">дюймы</a></li>
                    <li{{ !empty($input['Диаметр, мм']) ? ' class="active"' : '' }}><a href="#filterCol1_2" role="pill" data-toggle="pill">мм</a></li>
                </ul>
            </div>
        </div>
        <!-- /FILTER COLUMN -->
        <!-- FILTER COLUMN -->
        <div class="col-lg-2 col-md-3 col-sm-3 col-xs-6">
            <div class="filter-col">
                <div class="filter-col-title">Ширина</div>
                <div class="filter-col-container tab-content">
                    <div class="tab-pane{{ !empty($input['Ширина, дюймы']) || !in_array($categoryAlias, ['legk', 'legkogruz', 'gruz']) ? ' active' : '' }}"
                         id="filterCol2_1">
                        <ul class="nav">
                            @foreach($filter['Ширина']['дюймы'] as $key => $item)
                                @if($key)
                                    <li><label><input name="Filter[Ширина, дюймы][]"
                                                      value="{{ str_replace('.', ',', $key) }}"
                                                      type="checkbox" <?php echo (isset($input['Ширина, дюймы']) && in_array((string)$key, $input['Ширина, дюймы'], true)) ? 'checked="checked"' : '' ?>/> {{ $key }}
                                        </label></li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                    <div class="tab-pane{{ !empty($input['Ширина, мм']) || empty($input['Ширина, дюймы']) && in_array($categoryAlias, ['legk', 'legkogruz', 'gruz']) ? ' active' : '' }}"
                         id="filterCol2_2">
                        <ul class="nav">
                            @foreach($filter['Ширина']['мм'] as $key => $item)
                                @if($key)
                                    <li><label><input name="Filter[Ширина, мм][]" value="{{ $key }}" type="checkbox" <?php echo (isset($input['Ширина, мм']) && in_array((string)$key, $input['Ширина, мм'], true)) ? 'checked="checked"' : '' ?>/> {{ $key }}</label></li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
                <ul class="nav nav-pills">
                    <li{{ !empty($input['Ширина, дюймы']) || !in_array($categoryAlias, ['legk', 'legkogruz', 'gruz']) ? ' class="active"' : '' }}>
                        <a href="#filterCol2_1" role="pill" data-toggle="pill">дюймы</a></li>
                    <li{{ !empty($input['Ширина, мм']) || empty($input['Ширина, дюймы']) && in_array($categoryAlias, ['legk', 'legkogruz', 'gruz']) ? ' class="active"' : '' }}>
                        <a href="#filterCol2_2" role="pill" data-toggle="pill">мм</a></li>
                </ul>
            </div>
        </div>
        <!-- /FILTER COLUMN -->
        <!-- FILTER COLUMN -->
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
            <div class="filter-col">
                <div class="filter-col-title">Высота</div>
                <div class="filter-col-container tab-content" id="filterCol3">
                    <ul class="nav">
                        @foreach($filter['Высота'] as $key => $item)
                            @if($key)
                                <li><label><input name="Filter[Высота][]" value="{{ $key }}" type="checkbox" <?php echo (isset($input['Высота']) && in_array((string)$key, $input['Высота'], true)) ? 'checked="checked"' : '' ?>/> {{ $key }}</label></li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <!-- /FILTER COLUMN -->
        <!-- FILTER COLUMN -->
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
            <div class="filter-col">
                <div class="filter-col-title">Бренд</div>
                <div class="filter-col-container tab-content" id="filterCol4">
                    <ul class="nav">
                        <li><label><input type="checkbox" class="filter-select-all"> Все бренды</label></li>
                        @foreach($filter['Бренд'] as $key => $item)
                            @if($key)
                                <li><label><input name="Filter[Бренд][]" value="{{ $key }}" type="checkbox" <?php echo (isset($input['Бренд']) && in_array($key, $input['Бренд'])) ? 'checked="checked"' : '' ?>/> {{ $key }}</label></li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <!-- /FILTER COLUMN -->

        <!-- FILTER COLUMN -->
        <div class="col-md-2 col-sm-4 col-xs-4">
            <button type="submit" class="btn btn-block btn-primary" id="showTireFilter">Найти сейчас</button>
            <a href="#" class="btn btn-block btn-primary" id="resetTireFilter">Сбросить фильтр</a>
        </div>
        <!-- /FILTER COLUMN -->
    </div>
    <div class="row filter-additional">
        <div class="col-lg-12">
            <label><input name="Filter[Сезон][]" value="Всесезонный" <?php echo (isset($input['Сезон']) && in_array('Всесезонный', $input['Сезон'])) ? 'checked="checked"' : '' ?> type="checkbox"/> <i class="icon icon-filter-all"></i> Всесезонный</label>
            <label><input name="Filter[Сезон][]" value="Зима" <?php echo (isset($input['Сезон']) && in_array('Зима', $input['Сезон'])) ? 'checked="checked"' : '' ?> type="checkbox"/> <i class="icon icon-filter-winter"></i> Зима</label>
            <label><input name="Filter[Сезон][]" value="Лето" <?php echo (isset($input['Сезон']) && in_array('Лето', $input['Сезон'])) ? 'checked="checked"' : '' ?> type="checkbox"/> <i class="icon icon-filter-summer"></i> Лето</label>
            <label><input name="Filter[Шипы][]" value="1" <?php echo (isset($input['Шипы'])) ? 'checked="checked"' : '' ?> type="checkbox"/> <i class="icon icon-filter-spikes"></i> Шипы</label>
        </div>
    </div>
</form>
<!-- /FILTER -->

<script>
    window.tireFilterData = JSON.parse('<?= str_replace("'", "\'", json_encode($filter)) ?>');
    window.tireFilterByCategoryData = JSON.parse('<?= str_replace("'", "\'", json_encode($filterByCategory)) ?>');
</script>