<div class="box-body tab-pane{{ $category && $category->type == \models\Categories::TYPE_TIRES ? ' active' : '' }}"
     id="tabSimpleFilter1">
    <section id="navSearchCarTires"
             class="nav-search-car-tires" {{ $formDisplay ? '' : 'style="display:none"'}}>
        <nav class="navbar navbar-catalog row">
            <ul class="nav">
                @foreach($categories as $key => $category)
                    @if($category->type === \models\Categories::TYPE_TIRES)
                        <li class="col-md-{{ in_array($category->alias, ['gruz', 'kgsh']) ? 3 : 2 }} col-xs-6 text-center">
                            <a href="/catalog/{{ $category->alias }}/"{{ $categoryAlias == $category->alias ? ' class="active"' : '' }}>
                                <img src="/img/menu/{{ $category->alias }}.png" alt="{{ $category->name }}"/>

                                <div>{{ $category->name }}</div>
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </nav>
        <!-- FILTER -->
        <form method="get" action="/catalog/{{ $categoryAlias }}/" class="simple-filter" id="simpleTireFilter">
            <input type="hidden" vendor="type" value="0">
            <div class="row">
                <div class="col-xs-4">
                    <select class="form-control" name="Filter[Ширина]" id="filterColWidth">
                        <option value="">-- Ширина --</option>
                        @foreach($filterData['Ширина'] as $key => $item)
                            @if($key)
                                <option value="{{ str_replace('.', ',', $key) }}"
                                        {{ isset($input['Ширина']) && $input['Ширина'] == str_replace('.', ',', $key) ? ' selected' : '' }}
                                > {{ $key }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-xs-4">
                    <select class="form-control" name="Filter[Высота]">
                        <option value="">-- Профиль --</option>
                        @foreach($filterData['Высота'] as $key => $item)
                            @if($key)
                                <option value="{{ str_replace('.', ',', $key) }}"
                                        {{ isset($input['Высота']) && ($input['Высота'] == $key || is_array($input['Высота']) && $input['Высота'][0] == $key) ? ' selected' : '' }}
                                > {{ $key }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-xs-4">
                    <select class="form-control" name="Filter[Диаметр]" id="filterColDiameter">
                        <option value="">-- Диаметр --</option>
                        @foreach($filterData['Диаметр'] as $key => $item)
                            @if($key)
                                <option value="{{ str_replace('.', ',', $key) }}"
                                        {{ isset($input['Диаметр']) && $input['Диаметр'] == str_replace('.', ',', $key) ? ' selected' : '' }}
                                > {{ $key }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="col-xs-4">
                    <select class="form-control simple-filter-col-category" name="category_id">
                        <option value="">-- Тип шины --</option>
                        @foreach($categories as $category)
                            @if ($category->type == \models\Categories::TYPE_TIRES)
                            <option value="{{$category->alias}}"
                                    {{ $categoryAlias == $category->alias ? ' selected="selected"' : '' }}
                                    data-id="{{$category->id}}">{{$category->name}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-xs-4">
                    <select class="form-control" name="Filter[Сезон][]" id="filterColSeason"
                            style="display: {{  ($_SERVER["REQUEST_URI"] == '/' || $categoryAlias == 'legk') ? 'block' : 'none' }}">
                        <option value="">-- Сезон --</option>
                        <option value="Всесезонный"{{ isset($input['Сезон']) && isset($input['Сезон'][0]) && $input['Сезон'][0] == 'Всесезонный' ? ' selected="selected"' : '' }}>
                            Всесезонный
                        </option>
                        <option value="Зима"{{ isset($input['Сезон']) && isset($input['Сезон'][0]) && $input['Сезон'][0] == 'Зима' ? ' selected="selected"' : '' }}>
                            Зима
                        </option>
                        <option value="Лето"{{ isset($input['Сезон']) && isset($input['Сезон'][0]) && $input['Сезон'][0] == 'Лето' ? ' selected="selected"' : '' }}>
                            Лето
                        </option>
                    </select>
                </div>
                <div class="col-xs-4">
                    <select class="form-control" name="Filter[Бренд]" id="filterColBrand">
                        <option value="">-- Все бренды --</option>
                        @foreach($filterData['Бренд'] as $key => $item)
                            @if($key)
                                <option value="{{ str_replace('.', ',', $key) }}"
                                        {{ isset($input['Бренд']) && ($input['Бренд'] == $key || is_array($input['Бренд']) && $input['Бренд'][0] == $key) ? ' selected' : '' }}> {{ $key }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="col-xs-3">
                    {{--*/ $filter = \Request::get('Filter', []); $filter['СортировкаЦена'] = (isset($filter['СортировкаЦена']) && $filter['СортировкаЦена'] == 'ASC') ? 'DESC' : 'ASC'; /*--}}
                    {{--*/ $request = \Request::all(); $request['Filter'] = $filter; /*--}}
                    <a href="/catalog/{{ $categoryAlias }}/?{{ http_build_query($request) }}"
                       id="catalogSort"><span>Сортировать по цене</span> <i
                                class="glyphicon glyphicon-sort-by-attributes{{ $filter['СортировкаЦена'] != 'ASC' ? '-alt' : '' }}"></i></a>
                </div>
                <div class="col-xs-6 ">
                    <button type="submit" class="btn btn-primary btn-block"
                            data-postfix-one="шину" data-postfix-two="шины" data-postfix-five="шин"
                            data-postfix-many="шины" disabled>ПОКАЗАТЬ ШИНЫ
                    </button>
                </div>
            </div>
        </form>
        <!-- /FILTER -->

        <script>
            window.tireFilterData = JSON.parse('<?= str_replace("'", "\'", json_encode($filterData)) ?>');
            window.tireFilter = JSON.parse('<?= str_replace("'", "\'", json_encode($filter)) ?>');
            $('select[name="Filter[Бренд]"]').on('change', function () {
                $("#simpleTireFilter").submit();
            });
        </script>
    </section>
</div>