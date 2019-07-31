<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace widgets;

use components\Widget;
use components\ActiveRecord;

/**
 * GridView
 *
 * @property ActiveRecord $model
 */
class GridView extends Widget
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        if (!$this->model) {
            \App::abort(500, 'Model must be set');
        }

        $search = $this->model . 'Search';
        $className = isset($search) ? $search : $this->model;
        /** @var ActiveRecord $model */
        $model = new $className;
        $_data = method_exists($model, 'search') ? $model->search(\Input::all()) : $model;
        $_data = $_data->paginate(24);
        $dataGrid = [];
        $columns = $model->columns();
        $_parts = explode('\\', $className);
        $getParams = \Input::get(end($_parts));

        //  Данные в колонках.
        foreach ($_data as $item) {
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

				try {
					$data->gridColumns[$column] = eval("return \"" . addslashes($value) . "\";");
				} catch (\Exception $e) {
				}
            }
            $dataGrid[] = $data;
        }

        //  Фильтр.
        $filters = [];
        foreach ($model->columns() as $column) {
            $filter = isset($column->filter) ? $column->filter : '';
            if (isset($getParams[$column->column])) {
                if (strpos($column->filter, 'option')) {
                    $filter = str_replace(
                        'value="' . $getParams[$column->column] . '"',
                        'value="' . $getParams[$column->column] . '" selected',
                        $column->filter
                    );
                } elseif (strpos($column->filter, 'value=""')) {
                    $filter = str_replace('value=""', 'value="' . $getParams[$column->column] . '"', $column->filter);
                } else {
                    $filter = str_replace('>', ' value="' . $getParams[$column->column] . '">', $column->filter);
                }
            }
            $filters[] = $filter;
        }


        //  Дополняем пагинацию.
        $_data->appends(\Input::all());

        return $this->render(
            'index', [
                'model' => $model,
                'filters'   => $filters,
                'data'      => $dataGrid,
                'paginator' => $_data->links()
            ]
        );
    }
}