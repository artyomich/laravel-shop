<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace components;

use helpers\RouteInfo;

/**
 * Родительский класс для всех моделей.
 *
 * @package components
 */
class ActiveRecord extends \Illuminate\Database\Eloquent\Model
{
    /**
     * @const
     */
    const CREATED_AT = 'date_create';

    /**
     * @const
     */
    const UPDATED_AT = 'date_update';

    /**
     * @var array стек ошибок.
     */
    private $_errors = [];

    /**
     * @var array
     */
    public $gridColumns = [];

    /**
     * @var mixed
     */
    public $scenario = null;

    /**
     * @return string вернет название вызванного класса.
     */
    public static function className()
    {
        return get_called_class();
    }

    /**
     * @return bool вернет true в случае если запись новая.
     */
    public function isNewRecord()
    {
        return !$this->exists;
    }

    /**
     * Вернет правила валидации для модели.
     *
     * ```
     * return [
     *     [
     *         ['attribute1', 'attribute2', ...],
     *         'validator type',
     *         'on' => ['scenario1', 'scenario2'] TODO: Scenarios not work now!
     *     ],
     *     ...
     * ]
     * ```
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }

    /**
     * Returns the form name that this model class should use.
     *
     * The form name is mainly used by [[\yii\widgets\ActiveForm]] to determine how to name
     * the input fields for the attributes in a model. If the form name is "A" and an attribute
     * name is "b", then the corresponding input name would be "A[b]". If the form name is
     * an empty string, then the input name would be "b".
     *
     * By default, this method returns the model class name (without the namespace part)
     * as the form name. You may override it when the model is used in different forms.
     *
     * @return string the form name of this model class.
     */
    public function formName()
    {
        return (new \ReflectionClass($this))->getShortName();
    }

    /**
     * @return array колонки, которые будут отображаться в гриде по умолчанию.
     */
    public function defaultColumns()
    {
        return [
            'id',
            '_actions'
        ];
    }

    /**
     * @return array список колонок для грида.
     */
    public function columns()
    {
        return [];
    }

    /**
     * Вернет настройки для одной колонки.
     *
     * @param       $column
     * @param array $params
     *
     * @return object
     */
    public function getColumn($column, $params = [])
    {
        switch ($column) {
            case '_actions':
                return (object)array_merge(
                    [
                        'column'   => $column,
                        'template' => '_delete_',
                        'buttons'  => [
                            'delete' => '<a href="#"
                                data-ajax-action="delete" data-ajax-confirm="Вы действительно хотите удалить эту запись?"
                                data-ajax-url="/admin/' . RouteInfo::controller() . '/delete/{$data->id}/"
                                data-ajax-target="tr[data-id={$data->id}]"
                                ><i class="glyphicon glyphicon-trash text-danger"></i></a>'
                        ]
                    ], $params
                );

            default:
                return (object)array_merge(
                    $params, [
                        'column' => $column,
                        'params' => $params
                    ]
                );
        }
    }

    /**
     * Вернет названия колонок.
     *
     * @return mixed
     */
    public function getColumnsNames()
    {
        return \Cache::remember('getColumnsNames.' . $this->table, 120, function () {
            return \Schema::getColumnListing($this->table);
        });
    }

    /**
     * @return array вернет названия полей по колонкам.
     */
    public function attributeLabels()
    {
        //  TODO: generate labels by columns.
        return [];
    }

    /**
     * Вернет название колонки.
     *
     * @param $name
     *
     * @return mixed
     */
    public function getAttributeLabel($name)
    {
        $labels = $this->attributeLabels();
        return isset($labels[$name]) ? $labels[$name] : $name;
    }

    /**
     * Returns the first error of the specified attribute.
     *
     * @param string $attribute attribute name.
     *
     * @return string the error message. Null is returned if no error.
     * @see getErrors()
     * @see getFirstErrors()
     */
    public function getFirstError($attribute)
    {
        if (isset($this->_errors[$attribute])) {
            return $this->_errors[$attribute];
        }
        return null;
    }

    /**
     * Returns the first error of every attribute in the model.
     * @return array the first errors. The array keys are the attribute names, and the array
     * values are the corresponding error messages. An empty array will be returned if there is no error.
     * @see getErrors()
     * @see getFirstError()
     */
    public function getFirstErrors()
    {
        if (empty($this->_errors)) {
            return [];
        } else {
            $errors = [];
            foreach ($this->_errors as $name => $es) {
                if (!empty($es)) {
                    $errors[$name] = reset($es);
                    $errors[$name] = reset($errors[$name]);
                }
            }
            return $errors;
        }
    }

    /**
     * Adds a new error to the specified attribute.
     *
     * @param string $attribute attribute name
     * @param string $error     new error message
     */
    public function addError($attribute, $error = '')
    {
        $this->_errors[$attribute] = $error;
    }

    /**
     * Вернет список ошибок валидации модели.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * Вернет ошибку.
     *
     * @return array
     */
    public function getError($attribute)
    {
        return isset($this->_errors[$attribute]) ? $this->_errors[$attribute] : '';
    }

    /**
     * Вернет ошибку.
     *
     * @return array
     */
    public function hasError($attribute)
    {
        return isset($this->_errors[$attribute]);
    }

    /**
     * @return boolean вернет true, если в модели есть ошибки.
     */
    public function hasErrors()
    {
        return count($this->_errors) > 0;
    }

    /**
     * Установит значения аттрибутов модели.
     *
     * @param array $attributes
     */
    public function setAttributes($attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->$key = empty($value) ? null : $value;
        }
    }

    /**
     * Загрузка параметров.
     *
     * @param array $params
     * @param boolean $validation
     *
     * @return bool
     */
    public function load($params, $validation = true)
    {
        if (!is_array($params) || !isset($params[$this->formName()])) {
            return false;
        }

        $this->setAttributes($params[$this->formName()]);

        return $validation ? $this->validate() : true;
    }

    /**
     * @return bool вернет true, если успешно действие до сохранения модели.
     */
    public function beforeSave()
    {
        return true;
    }

    /**
     * Действия после соранения модели.
     */
    public function afterSave()
    {
    }

    /**
     * @inheritdoc
     */
    public function save(array $options = [])
    {
        if (!$this->validate() || !$this->beforeSave() || !parent::save($options)) {
            return false;
        }

        $this->afterSave();

        return true;
    }

    /**
     * Метод выполняющийся до основной валидации модели.
     *
     * @return bool основная валидация будет выполнена, если вернет true.
     */
    public function beforeValidate()
    {
        return true;
    }

    /**
     * Метод выполняющийся после валидации модели.
     */
    public function afterValidate()
    {
    }

    /**
     * Проверит модель на ошибки. @see {http://laravel.ru/docs/v4/validation}
     *
     * @return bool вернет true в случае успешной валидации всех правил.
     */
    public function validate()
    {
        if (!$this->beforeValidate()) {
            return false;
        }

        //  Правила валидации для laravel.
        $laravelRules = [];
        foreach ($this->rules() as $item) {
            $attributes = is_array($item[0]) ? $item[0] : [$item[0]];
            $rules = $item[1];

            foreach ($attributes as $attribute) {
                if (!isset($laravelRules[$attribute])) {
                    $laravelRules[$attribute] = [];
                }

                //  Если правило уникально, тогда добавляем id в конец для его игнорирования.
                $_rules = explode('|', $rules);
                foreach ($_rules as &$rule) {
                    if (substr_count($rule, 'unique') && isset($this->id)) {
                        $rule .= ',' . $this->id;
                    }
                }

                //  Если это кастомный валидатор, проверяем его сразу.
                if (method_exists($this, $rules)) {
                    $this->$rules();
                } else {
                    $laravelRules[$attribute][] = implode('|', $_rules);
                }
            }
        }

        foreach ($laravelRules as &$rule) {
            $rule = implode('|', $rule);
        }

        //  Стандартные валидаторы.
        $validator = \Validator::make($this->getAttributes(), $laravelRules);
        $validator->setAttributeNames($this->attributeLabels());

        if ($validator->fails()) {
            $messages = $validator->messages()->getMessages();
            foreach ($messages as $attribute => $message) {
                $this->addError($attribute, is_array($message) ? reset($message) : $message);
            }

            return false;
        }

        $this->afterValidate();

        return !$this->hasErrors();
    }

    /**
     * Выполняется перед удалением записи.
     *
     * @return bool
     */
    public function beforeDelete()
    {
        return true;
    }

    /**
     * Выполняется после удаления записи.
     */
    public function afterDelete()
    {
    }

    /**
     * Удалит найденную запись.
     */
    public function delete()
    {
        if ($this->isNewRecord() || !$this->beforeDelete() || !parent::delete()) {
            return false;
        }

        $this->afterDelete();
        return true;
    }

    /**
     * Перезагрузка модели.
     */
    public function reload()
    {
        //  FIXME: Не помню вообще для чего это делал. Вообще тут создается новая модель и у нее по определению все пусто.
        $instance = new static;
        /*$instance = $instance->newQuery()->find($this->{$this->primaryKey});
        $this->attributes = isset($instance->attributes) ? $instance->attributes : [];
        $this->original = $instance->original;*/

        return $instance;
    }

    /**
     * Get the primary key value for a save query.
     * NOTE: Змена стандартного метода для работы с множеством первичных ключей.
     *
     * @return mixed
     */
    protected function getKeyForSaveQuery($name = null)
    {
        $keyName = $this->getKeyName();
        $key = isset($name) ? $name : $keyName;
        if (isset($this->original[$key])) {
            return $this->original[$key];
        }
        return $this->getAttribute($key);
    }

    /**
     * Set the keys for a save update query.
     * NOTE: Змена стандартного метода для работы с множеством первичных ключей.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function setKeysForSaveQuery(\Illuminate\Database\Eloquent\Builder $query)
    {
        $keyName = $this->getKeyName();
        if (is_array($keyName)) {
            foreach ($keyName as $name) {
                $query->where($name, '=', $this->getKeyForSaveQuery($name));
            }
        } else {
            $query->where($keyName, '=', $this->getKeyForSaveQuery());
        }

        return $query;
    }

    /**
     * Perform the actual delete query on this model instance.
     * NOTE: Змена стандартного метода для работы с множеством первичных ключей.
     *
     * @return void
     */
    protected function performDeleteOnModel()
    {
        $this->setKeysForSaveQuery($this->newQuery())->delete();
    }

    /**
     * Расширяет валидатор для данной модели
     * @param $rules - массив правил, которые будут добавлены в валидацию
     */
    public function validatorExtend($rules)
    {
        is_array($rules) OR $rules = [$rules];
        foreach ($rules as $rule)
            \Validator::extend($rule, '\helpers\ValidatorHelper@' . $rule);
    }
}