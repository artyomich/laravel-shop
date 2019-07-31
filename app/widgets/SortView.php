<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace widgets;

use components\Widget;
use components\ActiveRecord;
use Illuminate\Database\Query\Builder;

/**
 * SortView
 *
 * @property ActiveRecord $model
 */
class SortView extends Widget
{
    /**
     * @var array
     */
    public $items = [];

    /**
     * @var ActiveRecord
     */
    public $model = null;

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (!$this->model && !$this->items) {
            \App::abort(500, 'Model or items must be set');
        }

        $search = $this->model . 'Search';
        $className = isset($search) ? $search : $this->model;
        /** @var ActiveRecord $model */
        $model = new $className;
        /** @var Builder $query */
        $query = method_exists($model, 'search') ? $model->search(\Input::all()) : $model;
        $this->items = !empty($this->items) ? $this->items : $query->get();

        $dataGrid = [];
        $columns = $model->columns();

        //  Поготовка данных.
        foreach ($this->items as $item) {
            $data = $item; //(object)$item->getAttributes();
            foreach ($columns as $col) {
                $column = $col->column;
                $value = isset($col->value) ? $col->value : $item->$column;

                switch ($column) {
                    case '_actions':
                        $value = $col->template;
                        foreach ($col->buttons as $name => $btn) {
                            $value = str_replace('_' . $name . '_', $btn, $value);
                        }
                        break;
                }

                $data->$column = eval('return "' . addslashes($value) . '";');
            }

            //  TODO: Пока что только 2 уровня.
            if (!$data->parent_id) {
                $dataGrid[$data->id] = [
                    'parent' => $data,
                    'childs' => []
                ];
            } else {
                $dataGrid[$data->parent_id]['childs'][] = [
                    'parent' => $data,
                    'childs' => []
                ];
            }
        }

        /*foreach ($_data->get() as $item) {
            $data = $item; //(object)$item->getAttributes();
            foreach ($columns as $col) {
                $column = $col->column;
                $value = isset($col->value) ? $col->value : $item->$column;

                switch ($column) {
                    case '_actions':
                        $value = $col->template;
                        foreach ($col->buttons as $name => $btn) {
                            $value = str_replace('_' . $name . '_', $btn, $value);
                        }
                        break;
                }

                $data->$column = eval('return "' . addslashes($value) . '";');
            }
            $dataGrid[] = $data;
        }*/

        return $this->render(
            'index', [
                'model' => $model,
                'data' => $dataGrid,
                'params' => $this->_params
            ]
        );
    }
}