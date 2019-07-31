<div class="modal fade" id="{{ $modal->getId() }}" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                            class="sr-only">Закрыть</span></button>
                <h4 class="modal-title" id="myModalLabel">Сообщить о поступлении товара</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-8 col-xs-offset-2">
                        <div style="padding: 20px 0">
        Ваш телефон<span class="close-8800">*</span>:
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon">+7</span>
                            <input type="text" name="ProductsNotifications[phone]" class="form-control"
                                   placeholder="Введите номер Вашего телефона"/>
                        </div>
                        <div style="padding: 20px 0">
        Ваш E-mail:
                        </div>
                        <div class="input-group">
                            <input type="text" name="ProductsNotifications[email]" class="form-control"
                                   placeholder="Введите Ваш E-mail"/>
                        </div>
                        <div style="padding: 20px 0">
        Актуальность:
                        </div>
                        <div class="input-group">
                            <select name="ProductsNotifications[expiration]" class="form-control">
                                <option value="7">7 дней</option>
                                <option value="14">14 дней</option>
                                <option value="30">30 дней</option>
                            </select>
                        </div>
                        <br>
                        <div>
                            <span class="close-8800">*</span> - обязательно к заполнению
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center bottom-buffer">
                <button type="submit" class="btn btn-primary btn-cart-oformit">Уведомить меня</button>
            </div>
        </form>
    </div>
</div>

