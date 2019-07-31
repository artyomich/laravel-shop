<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace models;

use components\ActiveRecord;
use components\UploadedFile;
use helpers\ArrayHelper;
use helpers\Html;
use Illuminate\Database\Eloquent\Builder;

/**
 * ActiveRecord таблицы `products`.
 *
 * @package models
 */
class Employers extends \components\ActiveRecord
{
    /**
     * @var string название таблицы.
     */
    protected $table = 'employers';

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
            'id'       => 'ID',
            'name'     => 'Имя',
            'city_id'  => 'Город',
            'email'    => 'E-mail',
            'photo_id' => 'Фото',
            'icq'      => 'ICQ',
            'phone'    => 'Номер телефона'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'required|min:5'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function defaultColumns()
    {
        return [
            'id_1c',
            'name',
            'city_id',
            '_actions'
        ];
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
                                data-ajax-url="/admin/hdbkemployers/delete/{$data->id}/"
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
                            'value'  => '<a href="/admin/hdbkemployers/update/{$data->id}">{$data->$column}</a>',
                            'filter' => Html::activeTextInput($this, $column, ['class' => 'form-control']),
                            'class'  => 'col-xs-8'
                        ]
                    );
                    break;
                case 'city_id':
                    $result[$column] = $this->getColumn(
                        $column, [
                            'value'  => '{$data->city->name}',
                            'filter' => Html::activeDropDownList(
                                $this, $column,
                                ArrayHelper::map(Cities::all(), 'id', 'name'),
                                ['class' => 'form-control', 'prompt' => '']
                            ),
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
    public function beforeValidate()
    {
        $file = \Input::file('image');
        if (!isset($file) && $this->isNewRecord()) {
            $this->addError('image', 'Вы не выбрали изображение');
        }

        return parent::beforeValidate();
    }

    /**
     * Сохранени изображений.
     */
    public function beforeSave()
    {
        if (\Input::hasFile('image')) {
            $mainImage = new UploadedFile($_FILES['image']['tmp_name'], $_FILES['image']['name'], null, null, null, true);

            //  Удаляем старое изображение.
            if ($this->image_id && $this->image()->count()) {
                //  TODO: try catch
                Images::find($this->image_id)->delete();
            }

            //  Загружаем новое изображение.
            $image = new Images;
            $image->scenario = Images::SCENARIO_EMPLOYERS;
            $image->file = $mainImage;
            if (!$image->save()) {
                dd($image->getErrors());
            }

            $this->image_id = $image->id;
        }

        if ($this->isNewRecord()) {
            //  Сохраним сортировку для новой категории.
            $this->sorting = max(\DB::table($this->getTable())->max('id'), 1);
        }

        //  FIXME: сделать, чтобы левые переменные не попадали в запрос.
        unset($this->mainImage);
        unset($this->uploaded);

        return parent::beforeSave();
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

        parent::afterDelete();
    }

    /**
     * Связь с таблицей `cities`.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function city()
    {
        return $this->belongsTo('\models\Cities', 'city_id');
    }

    /**
     * Связь с таблицей `images`.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function image()
    {
        return $this->belongsTo('\models\Images', 'image_id');
    }
}