<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace models;

use components\BaseController;
use helpers\Html;
use helpers\ArrayHelper;

/**
 * ActiveRecord таблицы `users`
 *
 * @property string $password
 *
 * @package models
 */
class Users extends \components\ActiveRecord
{
    /**
     * @var string название таблицы.
     */
    protected $table = 'users';

    /**
     * @const
     */
    const CREATED_AT = 'created_at';

    /**
     * @const
     */
    const UPDATED_AT = 'updated_at';

    /**
     * @const
     */
    const ROLE_CLIENTS = 'clients';

    /**
     * @const
     */
    const ROLE_ADMINS = 'admins';

    /**
     * @const
     */
    const ROLE_MANAGERS = 'managers';

    /**
     * @const
     */
    const ROLE_SUPERVISOR = 'supervisor';

    /**
     * @const
     */
    const PASSWORD_MIN_LENGTH = 6;

    /**
     * @const
     */
    const TYPE_PHISICAL = 'physical';

    /**
     * @const
     */
    const TYPE_FIRM = 'firm';

    /**
     * @const
     */
    const TYPE_VENDOR = 'vendor';

    /**
     * @const
     */
    const TYPE_EMPLOYEE = 'employee';

    /**
     * @var bool флаг указывающий на установку нового пароля при сохранении пользователя.
     */
    public $isSetNewPassword = false;

    /**
     * @var array
     */
    public $groups = [];

    /**
     * @var array
     */
    public $types
        = [
            self::TYPE_PHISICAL => 'Физическое лицо',
            self::TYPE_FIRM => 'Юридическое лицо',
            self::TYPE_VENDOR => 'Поставщик',
            self::TYPE_EMPLOYEE => 'Сотрудник',
        ];


    /**
     * @inheritdoc
     */
    public function defaultColumns()
    {
        return [
            'email',
            'first_name',
            'last_name',
            'type',
            '_actions'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $this->validatorExtend(['inn', 'ogrn']);
        $rules = [
            ['email', 'required|email|unique:users,email'],
            [['first_name'], 'required'],
            [['phone'], 'required|max:255|string'],
        ];

        if ($this->is_firm) {
            $rules = array_merge($rules, [
                [['inn'], 'required|inn:users,inn'],
                [['ogrn'], 'ogrn'],
                [['kpp', 'bik'], 'required|digits:9'],
                [['rs', 'ks'], 'required|digits:20'],
                [['city_name'], 'required'],
            ]);
        }

        if ($this->isSetNewPassword || $this->isNewRecord()) {
            $rules[] = [['password'], 'validatePassword'];
        }

        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function columns()
    {
        $columns = $this->defaultColumns();
        $result = [];

        foreach ($columns as $column) {
            switch ($column) {
                case 'email':
                    $result[$column] = $this->getColumn(
                        $column, [
                            'value' => '<a href="/admin/users/update/{$data->id}" ' .
                                'data-target="#userModal" data-ajax-action="content" ' .
                                'data-ajax-modal="1">{$data->$column}</a>',
                            'filter' => Html::activeTextInput($this, $column, ['class' => 'form-control']),
                        ]
                    );
                    break;
                case 'last_name':
                case 'first_name':
                    $result[$column] = $this->getColumn(
                        $column, [
                            'filter' => Html::activeTextInput($this, $column, ['class' => 'form-control']),
                        ]
                    );
                    break;
                case 'type':
                    $result[$column] = $this->getColumn(
                        $column, [
                            'value' => '{$data->getTypesName()}',
                            'filter' => Html::activeDropDownList(
                                $this, 'type', $this->types,
                                ['class' => 'form-control', 'prompt' => '']
                            )
                        ]
                    );
                    break;
                case '_actions':
                    $result[$column] = $this->getColumn($column);
                    break;
            }
        }

        return $result;
    }

    /**
     * Получить тип пользователя при миграции.
     * @param $user
     * @return string
     */
    public static function getType($user)
    {
        if (in_array('Клиенты', $user->groups()->get()->lists('name')) || !is_null($user->is_firm) || !is_null($user->is_vendor)) {
            return is_null($user->is_firm) ? 'physical' : ($user->is_vendor ? 'vendor' : 'firm');
        } else {
            return 'employee';
        }
    }

    /**
     * Вывод названия типа пользователя.
     * @return string
     */
    public function getTypesName()
    {
        return isset($this->types[$this->type]) ? $this->types[$this->type] : '';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'E-mail',
            'first_name' => 'Имя',
            'last_name' => 'Фамилия',
            'city_id' => 'Город',
            'is_male' => 'Пол',
            'password' => 'Пароль',
            'firm' => 'Наименование организации',
            'inn' => 'ИНН',
            'ogrn' => 'ОГРН',
            'kpp' => 'КПП',
            'rs' => 'Р/с',
            'ks' => 'К/с',
            'bik' => 'БИК',
            'bank' => 'Банк',
            'phone' => 'Телефон',
            'address' => 'Адрес',
            'actual_address' => 'Фактический адрес',
            'city_name' => 'Город',
            'type' => 'Тип',
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        $this->isSetNewPassword = (bool)$this->isSetNewPassword;
        $this->is_male = (bool)$this->is_male;

        return parent::beforeValidate();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function groups()
    {
        return $this->belongsToMany(
            '\models\Groups',
            'users_groups', 'user_id', 'group_id'
        );
    }

    /**
     * Сохранит информацию о пользователе.
     * @return bool
     */
    public function saveUser()
    {
        if (!$this->validate()) {
            return false;
        }

        if ($this->isNewRecord()) {
            $user = \Sentry::createUser(
                array_merge(
                    $this->getAttributes(),
                    [
                        'city_id'   => Cities::CITY_BY_DEFAULT,
                        'activated' => true
                    ]
                )
            );
        } else {
            $user = \Sentry::findUserById($this->id);
            $attributes = array_except($this->getAttributes(), ['password', 'permissions', 'persist_code', 'form_firm']);
            $user->fill($attributes);

            if (!$user->save()) {
                return false;
            }

            //  Флаг на смену пароля.
            if ($this->isSetNewPassword) {
                $user = \Sentry::findUserById($this->id);
                if (!$user->attemptResetPassword($user->getResetPasswordCode(), $this->password)) {
                    $this->addError('password', 'Не удалось изменить пароль');
                    return false;
                }
            }
        }

        //  Добавление групп.
        UsersGroupsRelation::where('user_id', $user->id)->delete();
        foreach ($this->groups as $group) {
            $usersGroups = new UsersGroupsRelation;
            $usersGroups->user_id = $user->id;
            $usersGroups->group_id = Groups::firstByAttributes(['alias' => $group])->id;
            $usersGroups->save();
        }

        $this->afterSave();

        return true;
    }

    /**
     * @return bool
     */
    public function validatePassword()
    {
        $validator = \Validator::make(
            $this->attributes,
            ['password' => 'required|min:' . self::PASSWORD_MIN_LENGTH]
        );
        $validator->setAttributeNames($this->attributeLabels());

        if ($validator->fails()) {
            $errors = $validator->messages();
            foreach ($errors->getMessages() as $attribute => $messages) {
                foreach ($messages as $message) {
                    $this->addError($attribute, $message);
                }
            }

            return false;
        }

        return true;
    }

    /**
     * Возвращает CDEK пользователя по id.
     * @param integer $id
     * @return mixed
     */
    public static function getCdekUser($id)
    {
        try {
            $user = \Sentry::findUserById($id);
        } catch (\Exception $e) {
            return false;
        }

        return $user->cdek_id;
    }
}