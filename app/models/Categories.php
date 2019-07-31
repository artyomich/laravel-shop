<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace models;

use components\ActiveRecord;

/**
 * ActiveRecord таблицы `categories`
 *
 * @package models
 */
class Categories extends \components\ActiveRecord
{
    /**
     * @var integer тип - шины.
     */
    const TYPE_TIRES = 1;

    /**
     * @var integer тип - диски.
     */
    const TYPE_DISKS = 2;

    /**
     * @var string название таблицы.
     */
    protected $table = 'categories';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'name'       => 'Название',
            'is_visible' => 'Активна',
            'alias' => 'Псевдоним',
            'description' => 'Описание',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'required|unique:categories,name'],
        ];
    }

    /**
     * Связь с таблицей `products`.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function products()
    {
        return $this->belongsToMany('\models\Products', 'products', 'id', 'category_id')->remember(120);
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
     * Вернет все категории предварительно отсортировав их.
     *
     * @return mixed
     */
    public static function getAll($id = null)
    {
        $query = static::orderBy('sorting', 'ASC');
        if (isset($id)) {
            $query->where('category_id', '=', $id);
        }
        return $query->remember(120)->get();
    }

    /**
     * Вернет категории по типу предварительно отсортировав их.
     *
     * @param $type
     * @return Categories[]
     */
    public static function getByType($type)
    {
        return self::orderBy('sorting', 'ASC')->where('type', $type)->get();
    }

    /**
     * Вернет данные категории по псевдониму.
     *
     * @param string $alias
     *
     * @return self
     */
    public static function getByAlias($alias)
    {
        $category = self::where('alias', $alias)->remember(120)->first();
        if (!isset($category)) {
            \App::abort(404, 'Категория не найдена');
        }
        return $category;
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        $products = Products::where('category_id', '=', $this->id)->get();
        foreach ($products as $product) {
            $product->delete();
        }

        return parent::beforeDelete();
    }
}