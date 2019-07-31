@extends('layouts.' . ($isAjax ? 'post' : 'default'))

@section('content')
    <div class="content">
        <h1 class="font-bold bottom-buffer">Восстановление пароля</h1>
        {? $form = \widgets\ActiveForm::begin() ?}

        <div class="row bottom-buffer">
            <div class="col-xs-4">
                @if(empty($status))
                    {{ $form->field($model, 'email', ['inputOptions' => ['required' => true, 'class' => 'form-control']])->label('Введите ваш Email адрес')->input('email') }}

                    {{ \helpers\Html::submitButton('Восстановить', ['class' => 'btn btn-primary']) }}
                @elseif($status == 'sended')
                    Письмо с инструкциями было успешно отправлено на ваш почтовый адрес.
                @elseif($status == 'activate')
                    {{ $form->field($model, 'password', ['inputOptions' => ['required' => true, 'class' => 'form-control']])->passwordInput() }}
                    <br/>
                    {{ \helpers\Html::submitButton('Отправить', ['class' => 'btn btn-primary']) }}
                @elseif($status == 'success')
                    <div class="alert alert-success">Пароль был успешно изменен.</div>
                    <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#AuthorizationModal">
                        Авторизоваться
                    </a>
                @endif
            </div>
        </div>

        {{ $form->end() }}
    </div>
@stop