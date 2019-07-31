@use('helpers\Html')

{?
$btnSubmit = !$model->exists ?
    Html::submitButton('Создать', ['class' => 'btn btn-success']) :
    Html::submitButton('Сохранить', ['class' => 'btn btn-primary']);

$form = \widgets\ActiveForm::begin([
    'action' => '/admin/users/group' . ($model->exists ? 'update/' . $model->id : 'create'),
    'options' => ['data-ajax'=>'1']
]);
?}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
            class="sr-only">Close</span></button>
    <h4 class="modal-title">{{$model->name}}</h4>
</div>
<div class="modal-body">
    <ul class="nav nav-tabs" role="tablist" id="ordersTabs">
        <li class="active">
            <a href="#groupMainTab" role="tab" data-toggle="tab">Основные</a>
        </li>
        <li>
            <a href="#groupRulesTab" role="tab" data-toggle="tab">Права</a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="groupMainTab">
            {{$form->field($model, 'name')}}
            {{$form->field($model, 'alias')}}
        </div>
        <div class="tab-pane" id="groupRulesTab">
            @foreach($model->permissionsList() as $rule => $label)
                <div class="checkbox">
                    <label>
                        <input type="checkbox"
                               name="{{$model->formName()}}[userPermissions][{{$rule}}]"
                               value="{{\models\Groups::RULE_ACCEPT}}"
                               {{$model->check($rule) ? ' checked="checked"' : ''}}> {{$label}}
                    </label>
                </div>
            @endforeach
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-link" data-dismiss="modal">отмена</button>
    {{$btnSubmit}}
</div>
{{ $form->end() }}