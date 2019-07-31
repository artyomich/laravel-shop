<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace models;

use helpers\Html;

/**
 * ActiveRecord таблицы `products`.
 *
 * @package models
 */
class LogErrors extends \components\ActiveRecord
{
    /**
     * @var string название таблицы.
     */
    protected $table = 'log_errors';

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date_create' => 'Дата',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        ];
    }

    /**
     * @inheritdoc
     */
    public function defaultColumns()
    {
        return [
            'id',
            'url',
            'method',
            'code',
            'message',
            'referer',
            'user_agent',
            'remote_url',
            'date_create',
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
                        'template' => '_delete_',
                        'buttons' => [
                            'delete' => '<a href="#"
                                data-ajax-action="delete" data-ajax-confirm="Вы действительно хотите удалить эту запись?"
                                data-ajax-url="/admin/hdbkerrors/delete/{$data->id}/"
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
                case 'message':
                    $result[$column] = $this->getColumn(
                        $column, [
                            'value' => '$data->message',
                        ]
                    );
                    break;
                case 'url':
                case 'referer':
                    $result[$column] = $this->getColumn(
                        $column, [
                            'value' => '<a href="{$data->$column}" target="_blank">{$data->$column}</a>',
                            'filter' => Html::activeTextInput($this, $column, ['class' => 'form-control']),
                            'class' => 'col-xs-4'
                        ]
                    );
                    break;
                case '_actions':
                    $result[$column] = $this->getColumn($column);
                    break;
                default:
                    $result[$column] = $this->getColumn($column);
                    break;
            }
        }

        return $result;
    }
}