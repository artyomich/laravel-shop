@use('helpers\Html')

<a href="#orderForm" class="btn btn-primary" id="saveToExcel">Сохранить в Excel</a>
<a href="#orderForm" class="btn btn-primary" id="saveToExcelWithDelivery">Сохранить в Excel заказы с доставкой</a>

<form id="orderForm">
    <table class="table table-striped grid-view">
        <thead>
        <tr>
            @foreach ($model->columns() as $column)
            <th
            {{ isset($column->class) ? ' class="' . $column->class . '"' :
            ''}}>{{$column->column == '_actions' ? '' : $model->getAttributeLabel($column->column)}}</th>
            @endforeach
        </tr>
        <tr>
            @foreach ($filters as $column)
            <th>{{isset($column) ? $column : ''}}</th>
            @endforeach
            <input type="submit" class="hidden">
        </tr>
        </thead>
        <tbody>
        @foreach ($data as $column)
        <tr data-id="{{ $column->id }}">
            @foreach ($model->columns() as $item)
            {{--*/ $_col = $item->column; /*--}}
            <td class="{{ $_col == '_actions' ? 'gridview-actions' : '' }}"
                style="{{ isset($item->style) ? $item->style : '' }}">{{ $column->gridColumns[$_col] }}
            </td>
            @endforeach
        </tr>
        @endforeach
        </tbody>
    </table>
</form>

{{ $paginator }}

<script>
    $(function() {
        $('select', '.grid-view').on('change', function() {
            $(this).closest('form').submit()
        });

        $('#saveToExcel').on('click', function() {
            var $flag = $('[name="excel"]');
            if ($flag.length) {
                $flag.remove();
            }
            $($(this).attr('href')).append('<input name="excel" value="{{ \modules\admin\controllers\OrdersController::EXPORT_EXCEL }}" type="hidden"/>').submit();
            return false;
        });

        $('#saveToExcelWithDelivery').on('click', function () {
            var $flag = $('[name="excel"]');
            if ($flag.length) {
                $flag.remove();
            }
            $($(this).attr('href')).append('<input name="excel" value="{{ \modules\admin\controllers\OrdersController::EXPORT_EXCEL_WITH_DELIVERY }}" type="hidden"/>').submit();
            return false;
        });

        /**
         * Range datepicker
         */
        moment.locale('en', {
            months : [
                "Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль",
                "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"
            ],
            monthsShort : [
                "Янв", "Фев", "Мар", "Апр", "Май", "Июн",
                "Июл", "Авг", "Сен", "Окт", "Ноя", "Дек"
            ],
             weekdays : [
                 "Воскресение", "Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота"
             ],
             weekdaysMin : [
                 "Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб"
             ]
        });

        $('input.daterangepicker').daterangepicker({
            format: 'DD/MM/YYYY',
            locale: {
                applyLabel: 'Применить',
                cancelLabel: 'Отмена',
                fromLabel: 'От',
                toLabel: 'До',
                weekLabel: 'Н',
                customRangeLabel: 'Custom Range'
            }
        }, function() {
            $('#saveToExcel').click();
        });
    })
</script>