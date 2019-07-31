<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace models;
use modules\deliverycalc\models\CitiesStatesDelivery;

/**
 * ActiveRecord таблицы `users`
 *
 * @package models
 */
class Cities extends \components\ActiveRecord
{
    /**
     * @var string название таблицы.
     */
    protected $table = 'cities';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @const город по умолчанию.
     */
    const CITY_BY_DEFAULT = 1;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'required|unique:cities,name'],
            ['alias', 'required|unique:cities,alias'],
            [['address', 'phones', 'email', 'email_manager', 'phone_manager', 'work_begin', 'work_end'], 'required'],
			[['work_begin', 'work_end', 'default_manager'], 'integer'],
			['enable_acquiring', 'boolean']
        ];
    }

    /**
     * Вернет true, если менеджеры еще работают т.е. рабочее время.
     */
    public static function isWorkTime()
    {
        $city = Cities::where('id', \Cookie::get('city_id'))->remember(120)->first();
        if (!isset($city)) {
            return false;
        }

        if (!$city->work_begin) {
            return false;
        }

        return (int)date('H') >= $city->work_begin && (int)date('H') <= $city->work_end;
    }

    /**
	 * @return self|bool вернет текущий город.
     */
    public static function getCurrentCity()
    {
        return \Cache::remember('city.' . \Cookie::get('city_id', self::CITY_BY_DEFAULT), 120, function () {
            $city = Cities::find(\Cookie::get('city_id')) OR $city = Cities::find(self::CITY_BY_DEFAULT);
            return $city;
        });
    }

    /**
     * @return mixed
     */
    public function stateCities()
    {
        return $this->hasMany(CitiesStatesDelivery::className(), 'city_id', 'id')->orderBy('name');
    }

	/**
	 * Вернет все шины в городе.
	 * @return mixed
	 */
	public function getVisibleProducts()
	{
		return Products::with('balance')
			->join(
				'products_images_relation',
				function ($join) {
					$join->on('products_images_relation.product_id', '=', 'products.id');
				}
			)
			->join(
				'products_balances',
				function ($join) {
					$join->on('products_balances.product_id', '=', 'products.id');
				}
			)
			->where('is_visible', 't')
			->where('products_balances.cost', '>', 0)
			->where('products_balances.balance', '>', 0)
			->where('products_balances.city_id', '=', $this->id)
			->select([
				'products.*',
                'products_balances.*'
			])
            ->distinct()
            ->remember(120)
			->get();
	}

    /**
     * @inheritdoc
     */
    public function defaultColumns()
    {
        return [
            'name',
            'address',
            'phones',
            'email',
			'enable_acquiring',
			'enable_consult',
            'work_begin',
            'work_end',
            'default_manager',
            'email_manager',
            'phone_manager',
            'address_storage',
            '_actions'
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave()
    {
		$this->is_visible = $this->is_visible ? 't' : 'f';
		$this->enable_acquiring = $this->enable_acquiring ? 't' : 'f';

		if ($this->isNewRecord()) {
			$consult = new OnlineConsult;
			$consult->city_id = $this->id;
			$consult->city_key = 'enter code here';
			$consult->save();
		}

        return parent::beforeSave();
    }

	/**
	 * Связь с онлайн консультантом.
	 */
	public function consult()
	{
		return $this->hasOne(OnlineConsult::className(), 'city_id', 'id');
	}

    /**
     * @return mixed вернет все доступные города.
     */
    public static function getAll()
    {
        return \Cache::remember('cities_all', 120, function () {
            return self::where('is_visible', '=', 't')->orderBy('name')->get();
        });
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
						'column' => $column,
						'template' => '_delete_',
						'buttons' => [
							'delete' => '<a href="#"
                                data-ajax-action="delete" data-ajax-confirm="Вы действительно хотите удалить эту запись?"
                                data-ajax-url="/admin/hdbkcities/delete/{$data->id}/"
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
     * @inheritdoc
     */
    public function columns()
    {
        $columns = $this->defaultColumns();
        $result = [];

        foreach ($columns as $column) {
            switch ($column) {
                case 'name':
                    $result[$column] = $this->getColumn(
                        $column, [
                            'value' => '<a href="/admin/hdbkcities/update/{$data->id}">{$data->$column}</a>'
                        ]
                    );
                    break;
                case 'address':
                case 'phones':
                case 'email':
                case 'work_begin':
                case 'work_end':
                case 'email_manager':
                case 'phone_manager':
                    $result[$column] = $this->getColumn($column);
                    break;
				case 'enable_acquiring':
					$result[$column] = $this->getColumn(
						$column, [
							'value' => '{$data->getAcquiringState()}'
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
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'            => 'ID',
            'name'          => 'Название',
            'alias'         => 'Псевдоним',
            'address'       => 'Адрес',
            'phones'        => 'Телефоны',
            'email'         => 'E-mail',
            'work_begin'    => 'Начало работы',
            'work_end'      => 'Конец работы',
            'email_manager' => 'E-mail менеджера',
            'phone_manager' => 'Телефоны менеджеров',
			'enable_acquiring' => 'Эквайринг',
            'is_visible' => 'Видимый',
            'online_pay_delivery' => 'Эквайринг при доставке',
            'default_manager' => 'Персональный менеджер',
            'address_storage' => 'Адрес склада',
        ];
    }

	/**
	 * Вернет состояние эквайринга для конкретного города.
	 * @return string
	 */
	public function getAcquiringState()
	{
		return $this->enable_acquiring ? '<span class="label label-success">Включен</span>' : '<span class="label label-danger">Отключен</span>';
	}
}