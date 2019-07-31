<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace models;

use helpers\RouteInfo;

/**
 * ActiveRecord таблицы `menus`
 *
 * @package models
 */
class Menus extends \components\ActiveRecord
{
    /**
     * @var string название таблицы.
     */
    protected $table = 'menus';

    /**
     * @var array загруженные изображения.
     */
    public $uploaded = [];

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
            [['name', 'alias'], 'required'],
            [['name'], 'max:64'],
            [['alias'], 'max:64']
        ];
    }

    /**
     * @inheritdoc
     */
    public function defaultColumns()
    {
        return [
            'name',
            'menu_id',
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
                            'value' => '<a href="/admin/menus/update/{$data->id}">{$data->$column}</a>',
                            //'filter' => Html::activeTextInput($this, $column, ['class' => 'form-control']),
                            'class' => 'col-xs-8'
                        ]
                    );
                    break;
                case 'menu_id':
                    $result[$column] = $this->getColumn(
                        $column, [
                            'value' => '{$data->getMenuName()}',
                            'style' => 'width:100px'
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
     * @return string вернет название меню, к которому принадлежит эта запись или пустую строку.
     */
    public function getMenuName()
    {
        $relation = $this->belongsTo('\models\MenusTypes', 'menu_id')->get();
        return isset($relation[0]) ? $relation[0]->name : '';
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function menu()
    {
        return $this->belongsTo(MenusTypes::className(), 'menu_id');
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'        => 'ID',
            'name'      => 'Название',
            'parent_id' => 'Родитель',
            'menu_id'   => 'Меню',
            'alias'     => 'Псевдоним'
        ];
    }

    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        //  Удаляем потомков, если есть.
        $childs = Menus::where('parent_id', $this->id)->get();
        foreach ($childs as $child) {
            $child->delete();
        }
    }
}