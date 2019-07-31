<div class="box-body tab-pane{{ $category && $category->type == \models\Categories::TYPE_DISKS ? ' active' : '' }}"
     id="tabSimpleFilter2">
    <section class="nav-search-car-tires" {{ $formDisplay ? '' : 'style="display:none"'}}>
        <nav class="navbar navbar-catalog row">
            <ul class="nav">
                @foreach($categories as $key => $category)
                    @if($category->type === \models\Categories::TYPE_DISKS)
                        <li class="col-md-{{ in_array($category->alias, ['gruz-disk']) ? 3 : 2 }} col-xs-6 text-center ">
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
        <form method="get" action="/catalog/{{ $categoryAlias }}/" class="simple-filter" id="filterDisks">
            <input type="hidden" vendor="type" value="1">
            <div class="row">
                <div class="col-xs-4">
                    <select class="form-control simple-filter-col-category" name="category_id">
                        <option value="">-- Группа --</option>
                        @foreach($categories as $category)
                            @if ($category->type == \models\Categories::TYPE_DISKS)
                                <option value="{{$category->alias}}"
                                        {{ $categoryAlias == $category->alias ? ' selected="selected"' : '' }}
                                        data-id="{{$category->id}}">{{$category->name}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="col-xs-4">
                    <select class="form-control" name="Filter[ТипДиска]">
                        <option value="">-- Тип диска --</option>
                        @foreach($filterDataDisk['ТипДиска'] as $key => $item)
                            @if($item)
                                <option value="{{ str_replace('.', ',', $item) }}"
                                        {{ isset($input['ТипДиска']) && $input['ТипДиска'] == str_replace('.', ',', $item) ? ' selected' : '' }}
                                > {{ $item }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="col-xs-4">
                    <select class="form-control" name="Filter[Ширина]">
                        <option value="">-- Ширина --</option>
                        @foreach($filterDataDisk['Ширина'] as $key => $item)
                            @if($item)
                                <option value="{{ $item }}"
                                        {{ isset($input['Ширина']) && $input['Ширина'] == $item ? ' selected' : '' }}>
                                    {{ $item }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="col-xs-4">
                    <select class="form-control" name="Filter[Бренд]">
                        <option value="">-- Все бренды --</option>
                        @foreach($filterDataDisk['Бренд'] as $key => $item)
                            @if(!empty($item))
                                <option value="{{ $item }}"
                                        {{ isset($input['Бренд']) && ($input['Бренд'] == $item || is_array($input['Бренд']) && $input['Бренд'][0] == $item) ? ' selected' : '' }}> {{ $item }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="col-xs-4">
                    <select class="form-control" name="Filter[ДиаметрDIA]">
                        <option value="">-- Посадочный диаметр --</option>
                        @foreach($filterDataDisk['ДиаметрDIA'] as $key => $item)
                            @if($key)
                                <option value="{{ $item }}"
                                        {{ isset($input['ДиаметрDIA']) && $input['ДиаметрDIA'] == $item ? ' selected' : '' }}
                                > {{ $item }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="col-xs-4">
                    <select class="form-control" name="Filter[Диаметр]">
                        <option value="">-- Диаметр --</option>
                        @foreach($filterDataDisk['ДиаметрПосадочный'] as $key => $item)
                            @if(!empty($item))
                                <option value="{{ str_replace('.', ',', $item) }}"
                                        {{ isset($input['ДиаметрПосадочный']) && $input['ДиаметрПосадочный'] == $item ? ' selected' : '' }}
                                > {{ $item }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="col-xs-6">
                    <select class="form-control" name="Filter[Сверловка]">
                        <option value="">-- Сверловка --</option>
                        @foreach($filterDataDisk['Сверловка'] as $key => $item)
                            @if($key)
                                <option value="{{ str_replace('.', ',', $item) }}"
                                        {{ isset($input['Сверловка']) && $input['Сверловка'] == $item ? ' selected' : '' }}
                                > {{ $item }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="col-xs-6">
                    <select class="form-control" name="Filter[Вылет]">
                        <option value="">-- Вылет --</option>
                        @foreach($filterDataDisk['Вылет'] as $key => $item)
                            @if($key)
                                <option value="{{ str_replace('.', ',', $item) }}"
                                        {{ isset($input['Вылет']) && $input['Вылет'] == str_replace('.', ',', $item) ? ' selected' : '' }}
                                > {{ $item }}</option>
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
                            data-postfix-one="диск" data-postfix-two="диска" data-postfix-five="дисков"
                            data-postfix-many="диски" disabled>ПОКАЗАТЬ ДИСКИ
                    </button>
                </div>
            </div>
        </form>
        <!-- /FILTER -->
    </section>
</div>