<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace widgets;

use components\Widget;
use components\ActiveRecord;
use helpers\Object;
use helpers\Html;

/**
 * Class ActiveForm
 *
 * @property string        $action
 * @property string        $method
 * @property ActiveRecord  $model
 *
 * @package  widgets
 */
class ActiveForm extends Widget
{
    /**
     * @var string куда отправлять данные формы.
     */
    public $action = '';

    /**
     * @var string метод отправкиданных.
     *
     * ```php
     * $form = ActiveForm::begin([
     *     'method' => 'get',
     *     'action' => ['module/controller/action'],
     * ]);
     * ```
     */
    public $method = 'post';

    /**
     * @var boolean whether to perform encoding on the error summary.
     */
    public $encodeErrorSummary = true;

    /**
     * @var string тип формы
     */
    public $type = 'vertical';

    /**
     * @return string вернет виджет в виде строки.
     */
    public function run()
    {
        echo Html::endForm();
    }

    /**
     * Инициализация виджета.
     */
    public function init()
    {
        echo Html::beginForm($this->action, $this->method, $this->options) .
            '<input type="hidden" name="_token" value="' . csrf_token() . '">';
    }

    /**
     * @param       $model
     * @param       $attribute
     * @param array $options
     *
     * @return ActiveField
     */
    public function field($model, $attribute, $options = [])
    {
        return Object::create(
            array_merge(
                ['class' => ActiveField::className()], $options, [
                    'model'     => $model,
                    'attribute' => $attribute,
                    'form'      => $this,
                ]
            )
        );
    }

    /**
     * Generates a summary of the validation errors.
     * If there is no validation error, an empty error summary markup will still be generated, but it will be hidden.
     * @param ActiveRecord|ActiveRecord[] $models the model(s) associated with this form
     * @param array $options the tag options in terms of name-value pairs. The following options are specially handled:
     *
     * - header: string, the header HTML for the error summary. If not set, a default prompt string will be used.
     * - footer: string, the footer HTML for the error summary.
     *
     * The rest of the options will be rendered as the attributes of the container tag. The values will
     * be HTML-encoded using [[\yii\helpers\Html::encode()]]. If a value is null, the corresponding attribute will not be rendered.
     * @return string the generated error summary
     * @see errorSummaryCssClass
     */
    public function errorSummary($models, $options = [])
    {
        Html::addCssClass($options, $this->errorSummaryCssClass);
        $options['encode'] = $this->encodeErrorSummary;
        return Html::errorSummary($models, $options);
    }
}