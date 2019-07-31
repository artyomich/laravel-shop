{{--*/ $form = \widgets\ActiveForm::begin() /*--}}
{{ $form->field($supportModel, 'username') }}
{{ $form->field($supportModel, 'email', ['required' => 1])->input('email') }}
{{ $form->field($supportModel, 'message')->textArea() }}
<br/>
{{ \helpers\Html::submitButton('Отправить', ['class'=> 'btn btn-primary']); }}
{{ $form->end() }}