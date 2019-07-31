@if(\Sentry::check())
    {{--*/ $opinion->rating = 4; /*--}}
    {{--*/ $opinion->product_id = $product->id; /*--}}

    {{--*/ $form = \widgets\ActiveForm::begin() /*--}}

    {{\helpers\Html::activeHiddenInput($opinion, 'product_id')}}

    <div class="row">
        <div class="col-xs-12 col-md-10">
            {{$form->field($opinion, 'user_fullname')}}
        </div>
        <div class="col-xs-12 col-md-2">
            <div class="form-group"><label class="control-label" for="productsopinions-rating">Оценка</label>
                {{\helpers\Html::activeDropDownList($opinion, 'rating', [1=>1, 2=>2, 3=>3, 4=>4, 5=>5], ['class'=>'form-control'])}}
                <div class="help-block error-block"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-md-6 text-success">
            {{$form->field($opinion, 'user_advantages')->textArea(['rows' => '3'])}}
        </div>
        <div class="col-xs-12 col-md-6 text-danger">
            {{$form->field($opinion, 'user_disadvantages')->textArea(['rows' => '3'])}}
        </div>
    </div>
    {{$form->field($opinion, 'user_comment')->textArea(['rows' => '3'])}}
    <br/>
    {{\helpers\Html::submitButton('Отправить', ['class' => 'btn btn-primary'])}}
    {{$form->end()}}
@else
    <p>Отзывы могут оставлять только авторизированные пользователи.</p>
    <a href="#" class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#AuthorizationModal">Авторизироваться</a>
@endif