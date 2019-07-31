<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace models;

use components\ActiveRecord;
use helpers\ArrayHelper;
use helpers\Html;
use Illuminate\Database\Eloquent\Builder;

/**
 * ActiveRecord таблицы `products`.
 *
 * @package models
 */
class Orders extends \components\ActiveRecord
{
    /**
     * @var string название таблицы.
     */
    protected $table = 'orders';

    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @var array заказанные товары (для сохранения).
     */
    public $orderItems = [];

    /**
     * @const string статус заказа "новый"
     */
    const STATUS_NEW = 'N';

    /**
     * @const string статус заказа "принят"
     */
    const STATUS_ACCEPTED = 'A';

    /**
     * @const string статус заказа "выполнен"
     */
    const STATUS_COMPLETED = 'C';

    /**
     * @const string статус заказа "удален"
     */
    const STATUS_DELETED = 'D';

    /**
     * @const string статус заказа "не согласован"
     */
    const STATUS_NOTAGREE = 'G';

    /**
     * @const string статус заказа "согласован"
     */
    const STATUS_AGREE = 'R';

    /**
     * @const string статус заказа "к обеспечению"
     */
    const STATUS_ENSURE = 'E';

    /**
     * @const string статус заказа "к отгрузке"
     */
    const STATUS_SHIPMENT = 'S';

    /**
     * @const string статус заказа "закрыт"
     */
    const STATUS_CLOSED = 'L';

    /**
     * @var array доступные статусы заказов.
     */
    public $statuses
        = [
            self::STATUS_NEW => 'Новый',
            self::STATUS_ACCEPTED => 'Принят',
            self::STATUS_COMPLETED => 'Выполнен',
            self::STATUS_DELETED => 'Удален',
            self::STATUS_NOTAGREE => 'Не согласован',
            self::STATUS_AGREE => 'Согласован',
            self::STATUS_ENSURE => 'К обеспечению',
            self::STATUS_SHIPMENT => 'К отгрузке',
            self::STATUS_CLOSED => 'Закрыт'
        ];

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cost' => 'Общая стоимость',
            'discount' => 'Скидка',
            'status' => 'Статус заказа',
            'code' => 'Код для просмотра статуса заказа',
            'date_create' => 'Дата заказа',
            'date_update' => 'Обновление',
            'user_name' => 'ФИО',
            'email' => 'E-mail',
            'phone' => 'Телефон',
            'address' => 'Адрес',
            'actual_address' => 'Фактический адрес',
            'note' => 'Примечание (не видно покупателю)',
            'comments' => 'Комментарий к заказу',
            'order' => 'Заказ',
            'manager' => 'Менеджер',
            'city_id' => 'Город',
            'date_range' => 'Промежуток',
            'is_from_direct' => 'Из рекламы',
            'is_paid' => 'Оплачен',
            'direct_campaign' => 'Директ кампания',
            'direct_ad_id' => 'Директ объявление'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['user_name', 'required|min:5'],
            ['phone', 'required|max:10000000000|min:1000000000|numeric'/*, 'message' => 'Неверный номер телефона'*/],
            ['email', 'max:64|email'],
            ['discount', 'integer'],
            ['cost', 'required|min:1|numeric']
        ];
    }

    /**
     * @inheritdoc
     */
    public function defaultColumns()
    {
        $user = \Sentry::getUser();
        $groups = $user->getGroups();

        return $groups[0]->alias == 'managers'
            ? [
                'date_create',
                'order',
                'cost',
                'is_paid'
            ]
            : [
                'date_create',
                'order',
                'status',
                'city_id',
                'date_range',
                'cost',
                'is_from_direct',
                'is_paid'
            ];
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
                case 'id':
                    $result[$column] = $this->getColumn(
                        $column, [
                            //'filter' => Html::textInput($column, '', ['class' => 'form-control']),
                            'filter' => Html::activeTextInput($this, $column, ['class' => 'form-control']),
                            'class' => 'col-xs-1'
                        ]
                    );
                    break;
                case 'date_create':
                    $result[$column] = $this->getColumn(
                        $column, [
                            'value' => '{$data->$column}',
                            //'filter' => Html::activeTextInput($this, $column, ['class' => 'form-control']),
                            'class' => 'col-xs-1'
                        ]
                    );
                    break;
                case 'cost':
                    $result[$column] = $this->getColumn(
                        $column, [
                            'value' => '{$data->$column} руб.',
                            //'filter' => Html::activeTextInput($this, $column, ['class' => 'form-control']),
                            'class' => 'col-xs-2'
                        ]
                    );
                    break;
                case 'status':
                    $result[$column] = $this->getColumn(
                        $column, [
                            'value' => '{$data->getStatus()}',
                            'filter' => Html::activeDropDownList(
                                $this, $column, $this->statuses,
                                ['class' => 'form-control', 'prompt' => '']
                            ),
                            'class' => 'col-xs-1'
                        ]
                    );
                    break;
                case 'is_paid':
                    $result[$column] = $this->getColumn(
                        $column, [
                            'value' => '{$data->columnIsPaid()}',
                            'class' => 'col-xs-1'
                        ]
                    );
                    break;
                case 'city_id':
                    $result[$column] = $this->getColumn(
                        $column, [
                            'value' => '{$data->getCityName()}',
                            'filter' => Html::activeDropDownList(
                                $this, $column, ArrayHelper::map(Cities::all(), 'id', 'name'),
                                ['class' => 'form-control', 'prompt' => '']
                            ),
                            'class' => 'col-xs-1'
                        ]
                    );
                    break;
                case 'date_range':
                    $result[$column] = $this->getColumn(
                        $column, [
                            'value' => '{$data->date_create}',
                            'filter' => Html::activeTextInput(
                                $this, $column, ['class' => 'form-control daterangepicker']
                            ),
                            'class' => 'col-xs-3'
                        ]
                    );
                    break;
                /*case 'manager':
                    $result[$column] = $this->getColumn(
                        $column, [
                            'value' => '{$data->manager->first_name}',
                            'class' => 'col-xs-3'
                        ]
                    );
                    break;*/
                case 'order':
                    $result[$column] = $this->getColumn(
                        $column, [
                            'value' => '<a href="/admin/orders/update/{$data->id}/">Заказ №{$data->id}</a> {$data->user_name}<br/><span class="label label-warning">{$data->note}</span>',
                            'filter' => Html::activeTextInput($this, $column, ['class' => 'form-control']),
                            'class' => 'col-xs-6'
                        ]
                    );
                    break;
                case 'is_from_direct':
                    $result[$column] = $this->getColumn(
                        $column, [
                            'value' => '{$data->is_from_direct}',
                            'filter' => Html::activeDropDownList(
                                $this, $column, ['D' => 'Директ', 'M' => 'Маркет', 'A' => 'Adwords'],
                                ['class' => 'form-control', 'prompt' => '']
                            ),
                            'class' => 'col-xs-2'
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
     * Вернет весь товар связанный с этим заказом.
     */
    public function items()
    {
        return $this->belongsToMany(
            '\models\Products',
            'order_items', 'order_id', 'product_id'
        )
            ->select('id', 'name', 'amount', 'id_1c', 'order_items.cost','order_items.vendor_id')
            ->orderBy('name', 'desc');
    }

    /**
     * Вернет город, где будет заказ.
     */
    public function city()
    {
        return $this->hasOne(
            '\models\Cities',
            'id',
            'city_id'
        );
    }

    /**
     * Вернет менеджера заказа.
     */
    public function manager()
    {
        return $this->belongsTo(
            '\models\Users',
            'manager_id'
        );
    }

    /**
     * Вернет транзакцию.
     */
    public function transaction()
    {
        return $this->hasOne(
            Transactions::class,
            'order_id'
        );
    }

    /**
     * Вернет статус заказа.
     *
     * @return string
     */
    public function getStatus()
    {
        return isset($this->statuses[$this->status]) ? $this->statuses[$this->status] : '##';
    }

    /**
     * Колонка вернет оплачен заказ или нет.
     */
    public function columnIsPaid($returnIfTrue = '<i class="glyphicon glyphicon-usd"></i>', $returnIfFalse = '')
    {
        return $this->transaction && $this->transaction->status == Transactions::STATUS_SUCCESS ? $returnIfTrue : $returnIfFalse;
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        //  Убираем лишнее с номера телефона.
        if ($this->phone[0] == '8' && strlen($this->phone) > 10) {
            $this->phone = ltrim($this->phone, '8');
        }
        $this->phone = str_replace('+7', '', $this->phone);

        return parent::beforeValidate();
    }

    /**
     * @inheritdoc
     */
    public function beforeSave()
    {
        //  Запишем код для отслеживания состояния заказа.
        if ($this->isNewRecord()) {
            $this->code = md5($this->id . time());
        }

        return parent::beforeSave();
    }

    /**
     * @inheritdoc
     */
    public function afterSave()
    {
        //  Сохраним позиции.
        foreach ($this->orderItems as $key => $value) {
            if (isset($value['vendor'])) {
                foreach ($value['vendor'] as $vendorId => $item) {
                    $model = new OrderItems;
                    $model->setAttributes(
                        [
                            'order_id' => $this->id,
                            'product_id' => $key,
                            'amount' => $item['count'],
                            'cost' => $item['costMarkUp'],
                            'status' => 'N',
                            'vendor_id' => $vendorId,
                            'delivery'=>$item['delivery'],
                        ]
                    );
                    $model->save();
                }
            }
            if (isset($value['count'])) {
                $model = new OrderItems;
                $model->setAttributes(
                    [
                        'order_id' => $this->id,
                        'product_id' => $key,
                        'amount' => $value['count'],
                        'cost' => $value['model']->getBalances(\models\Cities::getCurrentCity()->id)->cost,
                        'status' => 'N',
                    ]
                );
                $model->save();
            }
        }

        parent::afterSave();
    }

    /**
     * Вернет название города.
     *
     * @return string
     */
    public function getCityName()
    {
        return $this->city ? $this->city->name : '';
    }
}