<?php

/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\main\controllers;

use components\Log404;
use components\LogErrorsComponent;
use models\Categories;
use models\Cities;
use models\Filter;
use models\Hdbk404;
use models\Products;
use models\ProductsBalances;
use models\ProductsOpinions;
use models\ProductsSearch;
use models\Redirects;
use models\Users;

/**
 * Контроллер товаров.
 */
class CatalogController extends \modules\main\components\BaseController
{

    /**
     * @const integer сколько выводить записей на страницу.
     */
    const PRODUCTS_PER_PAGE = 20;

    /**
     * Главная страница со списком товаров.
     *
     * @return \Illuminate\View\View
     */
    public function getIndex()
    {
        $this->title = 'Поиск шин';

        $input = \Input::all();
        if (!isset($input['Filter'])) {
            $input['Filter'] = [];
        }

        if (\Sentry::check() && \Sentry::getUser()->is_firm) {
            $template = 'row_item_inline';
            $partner = true;
        } else {
            $template = 'row_item';
            $partner = false;
        }

        return $this->render(
            'list', [
                'products' => ProductsSearch::filter($input)->with('properties')->remember(120)->paginate(20),
                'template' => $template,
                'partner' => $partner
            ]
        );
    }

    /**
     * Калькулятор шин.
     *
     * @return \Illuminate\View\View
     */
    public function getCalculator()
    {
        $this->title = 'Калькулятор параметров легковых шин';

        $filter = require(app_path() . '/config/filter.php');
        $cityId = \Cookie::get('city_id');
        if (!$cityId) {
            $cityId = Cities::CITY_BY_DEFAULT;
        }
        if (!isset($filter[$cityId])) {
            \App::abort(404, 'Фильтр для такого города не найден');
        }

        ksort($filter[$cityId]['Диаметр'], SORT_NUMERIC);
        ksort($filter[$cityId]['Ширина'], SORT_NUMERIC);
        ksort($filter[$cityId]['Высота'], SORT_NUMERIC);
        ksort($filter[$cityId]['Бренд']);
        ksort($filter[$cityId]['Рисунок']);

        return $this->render(
            'calculator', [
                'filter' => $filter[$cityId]
            ]
        );
    }

    /**
     * Выбор показа каталога товаров либо отдельного товара.
     *
     * @param array $parameters
     *
     * @return bool|\Illuminate\View\View|mixed
     */
    public function missingMethod($parameters = [])
    {
        //  Если есть второй параметр, значим ищем товар.
        if (isset($parameters[1])) {
            return $this->productItem($parameters[1], $parameters[0]);
        }

        //  Смотрим сначала в справочнике фильтра.

        $url = str_replace('[', '%5B', urldecode($_SERVER["REQUEST_URI"]));
        $url = str_replace(']', '%5D', $url);

        $filter = Filter::where('source', $url)->first();
        if ($filter) {
            return \Redirect::to($filter->alias);
        }

        $url = urldecode(strtok($_SERVER["REQUEST_URI"], '?'));
        $filter = Filter::where('alias', $url)->remember(120)->first();
        $query = [];
        if ($filter) {
            //  Если нашли, парсим параметры из источника (source) и передаем их в лист для отображения нужного фильтра.
            $query = parse_url($filter->source);
            parse_str($query['query'], $query);
        }

        return $this->productsList($parameters[0], $query, $filter);
    }

    /**
     * Список товаров.
     *
     * @param string $alias
     * @param array|null $query
     * @param Filter|null $filter
     * @return \Illuminate\Http\Response|\Illuminate\View\View
     */
    private function productsList($alias, $query = [], $filter = null)
    {
        $this->title = 'Поиск шин';

        $input = empty($query) ? \Input::all() : $query;
        if (!isset($input['Filter'])) {
            $input['Filter'] = [];
        }

        //  TODO: Сделать фильтр переменных array_only

        if (!empty($query)) {
            $alias = $query['category_id'];
        }

        $category = Categories::getByAlias($alias);
        $input['Filter']['category_id'] = $category->id;

        if (isset($input['Filter']['Сезон'])) {
            $input['Filter']['Сезон'] = $input['Filter']['Сезон'][0];
        }
        $input['Filter'] = array_filter($input['Filter']);

        $paginate = ProductsSearch::filter($input)
            ->with('properties')
            ->with('vendorsBalances')
            ->with('balance')
            ->with('balances')
            ->with('images')
            ->with('categories')
            ->with('opinions')
            ->orderBy('sorting')
            ->remember(120)
            ->paginate(20);
        foreach ($paginate as $product) {
            $product->calcDeliveryCost();
        }
        $additional = [];
        if ($paginate->getCurrentPage() == $paginate->getLastPage() && count(array_except($input['Filter'], ['category_id']))) {
            foreach (Categories::all() as $c) {
                if ($c->id == $category->id) {
                    continue;
                }

                $input['Filter']['category_id'] = $c->id;
                $data = ProductsSearch::filter($input)->get();

                if (count($data)) {
                    $url = str_replace($category->alias, $c->alias, \URL::full());
                    $url = str_replace('page=', '', $url);
                    $additional[] = [
                        'category' => $c,
                        'products' => $data,
                        'url' => $url
                    ];
                }
            }
        }

        $tplItem = 'row_item';
        $partner = false;

        if (\Sentry::check() && \Sentry::getUser()->is_firm) {
            $tplItem .= '_inline';
            $partner = true;
        }

        switch ($category->type) {
            case Categories::TYPE_DISKS:
                $tplItem .= '_disk';
                break;
        }

        $description = $category->description . (isset($filter) && !empty($filter->description) ? (empty($category->description) ? '' : '<br>') . $filter->description : '');
        $description = str_replace([
            '{city_name}',
        ], [
            Cities::getCurrentCity()->name,
        ], $description);

        return $this->render(
            'list', [
                'input' => $input,
                'category' => $category,
                'products' => $paginate,
                'additional' => $additional,
                'numPerPage' => self::PRODUCTS_PER_PAGE,
                'template' => $tplItem,
                'partner' => $partner,
                'pageDesc' => $description,
            ]
        );
    }

    /**
     * Отображение одного товара.
     *
     * @param string $alias
     * @param string $categoryAlias
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\View\View
     */
    private function productItem($alias, $categoryAlias)
    {
        try {
            $product = Products::getByAlias($alias)->calcDeliveryCost();
        } catch (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e) {
            //  Если шина не найдена, пытаемся найти по другим параметрам.
            while (substr($alias, -2) == '_1') {
                $alias = substr($alias, 0, -2);
            }

            //  Так же убираем лишнее из адреса "oao_po_".
            //  http://poshk.ru/catalog/gruz/oao_po_ashk_i-111a_11_00_r20_1/
            $alias = str_replace('oao_po_', '', $alias);

            $product = Products::where('alias', 'LIKE', $alias . '%')->remember(120)->first();

            //  Если и теперь не помогло, смотрим список переадресаций.
            if (!$product) {
                $redirect = Redirects::where(['source' => $_SERVER['REQUEST_URI']])->remember(120)->first();
                if (!$redirect) {
                    //  Поиск по regexp.
                    $redirects = Redirects::all();
                    foreach ($redirects as $redirect) {
                        //  Сначала проверим на валидность.
                        $pattern = '/' . str_replace("/", "\/", $redirect->source) . '/Us';
                        if (preg_match($pattern, $_SERVER['REQUEST_URI']) === 1) {
                            //  Если все ок, перенаправляем.
                            return \Redirect::to(preg_replace($pattern, $redirect->destination, $_SERVER['REQUEST_URI']));
                        }
                    }

                    //  Если ничего не нашли, пытаемся редиректить в соответствующую категорию.
                    if (!empty($categoryAlias)) {
                        return \Redirect::to('/catalog/' . $categoryAlias . '/');
                    }

                    LogErrorsComponent::log(404);

                    return $this->render('404', ['categoryAlias' => $categoryAlias]);
                }

                return \Redirect::to($redirect->destination);
            }

            return \Redirect::to('/catalog/' . $categoryAlias . '/' . $product->alias . '/');
        }

        $tplItem = 'item';
        $partner = false;
        if (\Sentry::check() && \Sentry::getUser()->is_firm) {
            $tplItem .= '_partner';
            $partner = true;
        }

        switch ($product->categories->type) {
            case Categories::TYPE_DISKS:
                $tplItem .= '_disk';
                break;
        }
        //  Блок информации об оплате, доставке и пр.
        /** @var Products $product */
        $pageCategory = \models\PagesCategories::where(['alias' => 'ysloviya'])->remember(120)->first();
        $this->title = $product->name;
        return $this->render(
            $tplItem, [
                'pages' => \models\Pages::where(['category_id' => $pageCategory->id])->remember(120)->get(),
                'categoryAlias' => $categoryAlias,
                'product' => $product,
                'sizes' => $tplItem == 'item' ? $this->allSizes($product) : null,
                'analogs' => $tplItem == 'item' ? $this->allAnalogs($product) : null,
                'checkedOpinions' => $product->getCheckedOpinions(),
                'partner' => $partner
            ]
        );
    }

    /**
     * Другие типоразмеры автошины по модели
     *
     * @param object Products $product
     * @return ProductsSearch::filter
     */

    private function allSizes($product)
    {
        $params = [
            'Filter' =>
                [
                    'Бренд' => $product->properties->brand,
                    'Модель' => $product->properties->model,
                ],
        ];

        $sizes = $this->sizes($product->id, $params);

        return $sizes;

    }

    /**
     * Все аналогичные шины для продукта
     *
     * @param object Products $product
     * @return ProductsSearch::filter
     */

    private function allAnalogs($product)
    {
        $params = [
            'Filter' =>
                [
                    'Ширина' => $product->properties->width_mm ? $product->properties->width_mm : $product->properties->width_inch,
                    'Высота' => $product->properties->series,
                    'Диаметр' => $product->properties->diameter_inch ? $product->properties->diameter_inch : $product->properties->diameter_mm,
                ],
        ];
        $paramsAlt = ['Filter' => ['size' => $product->properties->size]];
        $product->properties->season AND $params['Filter']['Сезон'] = [0 => $product->properties->season];
        $analogs = $this->analogs($product->id, $params);
        if (!count($analogs) AND isset($params['Filter']['Сезон'])) {
            unset($params['Filter']['Сезон']);
            $analogs = $this->analogs($product->id, $params);
        }
        if (!count($analogs)) {
            $analogs = $this->analogs($product->id, $paramsAlt);
        }
        if (!count($analogs)) {
            unset($params['Filter']['Высота']);
            $analogs = $this->analogs($product->id, $params);
        }
        if (!count($analogs) AND $params['Filter']['Диаметр']) {
            unset($params['Filter']['Ширина']);
            $analogs = $this->analogs($product->id, $params);
        }
        return $analogs;
    }

    /**
     * Запись нового отзыва.
     */
    public function postOpinion()
    {
        $opinion = new ProductsOpinions();
        $opinion->scenario = ProductsOpinions::SCENARIO_CREATE_USER_OPINION;

        if (!$opinion->load(\Input::all())) {
            return $this->errorAjax(['error' => $opinion->getErrors()]);
        };

        $opinion->save();

        return $this->answerAjax('OK');
    }

    /**
     * Аналогичные шины для продукта с определенными параметрами.
     *
     * @param $id
     * @param $params
     * @return mixed
     */
    private function analogs($id, $params)
    {
        return ProductsSearch::filter($params)
            ->with('properties')
            ->with('images')
            ->with('opinions')
            ->with('categories')
            ->with('balances')
            ->orderBy('sorting')
            ->where('id', '<>', $id)
            ->remember(120)
            ->paginate(20);
    }

    /**
     * Другие типоразмеры шины.
     *
     * @param $id
     * @param $params
     * @return mixed
     */
    private function sizes($id, $params)
    {
        return ProductsSearch::filter($params)
            ->with('properties')
            ->orderBy('sorting')
            ->where('id', '<>', $id)
            ->remember(120)
            ->paginate(20);
    }
}