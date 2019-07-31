<?php
/**
 * Основано на Yii2.ActiveField
 *
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace widgets;

use components\ActiveRecord;
use components\Component;
use helpers\Html;

/**
 * Class ActiveField
 *
 * @property string        $template   шаблон для рендеринга поля.
 * @property ActiveRecord  $model
 * @property ActiveForm    $form
 * @property string        $attribute
 *
 * @package  widgets
 */
class ActiveField extends Component
{
    /**
     * @var ActiveRecord
     */
    public $model;

    /**
     * @var ActiveForm
     */
    public $form;

    /**
     * @var string
     */
    public $attribute;

    /**
     * @var string
     */
    public $hint;

    /**
     * @const string шаблон по умолчанию.
     */
    const DEFAULT_TEMPLATE = "<div class=\"form-group\">{label}\n{input}\n{hint}\n{error}</div>";

    /**
     * @var string шаблон для рендеринга поля.
     */
    public $template = self::DEFAULT_TEMPLATE;

    /**
     * @var array the default options for the input tags. The parameter passed to individual input methods
     * (e.g. [[textInput()]]) will be merged with this property when rendering the input tag.
     * @see \helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $inputOptions = ['class' => 'form-control'];

    /**
     * @var array the default options for the error tags. The parameter passed to [[error()]] will be
     * merged with this property when rendering the error tag.
     * The following special options are recognized:
     *
     * - tag: the tag name of the container element. Defaults to "div".
     *
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $errorOptions = ['class' => 'help-block error-block'];

    /**
     * @var array the default options for the label tags. The parameter passed to [[label()]] will be
     * merged with this property when rendering the label tag.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $labelOptions = ['class' => 'control-label'];

    /**
     * @var array
     */
    public $addonOptions = [];

    /**
     * @var array different parts of the field (e.g. input, label). This will be used together with
     * [[template]] to generate the final field HTML code. The keys are the token names in [[template]],
     * while the values are the corresponding HTML code. Valid tokens include `{input}`, `{label}` and `{error}`.
     * Note that you normally don't need to access this property directly as
     * it is maintained by various methods of this class.
     */
    public $parts = [];

    /**
     * Магия! Конвертирует поле в строку.
     *
     * @return string
     */
    public function __toString()
    {
        try {
            //  Меняем шаблон от типа формы.
            if ($this->form->type == 'horizontal' && $this->template == self::DEFAULT_TEMPLATE) {
                $this->template = "<div class=\"form-group row\">" .
                    "{label}\n<div class=\"col-sm-10\">{input}</div>\n" .
                    "<div class=\"col-sm-10 col-sm-offset-2 text-muted\"><small>{hint}</small></div>" .
                    "<div class=\"col-sm-10 col-sm-offset-2\">{error}</div>" .
                    "</div>";
                $this->labelOptions = ['class' => 'col-sm-2 control-label form-control-static'];
            }

            if ($this->addon) {
                $addon = '';
                if (isset($this->addon['prepend'])) {
                    $addon .= isset($this->addon['prepend']['asButton']) && $this->addon['prepend']['asButton']
                        ?
                        ('<span class="input-group-btn"><button class="btn btn-default" type="button">' .
                            $this->addon['prepend']['content'] . '</button></span>')
                        :
                        ('<span class="input-group-addon">' . $this->addon['prepend']['content'] . '</span>');
                }
                $addon .= '{input}';
                if (!empty($this->addon['append'])) {
                    $addon .= isset($this->addon['append']['asButton']) && $this->addon['append']['asButton']
                        ?
                        ('<span class="input-group-btn"><button class="btn btn-default" type="button">' .
                            $this->addon['append']['content'] . '</button></span>')
                        :
                        ('<span class="input-group-addon">' . $this->addon['append']['content'] . '</span>');
                }

                $this->template = str_replace(
                    '{input}', '<div class="input-group">' . $addon . '</div>', $this->template
                );

            }

            if (!isset($this->parts['{input}'])) {
                $this->parts['{input}'] = Html::activeTextInput($this->model, $this->attribute, $this->inputOptions);
            }

            if (!isset($this->parts['{label}'])) {
                $this->parts['{label}'] = Html::activeLabel($this->model, $this->attribute, $this->labelOptions);
            }

            if (!isset($this->parts['{error}'])) {
                $this->parts['{error}'] = Html::error($this->model, $this->attribute, $this->errorOptions);
            }

            if (!isset($this->parts['{hint}'])) {
                $this->parts['{hint}'] = $this->hint;
            }
        } catch (\Exception $e) {
            \App::abort(500, $e->getMessage());
        }

        return strtr($this->template, $this->parts);
    }

    /**
     * Текстовое поле.
     *
     * @param array $options
     * @return $this
     */
    public function textField($options = [])
    {
        $options = array_merge($this->inputOptions, $options);
        $this->adjustLabelFor($options);
        $this->parts['{input}'] = Html::activeTextInput($this->model, $this->attribute, $options);

        return $this;
    }

    /**
     * Renders an input tag.
     *
     * @param string $type    the input type (e.g. 'text', 'password')
     * @param array  $options the tag options in terms of name-value pairs. These will be rendered as
     *                        the attributes of the resulting tag. The values will be HTML-encoded using [[Html::encode()]].
     *
     * @return static the field object itself
     */
    public function input($type, $options = [])
    {
        $options = array_merge($this->inputOptions, $options);
        $this->adjustLabelFor($options);
        $this->parts['{input}'] = Html::activeInput($type, $this->model, $this->attribute, $options);

        return $this;
    }

    /**
     * Renders a text input.
     * This method will generate the "name" and "value" tag attributes automatically for the model attribute
     * unless they are explicitly specified in `$options`.
     *
     * @param array $options the tag options in terms of name-value pairs. These will be rendered as
     *                       the attributes of the resulting tag. The values will be HTML-encoded using [[Html::encode()]].
     *
     * @return static the field object itself
     */
    public function textInput($options = [])
    {
        $options = array_merge($this->inputOptions, $options);
        $this->adjustLabelFor($options);
        $this->parts['{input}'] = Html::activeTextInput($this->model, $this->attribute, $options);

        return $this;
    }

    /**
     * Renders a hidden input.
     *
     * Note that this method is provided for completeness. In most cases because you do not need
     * to validate a hidden input, you should not need to use this method. Instead, you should
     * use [[\yii\helpers\Html::activeHiddenInput()]].
     *
     * This method will generate the "name" and "value" tag attributes automatically for the model attribute
     * unless they are explicitly specified in `$options`.
     *
     * @param array $options the tag options in terms of name-value pairs. These will be rendered as
     *                       the attributes of the resulting tag. The values will be HTML-encoded using [[Html::encode()]].
     *
     * @return static the field object itself
     */
    public function hiddenInput($options = [])
    {
        $options = array_merge($this->inputOptions, $options);
        $this->adjustLabelFor($options);
        $this->parts['{input}'] = Html::activeHiddenInput($this->model, $this->attribute, $options);

        return $this;
    }

    /**
     * Renders a password input.
     * This method will generate the "name" and "value" tag attributes automatically for the model attribute
     * unless they are explicitly specified in `$options`.
     *
     * @param array $options the tag options in terms of name-value pairs. These will be rendered as
     *                       the attributes of the resulting tag. The values will be HTML-encoded using [[Html::encode()]].
     *
     * @return static the field object itself
     */
    public function passwordInput($options = [])
    {
        $options = array_merge($this->inputOptions, $options);
        $this->adjustLabelFor($options);
        $this->parts['{input}'] = Html::activePasswordInput($this->model, $this->attribute, $options);

        return $this;
    }

    /**
     * Renders a file input.
     * This method will generate the "name" and "value" tag attributes automatically for the model attribute
     * unless they are explicitly specified in `$options`.
     *
     * @param array $options the tag options in terms of name-value pairs. These will be rendered as
     *                       the attributes of the resulting tag. The values will be HTML-encoded using [[Html::encode()]].
     *
     * @return static the field object itself
     */
    public function fileInput($options = [])
    {
        // https://github.com/yiisoft/yii2/pull/795
        if ($this->inputOptions !== ['class' => 'form-control']) {
            $options = array_merge($this->inputOptions, $options);
        }
        $this->adjustLabelFor($options);
        $this->parts['{input}'] = Html::activeFileInput($this->model, $this->attribute, $options);

        return $this;
    }

    /**
     * Renders a text area.
     * The model attribute value will be used as the content in the textarea.
     *
     * @param array $options the tag options in terms of name-value pairs. These will be rendered as
     *                       the attributes of the resulting tag. The values will be HTML-encoded using [[Html::encode()]].
     *
     * @return static the field object itself
     */
    public function textarea($options = [])
    {
        $options = array_merge($this->inputOptions, $options);
        $this->adjustLabelFor($options);
        $this->parts['{input}'] = Html::activeTextarea($this->model, $this->attribute, $options);

        return $this;
    }

    /**
     * Renders a radio button.
     * This method will generate the "checked" tag attribute according to the model attribute value.
     *
     * @param array   $options         the tag options in terms of name-value pairs. The following options are specially handled:
     *
     * - uncheck: string, the value associated with the uncheck state of the radio button. If not set,
     *   it will take the default value '0'. This method will render a hidden input so that if the radio button
     *   is not checked and is submitted, the value of this attribute will still be submitted to the server
     *   via the hidden input.
     * - label: string, a label displayed next to the radio button.  It will NOT be HTML-encoded. Therefore you can pass
     *   in HTML code such as an image tag. If this is is coming from end users, you should [[Html::encode()]] it to prevent XSS attacks.
     *   When this option is specified, the radio button will be enclosed by a label tag.
     * - labelOptions: array, the HTML attributes for the label tag. This is only used when the "label" option is specified.
     *
     * The rest of the options will be rendered as the attributes of the resulting tag. The values will
     * be HTML-encoded using [[Html::encode()]]. If a value is null, the corresponding attribute will not be rendered.
     * @param boolean $enclosedByLabel whether to enclose the radio within the label.
     *                                 If true, the method will still use [[template]] to layout the checkbox and the error message
     *                                 except that the radio is enclosed by the label tag.
     *
     * @return static the field object itself
     */
    public function radio($options = [], $enclosedByLabel = true)
    {
        if ($enclosedByLabel) {
            if (!isset($options['label'])) {
                $attribute = Html::getAttributeName($this->attribute);
                $options['label'] = Html::encode($this->model->getAttributeLabel($attribute));
            }
            $this->parts['{input}'] = Html::activeRadio($this->model, $this->attribute, $options);
            $this->parts['{label}'] = '';
        } else {
            $this->parts['{input}'] = Html::activeRadio($this->model, $this->attribute, $options);
        }
        $this->adjustLabelFor($options);

        return $this;
    }

    /**
     * Renders a checkbox.
     * This method will generate the "checked" tag attribute according to the model attribute value.
     *
     * @param array   $options         the tag options in terms of name-value pairs. The following options are specially handled:
     *
     * - uncheck: string, the value associated with the uncheck state of the radio button. If not set,
     *   it will take the default value '0'. This method will render a hidden input so that if the radio button
     *   is not checked and is submitted, the value of this attribute will still be submitted to the server
     *   via the hidden input.
     * - label: string, a label displayed next to the checkbox.  It will NOT be HTML-encoded. Therefore you can pass
     *   in HTML code such as an image tag. If this is is coming from end users, you should [[Html::encode()]] it to prevent XSS attacks.
     *   When this option is specified, the checkbox will be enclosed by a label tag.
     * - labelOptions: array, the HTML attributes for the label tag. This is only used when the "label" option is specified.
     *
     * The rest of the options will be rendered as the attributes of the resulting tag. The values will
     * be HTML-encoded using [[Html::encode()]]. If a value is null, the corresponding attribute will not be rendered.
     * @param boolean $enclosedByLabel whether to enclose the checkbox within the label.
     *                                 If true, the method will still use [[template]] to layout the checkbox and the error message
     *                                 except that the checkbox is enclosed by the label tag.
     *
     * @return static the field object itself
     */
    public function checkbox($options = [], $enclosedByLabel = true)
    {
        if ($enclosedByLabel) {
            if (!isset($options['label'])) {
                $attribute = Html::getAttributeName($this->attribute);
                $options['label'] = Html::encode($this->model->getAttributeLabel($attribute));
            }
            $this->parts['{input}'] = Html::activeCheckbox($this->model, $this->attribute, $options);
            $this->parts['{label}'] = '';
        } else {
            $this->parts['{input}'] = Html::activeCheckbox($this->model, $this->attribute, $options);
        }
        $this->adjustLabelFor($options);

        return $this;
    }

    /**
     * Renders a drop-down list.
     * The selection of the drop-down list is taken from the value of the model attribute.
     *
     * @param array $items   the option data items. The array keys are option values, and the array values
     *                       are the corresponding option labels. The array can also be nested (i.e. some array values are arrays too).
     *                       For each sub-array, an option group will be generated whose label is the key associated with the sub-array.
     *                       If you have a list of data models, you may convert them into the format described above using
     *                       [[ArrayHelper::map()]].
     *
     * Note, the values and labels will be automatically HTML-encoded by this method, and the blank spaces in
     * the labels will also be HTML-encoded.
     * @param array $options the tag options in terms of name-value pairs. The following options are specially handled:
     *
     * - prompt: string, a prompt text to be displayed as the first option;
     * - options: array, the attributes for the select option tags. The array keys must be valid option values,
     *   and the array values are the extra attributes for the corresponding option tags. For example,
     *
     * ~~~
     * [
     *     'value1' => ['disabled' => true],
     *     'value2' => ['label' => 'value 2'],
     * ];
     * ~~~
     *
     * - groups: array, the attributes for the optgroup tags. The structure of this is similar to that of 'options',
     *   except that the array keys represent the optgroup labels specified in $items.
     *
     * The rest of the options will be rendered as the attributes of the resulting tag. The values will
     * be HTML-encoded using [[Html::encode()]]. If a value is null, the corresponding attribute will not be rendered.
     *
     * @return static the field object itself
     */
    public function dropDownList($items, $options = [])
    {
        $options = array_merge($this->inputOptions, $options);
        $this->adjustLabelFor($options);
        $this->parts['{input}'] = Html::activeDropDownList($this->model, $this->attribute, $items, $options);

        return $this;
    }

    /**
     * Renders a list box.
     * The selection of the list box is taken from the value of the model attribute.
     *
     * @param array $items   the option data items. The array keys are option values, and the array values
     *                       are the corresponding option labels. The array can also be nested (i.e. some array values are arrays too).
     *                       For each sub-array, an option group will be generated whose label is the key associated with the sub-array.
     *                       If you have a list of data models, you may convert them into the format described above using
     *                       [[\yii\helpers\ArrayHelper::map()]].
     *
     * Note, the values and labels will be automatically HTML-encoded by this method, and the blank spaces in
     * the labels will also be HTML-encoded.
     * @param array $options the tag options in terms of name-value pairs. The following options are specially handled:
     *
     * - prompt: string, a prompt text to be displayed as the first option;
     * - options: array, the attributes for the select option tags. The array keys must be valid option values,
     *   and the array values are the extra attributes for the corresponding option tags. For example,
     *
     * ~~~
     * [
     *     'value1' => ['disabled' => true],
     *     'value2' => ['label' => 'value 2'],
     * ];
     * ~~~
     *
     * - groups: array, the attributes for the optgroup tags. The structure of this is similar to that of 'options',
     *   except that the array keys represent the optgroup labels specified in $items.
     * - unselect: string, the value that will be submitted when no option is selected.
     *   When this attribute is set, a hidden field will be generated so that if no option is selected in multiple
     *   mode, we can still obtain the posted unselect value.
     *
     * The rest of the options will be rendered as the attributes of the resulting tag. The values will
     * be HTML-encoded using [[Html::encode()]]. If a value is null, the corresponding attribute will not be rendered.
     *
     * @return static the field object itself
     */
    public function listBox($items, $options = [])
    {
        $options = array_merge($this->inputOptions, $options);
        $this->adjustLabelFor($options);
        $this->parts['{input}'] = Html::activeListBox($this->model, $this->attribute, $items, $options);

        return $this;
    }

    /**
     * Renders a list of checkboxes.
     * A checkbox list allows multiple selection, like [[listBox()]].
     * As a result, the corresponding submitted value is an array.
     * The selection of the checkbox list is taken from the value of the model attribute.
     *
     * @param array $items   the data item used to generate the checkboxes.
     *                       The array values are the labels, while the array keys are the corresponding checkbox values.
     *                       Note that the labels will NOT be HTML-encoded, while the values will.
     * @param array $options options (name => config) for the checkbox list. The following options are specially handled:
     *
     * - unselect: string, the value that should be submitted when none of the checkboxes is selected.
     *   By setting this option, a hidden input will be generated.
     * - separator: string, the HTML code that separates items.
     * - item: callable, a callback that can be used to customize the generation of the HTML code
     *   corresponding to a single item in $items. The signature of this callback must be:
     *
     * ~~~
     * function ($index, $label, $name, $checked, $value)
     * ~~~
     *
     * where $index is the zero-based index of the checkbox in the whole list; $label
     * is the label for the checkbox; and $name, $value and $checked represent the name,
     * value and the checked status of the checkbox input.
     *
     * @return static the field object itself
     */
    public function checkboxList($items, $options = [])
    {
        $this->adjustLabelFor($options);
        $this->parts['{input}'] = Html::activeCheckboxList($this->model, $this->attribute, $items, $options);

        return $this;
    }

    /**
     * Renders a list of radio buttons.
     * A radio button list is like a checkbox list, except that it only allows single selection.
     * The selection of the radio buttons is taken from the value of the model attribute.
     *
     * @param array $items   the data item used to generate the radio buttons.
     *                       The array values are the labels, while the array keys are the corresponding radio values.
     *                       Note that the labels will NOT be HTML-encoded, while the values will.
     * @param array $options options (name => config) for the radio button list. The following options are specially handled:
     *
     * - unselect: string, the value that should be submitted when none of the radio buttons is selected.
     *   By setting this option, a hidden input will be generated.
     * - separator: string, the HTML code that separates items.
     * - item: callable, a callback that can be used to customize the generation of the HTML code
     *   corresponding to a single item in $items. The signature of this callback must be:
     *
     * ~~~
     * function ($index, $label, $name, $checked, $value)
     * ~~~
     *
     * where $index is the zero-based index of the radio button in the whole list; $label
     * is the label for the radio button; and $name, $value and $checked represent the name,
     * value and the checked status of the radio button input.
     *
     * @return static the field object itself
     */
    public function radioList($items, $options = [])
    {
        $this->adjustLabelFor($options);
        $this->parts['{input}'] = Html::activeRadioList($this->model, $this->attribute, $items, $options);

        return $this;
    }

    /**
     * Adjusts the "for" attribute for the label based on the input options.
     *
     * @param array $options the input options
     */
    protected function adjustLabelFor($options)
    {
        if (isset($options['id']) && !isset($this->labelOptions['for'])) {
            $this->labelOptions['for'] = $options['id'];
        }
    }
    /**
     * Generates a label tag for [[attribute]].
     * @param string|boolean $label the label to use. If null, the label will be generated via [[Model::getAttributeLabel()]].
     * If false, the generated field will not contain the label part.
     * Note that this will NOT be [[Html::encode()|encoded]].
     * @param array $options the tag options in terms of name-value pairs. It will be merged with [[labelOptions]].
     * The options will be rendered as the attributes of the resulting tag. The values will be HTML-encoded
     * using [[Html::encode()]]. If a value is null, the corresponding attribute will not be rendered.
     * @return static the field object itself
     */
    public function label($label = null, $options = [])
    {
        if ($label === false) {
            $this->parts['{label}'] = '';
            return $this;
        }

        $options = array_merge($this->labelOptions, $options);
        if ($label !== null) {
            $options['label'] = $label;
        }
        $this->parts['{label}'] = Html::activeLabel($this->model, $this->attribute, $options);

        return $this;
    }
}