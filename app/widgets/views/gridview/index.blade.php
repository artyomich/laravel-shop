@use('helpers\Html')

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
        <form>
            @foreach ($filters as $column)
            <th>{{isset($column) ? $column : ''}}</th>
            @endforeach
            <input type="submit" class="hidden">
        </form>
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

{{ $paginator }}

<script>
    $(function() {
        $('select', '.grid-view').on('change', function() {
            $('form', '.grid-view').submit();
        });

        $('.grid-view a').each(function () {
            if ($(this).text().length > 50) {
                $(this).text($(this).text().substr(0, 50) + '...');
            }
        })
    })
</script>