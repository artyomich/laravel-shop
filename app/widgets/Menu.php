<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace widgets;

use components\Widget;
use components\ActiveRecord;

/**
 * Main widget.
 *
 * @property string $type
 * @property string $template
 */
class Menu extends Widget
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        //  Шаблон для рендера.
        //  FIXME: Какой то баг с PHP (!isset($this->template)).
        if (!strlen($this->template)) {
            $this->template = $this->type;
        }

        $data = [];
        $items = \models\Menus::join('menus_types', 'menus_types.id', '=', 'menus.menu_id')
            ->where('menus_types.alias', '=', $this->type)
            ->orderBy('parent_id', 'desc')
            ->orderBy('sorting')
            ->select('menus.*')
            ->remember(120)
            ->get();

        $cityAlias = \Cookie::get('city_alias');
        if (!isset($cityAlias)) {
            $cityAlias = 'barnaul';
        }

        foreach ($items as $item) {
            //  Замены. Например меняем %CITYNAME%
            $item->alias = str_replace('%CITYNAME%', $cityAlias, $item->alias);

            if (!isset($item->parent_id)) {
                $data[$item->id] = [
                    'parent' => $item,
                    'childs' => []
                ];
                continue;
            }

            $data[$item->parent_id]['childs'][] = $item;
        }

        return $this->render(
            $this->template, [
                'data'    => $data,
                'current' => $_SERVER['REQUEST_URI']
            ]
        );
    }
}