<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace models;

use components\ActiveRecord;

/**
 * ActiveRecord таблицы `pages_categories`
 *
 * @package models
 */
class PagesCategories extends \components\ActiveRecord
{
    /**
     * @var string название таблицы.
     */
    protected $table = 'pages_categories';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'required'],
            ['alias', 'required|unique:pages_categories,alias']
        ];
    }

    /**
     * @inheritdoc
     */
    public function defaultColumns()
    {
        return [
            'name',
            '_actions'
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
                case 'name':
                    $result[$column] = $this->getColumn(
                        $column, [
                            'value' => '<a href="/admin/pages/update/{$data->id}">{$data->$column}</a>',
                            //'filter' => Html::activeTextInput($this, $column, ['class' => 'form-control']),
                            'class' => 'col-xs-10'
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
            'id'         => 'ID',
            'name'       => 'Название',
            'alias'      => 'Псевдоним',
            'is_visible' => 'Активна'
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave()
    {
        $this->is_visible = (bool)$this->is_visible;
        if ($this->isNewRecord()) {
            //  Сохраним сортировку для новой категории.
            $this->sorting = max(\DB::table($this->getTable())->max('id'), 1);
        }

        return parent::beforeSave();
    }

    /**
     * Связь с таблицей `pages`.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pages()
    {
        return $this->hasMany('\models\Pages', 'category_id')->remember(120);
    }
}