<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace models;

use components\ActiveRecord;
use components\UploadedFile;
use helpers\ArrayHelper;
use helpers\Html;
use helpers\RouteInfo;

/**
 * ActiveRecord таблицы `hdbk_filter`
 *
 * @package models
 */
class Filter extends \components\ActiveRecord
{
    /**
     * @var string название таблицы.
     */
    protected $table = 'hdbk_filter';

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
            [['alias', 'source'], 'required|min:3'],
            ['description', 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'alias' => 'Псевдоним',
            'source' => 'Источник (alias)',
            'description' => 'Текст описания',
        ];
    }

    /**
     * @inheritdoc
     */
    public function defaultColumns()
    {
        return [
            'alias',
            'source',
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
                case 'alias':
                    $result[$column] = $this->getColumn(
                        $column, [
                            'value' => '<a href="/admin/hdbkfilter/update/{$data->id}">{$data->$column}</a>',
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
                                    data-ajax-url="/admin/hdbkfilter/delete/{$data->id}/"
                                    data-ajax-target="li[data-id={$data->id}]"
                                    ><i class="glyphicon glyphicon-trash text-danger"></i></a>'
                            ]
                        ]
                    );
                    break;

                default:
                    $result[$column] = $this->getColumn($column);
                    break;
            }
        }

        return $result;
    }
}