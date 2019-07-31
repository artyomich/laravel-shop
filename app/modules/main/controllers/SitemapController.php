<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\main\controllers;

use models\Pages;
use models\Products;

/**
 * Контроллер товаров.
 */
class SitemapController extends \modules\main\components\BaseController
{
    /**
     * @return \Illuminate\View\View
     */
    public function getIndex()
    {
        $sitemap = [
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' .
            '<url>',
            '<loc>http://poshk.ru/</loc>',
            '<priority>0.5</priority>',
            '</url>'
        ];

        //  Каталог товара.
        $products = Products::with('categories')->get();
        foreach ($products as $product) {
            $sitemap = array_merge($sitemap, [
                '<url>',
                '<loc>http://poshk.ru/catalog/' . urlencode($product->categories->alias) . '/' . urlencode($product->alias) . '/</loc>',
                '<priority>0.5</priority>',
                '</url>'
            ]);
        }

        //  Все страницы.
        $pages = Pages::where('is_visible', 't', 'IS')->remember(120)->get();
        foreach ($pages as $page) {
            $sitemap = array_merge($sitemap, [
                '<url>',
                '<loc>http://poshk.ru' . $page->alias . '</loc>',
                '<priority>0.5</priority>',
                '</url>'
            ]);
        }

        $sitemap[] = '</urlset>';

        file_put_contents(app_path() . '/../public/sitemap.xml', implode("\n", $sitemap));

        return '';
    }
}