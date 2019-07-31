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
 * ActiveRecord таблицы `redirects`.
 *
 * @package models
 */
class Redirects extends \components\ActiveRecord
{
    /**
     * @var string название таблицы.
     */
    protected $table = 'redirects';

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
            'id' => 'ID',
            'source' => 'От',
            'destination' => 'На'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['source', 'destination'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function defaultColumns()
    {
        return [
            'source',
            'destination',
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
                        'column' => $column,
                        'template' => '_view_ _delete_',
                        'buttons' => [
                            'view' => '<a href="{$data->source}" target="_blank"><i class="glyphicon glyphicon-link"></i></a>',
                            'delete' => '<a href="#"
                                data-ajax-action="delete" data-ajax-confirm="Вы действительно хотите удалить эту запись?"
                                data-ajax-url="/admin/hdbkredirects/delete/{$data->id}/"
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
                case 'id':
                    $result[$column] = $this->getColumn(
                        $column, [
                            'value' => '{$data->$column}',
                            'filter' => Html::activeTextInput($this, $column, ['class' => 'form-control']),
                            'class' => 'col-xs-1'
                        ]
                    );
                    break;
                case 'source':
                    $result[$column] = $this->getColumn(
                        $column, [
                            'value' => '<a href="/admin/hdbkredirects/update/{$data->id}">{$data->$column}</a>',
                            'filter' => Html::activeTextInput($this, $column, ['class' => 'form-control']),
                            'class' => 'col-xs-5'
                        ]
                    );
                    break;
                case 'destination':
                    $result[$column] = $this->getColumn(
                        $column, [
                            'value' => '{$data->$column}',
                            'filter' => Html::activeTextInput($this, $column, ['class' => 'form-control']),
                            'class' => 'col-xs-5'
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
}