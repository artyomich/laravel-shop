<div class="modal fade" id="{{ $modal->getId() }}" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content">
            <input type="hidden" name="Events[type]" value="1">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                            class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">Обратный звонок специалиста</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-8 col-xs-offset-2">
                        <div class="text-center">
                            <img src="/img/callme.jpg"/>
                        </div>
                        <div style="padding: 20px 0">Укажите свой контактный телефон и мы перезвоним вам в ближайшие
                            несколько минут:
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon">+7</span>
                            <input type="text" name="Events[phone]" class="form-control"
                                   placeholder="Введите номер Вашего телефона"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center bottom-buffer">
                <button type="submit" class="btn btn-primary">Жду звонка</button>
            </div>
        </form>
    </div>
</div>