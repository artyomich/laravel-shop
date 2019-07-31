<?php

namespace models;

use \SoapBox\Formatter\Formatter;

class YandexMarket
{
    /**
     * Обновит XML файл для выгрузки.
     */
    public static function updateXml()
    {
        set_time_limit(0);
        ini_set('max_execution_time', 999999);

        $template = [
            'shop' => [
                'name' => 'poshk.ru',
                'company' => 'ООО Пошк',
                'url' => 'http://poshk.ru',
                'currencies' => [],
                'categories' => [],
                'offers' => []
            ]
        ];

        $categories = [];
        foreach (\models\Categories::all() as $category) {
            $categories[] = '<category id="' . $category->id . '">' . $category->name . '</category>';
        }

        //	Как ставить аттрибуты я не представляю.
        $formatter = Formatter::make($template, Formatter::ARR)->toXml();
        $formatter = str_replace('<xml>', '<yml_catalog date="' . date('Y-m-d H:i') . '">', $formatter);
        $formatter = str_replace('</xml>', '</yml_catalog>', $formatter);
        $formatter = str_replace('<currencies/>', '<currencies><currency id="RUR" rate="1" plus="0"/></currencies>', $formatter);
        $formatter = str_replace('<categories/>', '<categories>' . implode('', $categories) . '</categories>', $formatter);

        $cities = \models\Cities::all();
        $categories = [];

        foreach (\models\Categories::all() as $category) {
            $categories[$category->id] = $category;
        }

        $prepayCities = \Config::get('onlinepay.prepayCities');
        foreach ($cities as $city) {
            $products = [];

            //  Если город отключен, то удаляем файл.
            $xmlName = app_path() . '/../public/yandexmarket/' . $city->alias . '.xml';
            if (!$city->is_visible) {
                if (is_file($xmlName)) {
                    unlink($xmlName);
                }

                continue;
            }

            foreach ($city->getVisibleProducts() as $product) {
                $image = Images::getByProductId($product->id);
                $products[] = '<offer id="' . $product->id . '" available="' . ($product->balance && $product->cost ? 'true' : 'false') . '">
					<url>' . $template['shop']['url'] . '/catalog/' . $categories[$product->category_id]->alias . '/' . $product->alias . '/</url>

					<price>' . $product->cost . '</price>

					<currencyId>RUR</currencyId>
					<categoryId>' . $product->category_id . '</categoryId>
					<delivery>true</delivery>
					<pickup>true</pickup>
					<picture>' . $template['shop']['url'] . \helpers\Image::url($image, 1152, 768) . '</picture>
					<name>' . $product->name . '</name>
					<description>' . strip_tags($product->description) . '</description>
					<sales_notes>' . (in_array($city->alias, $prepayCities) ? 'Необходима предоплата' : 'Оплата Наличными/Безнал/VISA') . '</sales_notes>
				</offer>';
            }

            $offers = str_replace('&', '&amp;', implode('', $products));
            file_put_contents($xmlName, str_replace('<offers/>', '<offers>' . $offers . '</offers>', $formatter));
        }
    }
}