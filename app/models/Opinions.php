<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace models;

use helpers\RouteInfo;

/**
 * ActiveRecord таблицы `opinions`
 *
 * @package models
 */
class Opinions extends \components\ActiveRecord
{
    /**
     * @var string название таблицы.
     */
    protected $table = 'opinions';

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
            ['name', 'required|min:5']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'name'       => 'Название',
            'is_visible' => 'Видимый'
        ];
    }

    /**
     * Связь с таблицей `images`
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function image()
    {
        return $this->belongsTo('\models\Images', 'image_id');
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
                case 'id':
                    $result[$column] = $this->getColumn(
                        $column, [
                            //'filter' => Html::textInput($column, '', ['style'=>'width:50px;', 'class' => 'form-control']),
                            'class' => 'col-xs-1'
                        ]
                    );
                    break;
                case 'name':
                    $result[$column] = $this->getColumn(
                        $column, [
                            'value' => '<a href="/admin/opinions/update/{$data->id}">{$data->$column}</a>',
                            //'filter' => Html::activeTextInput($this, $column, ['class' => 'form-control']),
                            'class' => 'col-xs-6'
                        ]
                    );
                    break;
                case '_actions':
                    $result[$column] = $this->getColumn(
                        $column, [
                            'buttons' => [
                                'delete' => '<a href="#"
                                    data-ajax-action="delete" data-ajax-confirm="Вы действительно хотите удалить эту запись?"
                                    data-ajax-url="/admin/' . RouteInfo::controller() . '/delete/{$data->id}/"
                                    data-ajax-target="li[data-id={$data->id}]"
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
     * @inheritdoc
     */
    public function beforeValidate()
    {
        $file = \Input::file('image');
        if (!isset($file) && $this->isNewRecord()) {
            $this->addError('image', 'Вы не выбрали изображение');
            return false;
        }

        return parent::beforeValidate();
    }

    /**
     * Сохранени изображений.
     */
    public function beforeSave()
    {
        $_get = \Input::get($this->formName());
        $mainImage = \Input::file('image');

        $this->is_visible = (bool)$this->is_visible;

        if (isset($mainImage)) {
            //  Удаляем старое изображение.
            if ($this->image_id && isset($this->image)) {
                //  TODO: try catch
                Images::find($this->image_id)->delete();
            }

            //  Загружаем новое изображение.
            $image = new Images;
            $image->scenario = Images::SCENARIO_OPINIONS;
            $image->file = $mainImage;
            if (!$image->save()) {
                dd($image->getErrors());
            }

            $this->image_id = $image->id;
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
     * Вернет название города.
     *
     * @return string
     */
    public function getCityName()
    {
        return $this->city ? $this->city->name : '';
    }
}