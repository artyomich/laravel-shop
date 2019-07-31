<?php
/**
 * @var \components\ActiveRecord $model
 */
?>

<?php $form = \widgets\ActiveForm::begin(
    [
        'action'  => '/admin/menusTypes/update/' . $model->id,
        'type'    => 'vertical',
        'options' => [
            'data-ajax' => 1
        ]
    ]
) ?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
            class="sr-only">Close</span></button>
    <h4 class="modal-title">{{{ $model->name }}}
        <small>редактирование меню</small>
    </h4>
</div>
<div class="modal-body">
    {{ $form->field($model, 'name') }}
    {{ $form->field($model, 'alias') }}
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-danger pull-left" data-ajax="1"
            data-ajax-action="delete" data-ajax-url="{{ '/admin/menusTypes/delete/' . $model->id }}">Удалить
    </button>
    <button type="button" class="btn btn-link" data-dismiss="modal">отмена</button>
    <button type="submit" class="btn btn-primary">Сохранить</button>
</div>
{{ $form->end() }}