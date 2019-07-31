<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace models;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

/**
 * ActiveRecord таблицы `products_opinions`
 *
 * @package models
 */
class ProductsOpinions extends \components\ActiveRecord
{
    use SoftDeletingTrait;

    /**
     * @const string сценарий, когда пользователь сам оставляет отзыв на сайте.
     */
    const SCENARIO_CREATE_USER_OPINION = 'createUserOpinion';

    /**
     * @var string название таблицы.
     */
    protected $table = 'products_opinions';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return array
     */
    public function getDates()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product' => 'Товар',
            'user_fullname' => 'Ваше имя',
            'user_advantages' => 'Достоинства',
            'user_disadvantages' => 'Недостатки',
            'user_comment' => 'Комментарий',
            'rating' => 'Оценка',
            'is_checked' => 'Статус'
        ];
    }

    /**
     * @inheritdoc
     */
    public function defaultColumns()
    {
        return [
            'product_data',
            'user_fullname',
            'user_advantages',
            'user_disadvantages',
            'user_comment',
            'rating',
            'is_checked',
            '_actions'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'rating'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave()
    {
        if ($this->scenario == self::SCENARIO_CREATE_USER_OPINION) {
            $this->date_create = date('Y-m-d H:i:s');
        }

        return parent::beforeSave();
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
                case 'product_data':
                    $result[$column] = $this->getColumn(
                        $column, [
                            'value' => '{$data->columnProduct()}',
                            //'filter' => Html::activeTextInput($this, $column, ['class' => 'form-control']),
                            'class' => 'col-xs-2'
                        ]
                    );
                    break;
                case 'user_fullname':
                case 'user_advantages':
                case 'user_disadvantages':
                case 'user_comment':
                    $result[$column] = $this->getColumn(
                        $column, [
                            'class' => 'col-xs-2'
                        ]
                    );
                    break;
                case 'rating':
                    $result[$column] = $this->getColumn(
                        $column, [
                            'class' => 'col-xs-1'
                        ]
                    );
                    break;
                case 'is_checked':
                    $result[$column] = $this->getColumn(
                        $column, [
                            'value' => '{$data->columnIsChecked()}',
                            //'filter' => Html::activeTextInput($this, $column, ['class' => 'form-control']),
                            'class' => 'col-xs-1'
                        ]
                    );
                    break;
                case '_actions':
                    $result[$column] = $this->getColumn(
                        $column, [
                            'buttons' => [
                                'delete' => '<a href="#"
                                    data-ajax-action="delete" data-ajax-confirm="Вы действительно хотите удалить эту запись?"
                                    data-ajax-url="/admin/productsopinions/delete/{$data->id}/"
                                    data-ajax-target="#opProducts tr[data-id={$data->id}]"
                                    ><i class="glyphicon glyphicon-trash text-danger"></i></a>'
                            ]
                        ]
                    );
                    break;
            }
        }

        return $result;
    }

    /**
     * Вернет текст в колонке "is_cheched".
     * @return string
     */
    public function columnIsChecked()
    {
        return $this->is_checked
            ? '<span class="text-muted">Подтвержден</span>'
            : '<a href="#" class="btn btn-sm btn-success"
                    data-ajax-url="/admin/productsopinions/confirm/' . $this->id . '/"
                    data-ajax-action="confirm" data-ajax-target="li[data-id=' . $this->id . ']">Подтвердить</a>';
    }

    /**
     * Вернет текст в колонке "product".
     * @return string
     */
    public function columnProduct()
    {
        $product = $this->product()->first();
        return $product
            ? '<a href="/catalog/' . $product->categories->alias . '/' . $product->alias . '/" target="_blank">' . $product->name . '</a>'
            : 'Товар не найден (' . $this->product_id . ')';
    }

    /**
     * Связь с таблицей `products`
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo('\models\Products', 'product_id')->remember(120);
    }
}