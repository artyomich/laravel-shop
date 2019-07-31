@if(count($model->items))
    <table class="table">
    @foreach($model->items as $item)
    <tr>
        <td class="col-xs-6"><a href="/admin/products/update/{{ $item->id }}/" target="_blank">{{ $item->name }}</a></td>
        <td class="col-xs-2">
            <div class="input-group">
                <input type="number" name="amount[{{ $item->id }}]" value="{{ $item->amount }}" class="form-control" />
                <span class="input-group-addon">шт.</span>
            </div>
        </td>
        <td class="col-xs-1 text-center">
            <a href="#" class="btn btn-link"><i class="glyphicon glyphicon-trash text-danger"></i></a>
        </td>
    </tr>
    @endforeach
    </table>
@else
    <p>Пусто</p><br/>
@endif