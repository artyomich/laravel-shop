{?
$form = \widgets\ActiveForm::begin(
    [
        'action'  => '/admin/banners/group' . (!$model->isNewRecord() ? 'update/' . $model->id : 'create'),
        'type'    => 'vertical',
        'options' => [
            'data-ajax' => 1
        ]
    ]
)
?}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
            class="sr-only">Close</span></button>
    <h4 class="modal-title">
        @if (!$model->isNewRecord())
        {{{ $model->name }}}<small>редактирование группы банеров</small>
        @else
        Создание группы банеров
        @endif
    </h4>
</div>
<div class="modal-body">
    {{ $form->field($model, 'name') }}
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-danger pull-left" data-ajax="1" data-ajax-action="delete"
            data-ajax-url="{{ '/admin/banners/groupdelete/' . $model->id }}">
        Удалить
    </button>
    <button type="button" class="btn btn-link" data-dismiss="modal">отмена</button>
    <button type="submit" class="btn btn-primary">Сохранить</button>
</div>
{{ $form->end() }}