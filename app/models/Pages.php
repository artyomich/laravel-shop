<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace models;

use components\ActiveRecord;
use components\UploadedFile;

/**
 * ActiveRecord таблицы `pages`
 *
 * @package models
 */
class Pages extends \components\ActiveRecord
{
    /**
     * @var string название таблицы.
     */
    protected $table = 'pages';

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'body'], 'required|min:5'],
            ['alias', 'required|unique:pages,alias']
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
            'id'               => 'ID',
            'category_id'      => 'Категория',
            'name'             => 'Название',
            'alias'            => 'Псевдоним',
            'description'      => 'Описание',
            'body'             => 'Содержание',
            'meta_title'       => 'Заголовок (meta)',
            'meta_description' => 'Описание (meta)',
            'meta_keywords'    => 'Колючевые слова (meta)',
            'date_create'      => 'Дата создания',
            'date_update'      => 'Дата удаления',
            'is_visible'       => 'Активна'
        ];
    }

    /**
     * Связь с таблицей `images`
     */
    public function image()
    {
        return $this->belongsTo('\models\Images', 'image_id');
    }

    /**
     * Связь с таблицей `images`
     */
    public function photos()
    {
        return $this->belongsToMany('\models\Images', 'pages_images_relation', 'page_id', 'image_id');
    }

    /**
     * Связь с таблицей `pages_categories`.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function categories()
    {
        return $this->belongsTo('\models\PagesCategories', 'category_id')->remember(120);
    }

    /**
     * Поиск статей по псевдониму категории
     *
     * @param $name
     *
     * @return mixed
     */
    public static function findByCategoryAlias($name)
    {
        $category = PagesCategories::where('alias', '=', $name)->remember(120)->first();
        return self::where('category_id', '=', $category->id)->orderBy('date_create', 'desc')->remember(120)->get();
    }

    /**
     * Сохранени изображений.
     */
    public function beforeSave()
    {
        $_get = \Input::get($this->formName());

        //  Если файл не пришел, то удалим его как бы.
        if (!isset($_get['mainImage']) && $this->image_id) {
            Images::find($this->image_id)->delete();
            $this->image_id = null;
        } elseif (\Input::hasFile('mainImage')) {
            $mainImage = new UploadedFile($_FILES['mainImage']['tmp_name'], $_FILES['mainImage']['name'], null, null, null, true);
            if (isset($mainImage)) {
                //  Удаляем сталое узображение.
                if ($this->image_id) {
                    //  TODO: try catch
                    Images::find($this->image_id)->delete();
                }

                //  Загружаем новое изображение.
                $image = new Images;
                $image->scenario = Images::SCENARIO_PAGES;
                $image->file = $mainImage;
                $image->save();

                $this->image_id = $image->id;
            }
        }

        //  FIXME: сделать, чтобы левые переменные не попадали в запрос.
        unset($this->mainImage);
        unset($this->uploaded);

        return parent::beforeSave();
    }

    /**
     * @inheritdoc
     */
    public function afterSave()
    {
        //  Загрузка множества изображений.

        //  Если не пришли какие то старые фото, то удалим их.
        $uploaded = \Input::get($this->formName());
        $uploaded = isset($uploaded['uploaded']) ? $uploaded['uploaded'] : [];

        //  Получим все id изображений этого товара.
        $ids = [];
        foreach ($this->photos as $image) {
            $ids[] = $image->id;
        }

        //  Получим разницу.
        $diff = array_diff($ids, $uploaded);

        //  Идатим все различные ID т.к. они не пришли.
        foreach ($diff as $id) {
            Images::find($id)->delete();
        }

        //  Если есть новые изображения.
        $files = \Input::file('Images');

        foreach ($files as $file) {
            $image = new Images;
            $image->file = $file;
            $image->productId = $this->id;
            $image->scenario = Images::SCENARIO_PAGES;
            $image->save();
        }

        parent::afterSave();
    }

    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        if ($this->image_id) {
            //  TODO: try catch
            Images::find($this->image_id)->delete();
        }

        //  TODO: Удалить фото для этой статьи.

        parent::afterDelete();
    }
}