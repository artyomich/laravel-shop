<div class="modal fade" id="{{ $modal->getId() }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <input type="hidden" name="cityName" value="{{ $city->name }}">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                            class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">Что можно улучшить на этой странице?</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="improveComment" class="control-label">Ваш комментарий</label>
                    <textarea name="comment" class="form-control" rows="5" cols="40" id="improveComment"></textarea>
                </div>
                <br/>

                <div class="form-group">
                    <label for="improveEmail" class="control-label">Email</label>
                    <input type="email" name="revommend[email]" class="form-control" id="improveEmail"/>
                </div>
                <br/>
                <span class="glyphicon glyphicon-info-sign"></span> <span class="text-muted">Email указывать не обязательно. Но мы будем благодарны за возможность связаться с вами для уточнения информации.</span>
                <br/><br/>

                <div class="text-center bottom-buffer">
                    <button type="button" id="sendMessage" class="btn btn-primary">Отправить</button>
                </div>
            </div>

        </div>
    </div>
</div>