<div class="modal fade" id="{{ $modal->getId() }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                            class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">Выбор города</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <ul class="nav nav-pills">
                        @foreach ($cities as $item)
                            <li class="col-xs-6 col-sm-4{{ $city->id == $item->id ? ' active' : '' }}">
                                <a href="#" data-city-id="{{ $item->id }}">{{ $item->name }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-center">
                    <span>Если в списке нет вашего города, выберите ближайший к вам. Мы работаем с транспортными компаниями</span>
                </div>
            </div>
        </div>
    </div>
</div>