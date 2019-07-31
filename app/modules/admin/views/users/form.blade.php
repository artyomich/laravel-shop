@use('helpers\Html')

{?
$btnSubmit = !$model->exists ?
    Html::submitButton('Создать', ['class' => 'btn btn-success']) :
    Html::submitButton('Сохранить', ['class' => 'btn btn-primary']);

$form = \widgets\ActiveForm::begin([
    'action' => '/admin/users/' . ($model->exists ? 'update/' . $model->id : 'create'),
    'options' => ['data-ajax'=>'1']
]);

$userGroups = \helpers\ArrayHelper::map($model->groups()->get(), 'id', 'alias');
?}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
            class="sr-only">Close</span></button>
    <h4 class="modal-title">{{$model->isNewRecord() ? 'Новый пользователь' : 'Редактирование пользователя'}}</h4>
</div>
<div class="modal-body">
    <ul class="nav nav-tabs" role="tablist">
        <li class="active">
            <a href="#usersMainTab" role="tab" data-toggle="tab">Основные</a>
        </li>
        <li>
            <a href="#usersGroupsTab" role="tab" data-toggle="tab">Группы</a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="usersMainTab">
            {{ $form->field($model, 'first_name') }}
            {{ $form->field($model, 'last_name') }}
            {{ $form->field($model, 'email')->textInput(['type' => 'email']) }}
            {{ $form->field($model, 'phone') }}
            {{ $form->field($model, 'city_id')->dropDownList(
                \helpers\ArrayHelper::map(\models\Cities::all(), 'id', 'name'))
            }}
            {{ $form->field($model, 'is_male')->dropDownList(['0' => 'Жен.', '1' => 'Муж.'])
            }}
            {{ $form->field($model, 'password', $model->isNewRecord() ? [] : [
                'addon' => [
                    'prepend' => ['content' => \helpers\Html::activeCheckbox($model, 'isSetNewPassword')]
                ]
            ])->passwordInput(['value' => '']) }}
        </div>
        <div class="tab-pane" id="usersGroupsTab">
            @foreach ($groups as $group)
                <div class="checkbox">
                    <label>
                        {{\helpers\Html::checkbox('UsersGroups[' . $group->alias . ']',
                            in_array($group->alias, $userGroups))}} {{$group->name}}
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