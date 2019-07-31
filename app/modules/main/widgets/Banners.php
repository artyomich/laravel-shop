<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\main\widgets;

use components\Widget;
use models\BannersGroups;
use models\Cities;

/**
 * Banners widget.
 */
class Banners extends Widget
{
    public $group;

    /**
     * @inheritdoc
     */
    public function run()
    {
        $query = \models\Banners::with('image')
            ->where('is_visible', 't')
            ->where(
                function ($query) {
                    $query
                        ->where('city_id', \Cookie::get('city_id', Cities::CITY_BY_DEFAULT))
                        ->orWhereNull('city_id')
                        ->remember(120);
                }
            );

        if (isset($this->group)) {
            $group = BannersGroups::where(['name' => $this->group])->first();
            if ($group) {
                $query = $query->where('group_id', (int)$group->id);
            }
        } else {
            $query = $query->whereNull('group_id');
        }

        return $this->render(
            'index', [
                'banners' => $query->orderBy('sorting')->remember(120)->get()
            ]
        );
    }
}