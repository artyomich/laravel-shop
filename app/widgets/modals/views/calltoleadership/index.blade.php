<div class="modal fade" id="{{ $modal->getId() }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                            class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">Горячая линия по работе с клиентами</h4>
            </div>
            <div class="modal-body">
                <p> Служба поддержки клиентов нашей компании работает в офисе г. Барнаул</p>

                <p>
                    Мы всегда рады ответить на интересующие вас вопросы, а также выслушать все замечания и предложения
                </p>

                <br>
                <br>
                <br>

                <p class="free-call-8800">Звонок бесплатный</p>

                <div class="row">
                    <div class="col-xs-4 body-8800">8-800-700-1422</div>
                    <div class="col-xs-8 opening-hours-8800">Часы работы с 6<sup>
                            <small>00</small>
                        </sup>-16<sup>
                            <small>00</small>
                        </sup> по московскому времени <br>
                        сейчас офис -
                        @if((date('H')>=6) and (date('H')<16))
                            <span class="open-8800">Открыт</span>
                        @else
                            <span class="close-8800">Закрыт</span>
                        @endif
                        - текущее время: {{ date('H')}}<sup>
                            <small>{{ date('m')}}</small>
                        </sup>
                    </div>
                </div>
                <br>
                <br>

                <p class="thanks-8800">
                    Наша команда будет благодарна за получение от Вас обратной связи и оценки работы
                    любого сотрудника из любого филиала
                </p>

                <p>
                    Свои замечания и предложения Вы можете направить Генеральному директору
                    "Первой объединенной шинной компании" по электронной почте:
                    <a href="mailto:user1372@ashk.ru">user1372@ashk.ru</a>
                </p>
            </div>
        </div>
    </div>
</div>