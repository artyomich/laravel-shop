<!-- AUTHORIZATION MODAL -->
<div class="modal fade" id="AuthorizationModal" tabindex="-1">
    <div class="modal-dialog-x">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                            class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">Авторизация клиента</h4>
                <span>После входа вам будут доступны скидки</span>
            </div>
            <div class="modal-body row">
                <div class="col-lg-4 col-lg-offset-1 col-md-4 col-md-4-offset-1 col-sm-4 col-sm-4-offset-1 col-xs-4 col-xs-4-offset-1">
                    <span>Введите e-mail и пароль для входа в личный кабинет</span>

                    <div class="alert-login alert-info hide" role="alert"></div>
                    {{ Form::open() }}
                    {{ Form::text('signin[email]', null, array('class' => 'form-control signin_email',  'placeholder' => 'Email адрес:', 'autofocus', 'data-form'=>'customer', 'onkeyup'=>"if(event.which==13){signin()}")) }}
                    {{ Form::password('signin[password]', array('class' => 'form-control signin_password', 'placeholder' => 'Пароль:', 'data-form'=>'customer', 'onkeyup'=>"if(event.which==13){signin()}")) }}
                    {{ Form::button('Войти',['class'=>"btn btn-primary pull-left signin", 'data-form'=>'customer']) }}
                    <div class="text-center">
                        <a href="/user/restore-password/" class="btn btn-xs btn-link" id="restorePassword">Восстановить
                            пароль</a>
                    </div>
                    {{ Form::close() }}
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 or">
                    <p> или </p>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 ">
                    <span>Еще не зарегистрированы? </br>Получите скидку за пару кликов</span>

                    <div class="alert-registration alert-info hide" role="alert"></div>
                    {{ Form::text('email', null, array('class' => 'form-control registration_email', 'placeholder' => 'Email адрес:', 'data-form'=>'customer', 'onkeyup'=>"if(event.which==13){signout()}")) }}
                    {{ Form::text('name', null, array('class' => 'form-control registration_name', 'placeholder' => 'Имя:', 'data-form'=>'customer', 'onkeyup'=>"if(event.which==13){signout()}")) }}
                    {{ Form::submit('Зарегистрироваться',['class'=>"btn btn-primary pull-left registration_send", 'data-form'=>'customer']) }}
                </div>
            </div>

        </div>
    </div>
</div>
<!-- /AUTHORIZATION MODAL  -->

<!-- REGISTRATION MODAL -->
<div class="modal fade" id="RegistrationModal" tabindex="-1">
    <div class="modal-dialog-x send-mail">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                        class="sr-only">Close</span></button>
            <div class="" style="text-align: center; height: 130px; width: 200px; padding: 10px">
                <h4 class="modal-title" id="myModalLabel">Пароль отправлен на Вашу почту</h4>
                <input type="button" value="OK" class="btn btn-primary pull-left" data-dismiss="modal"
                       style="width: 100%; margin-top: 15px">
            </div>
        </div>
    </div>
</div>
<!-- /REGISTRATION MODAL  -->
