@if(isset($orders->orders->toArray()['0']))
    <div class="row head_order_list">
        <div class="col-xs-1">
            <span>№ заказа</span>
        </div>
        <div class="col-xs-4 cursor-pointer" data-widget="sort" data-sort="order_data">
            <span>Дата заказа <i class="glyphicon glyphicon-sort-by-attributes"></i></span>
        </div>
        <div class="col-xs-2">
            Сумма заказа
        </div>
        <div class="col-xs-2 cursor-pointer" data-widget="sort" data-sort="order_status">
            <span>Статус <i class="glyphicon glyphicon-sort-by-attributes"></i></span>
        </div>
        <div class="col-xs-3">
            Доставка
        </div>
    </div>

    <div id="menu">
        @foreach($orders->orders as $value)
            <div class="box collapsed-box">
                <div class="row list-group-item">
                    <div class="col-xs-1">
                        <p class="order-number" data-widget="collapse">{{ $value->id }}</p>
                    </div>
                    <div class="col-xs-4 order_data">
                        {{ date('d.m.Y H:i  ', strtotime($value->date_create)) }}
                    </div>
                    <div class="col-xs-2">
                        {{ $value->cost + $value->delivery_cost }} р.
                    </div>
                    <div class="col-xs-2">
                        {{ $value->statuses[$value->status] }}
                    </div>
                    <div class="col-xs-3">
                        {{ $value->address ? $value->address : 'Самовывоз' }}
                    </div>
                </div>
                <div class="row box-body order-list-readmore">
                    <div class="row list-group head_order_list">
                        <div class="col-md-offset-1 col-xs-4">
                            Наименование
                        </div>
                        <div class="col-xs-2">
                            Цена
                        </div>
                        <div class="col-xs-1">
                            Количество
                        </div>
                    </div>
                    @foreach($value->items as $items)
                        <div class="row list-group-item">
                            <div class="col-md-offset-1 col-xs-4">
                                {{ $items->name }}
                            </div>
                            <div class="col-xs-2">
                                {{ $items->cost }} р.
                            </div>
                            <div class="col-xs-1">
                                {{ $items->amount }} шт.
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
@else
    <h1 class="text-center top-buffer"> У Вас нет заказов</h1>
@endif