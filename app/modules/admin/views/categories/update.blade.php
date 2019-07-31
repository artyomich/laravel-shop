{?
$modelParam = !empty($modelName) ? ('?model=' . $modelName) : '';
$form = \widgets\ActiveForm::begin(
    [
        'action'  => '/admin/categories/update/' . $model->id . $modelParam,
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
    <h4 class="modal-title">{{{ $model->name }}}
        <small>редактирование категории</small>
    </h4>
</div>
<div class="modal-body">
    {{ $form->field($model, 'name') }}
    {{ $form->field($model, 'alias') }}
    {{ $form->field($model, 'is_visible')->checkbox() }}
    {{ $form->field($model, 'description')->textarea(['rows' => '20']) }}
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-danger pull-left" data-ajax="1" data-ajax-action="delete"
            data-ajax-url="{{ '/admin/categories/delete/' . $model->id . $modelParam . '&redirect=/admin/pages/' }}">
        Удалить
    </button>
    <button type="button" class="btn btn-link" data-dismiss="modal">отмена</button>
    <button type="submit" class="btn btn-primary">Сохранить</button>
</div>
{{ $form->end() }}