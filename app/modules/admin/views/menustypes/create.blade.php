<?php
/**
 * @var \components\ActiveRecord $model
 */
?>
{{--*/ $form = \widgets\ActiveForm::begin([
'action' => '/admin/menusTypes/create',
'options' => ['data-ajax'=>'1']
]) /*--}}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
            class="sr-only">Close</span></button>
    <h4 class="modal-title">Новое меню</h4>
</div>
@if ($model->isNewRecord() || $model->hasErrors())
<div class="modal-body">
    {{ $form->field($model, 'name')->textField(['class' => 'form-control form-autoalias']) }}
    {{ $form->field($model, 'alias')->textField(['data-autoalias' => 1]) }}
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-link" data-dismiss="modal">отмена</button>
    <button type="submit" class="btn btn-success">Создать</button>
</div>
@endif
{{ $form->end() }}