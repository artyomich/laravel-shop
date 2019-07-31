<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\main\widgets;

use components\Widget;
use helpers\ArrayHelper;
use models\Cities;
use models\Pages;
use models\PagesCategories;
use models\Products;

/**
 * ProductInstructions widget.
 *
 * @property Products product
 */
class ProductInstructions extends Widget
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        return str_replace([
            '{product_name}',
            '{city_name}',
            '{manufacturer_name}',
            '{size}',
            '{season}',
            '{construction}',
            '{phone}',
            '{category_name}'
        ], [
            ArrayHelper::getValue($this, 'product.name', ''),
            Cities::getCurrentCity()->name,
            ArrayHelper::getValue($this, 'product.properties.manufacturer', ''),
            ArrayHelper::getValue($this, 'product.properties.size', ''),
            ArrayHelper::getValue($this, 'product.properties.season', ''),
            ArrayHelper::getValue($this, 'product.properties.construction', ''),
            Cities::getCurrentCity()->phones,
            ArrayHelper::getValue($this, 'product.categories.name', ''),
        ], $this->loadPage(ArrayHelper::getValue($this, 'pageAlias', '')));
    }

    /**
     * Зарузит системную страницу.
     * Если такой нет, то вернет пустую строку.
     * @return string
     */
    public function loadPage($pageAlias = null)
    {
            /** @var PagesCategories $model */
        return is_null($page = Pages::where('alias', $pageAlias)->remember(120)->first()) ? '' : $page->body;
    }
}