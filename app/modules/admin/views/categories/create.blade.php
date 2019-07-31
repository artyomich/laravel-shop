{?
$form = \widgets\ActiveForm::begin([
    'action' => '/admin/categories/create' . (!empty($modelName) ? ('?model=' . $modelName) : ''),
    'options' => ['data-ajax'=>'1']
]);
?}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
            class="sr-only">Close</span></button>
    <h4 class="modal-title">Новая категория</h4>
</div>
@if ($model->isNewRecord() || $model->hasErrors())
<div class="modal-body">
    {{ $form->field($model, 'name') }}
    {{ $form->field($model, 'alias') }}
    {{ $form->field($model, 'is_visible')->checkbox() }}
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-link" data-dismiss="modal">отмена</button>
    <button type="submit" class="btn btn-success">Создать</button>
</div>
@endif
{{ $form->end() }}