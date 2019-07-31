<?php
/**
 * Роутинг.
 *
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

$routeMap = [
    //  Админка.
    'admin' => [
        'routes' => [
            'products' => 'ProductsController',
            'productsopinions' => 'ProductsOpinionsController',
            'orders' => 'OrdersController',
            'user' => 'UserController',
            'categories' => 'CategoriesController',
            'menus' => 'MenusController',
            'menusTypes' => 'MenusTypesController',
            'pages' => 'PagesController',
            'banners' => 'BannersController',
            'opinions' => 'OpinionsController',
            'transactions' => 'TransactionsController',
            'hdbks' => 'HdbksController',
            'hdbkcities' => 'HdbkCitiesController',
            'hdbkemployers' => 'HdbkEmployersController',
            'hdbkredirects' => 'HdbkRedirectsController',
            'hdbkfilter' => 'HdbkFilterController',
            'hdbkerrors' => 'HdbkErrorsController',
            'users' => 'UsersController',
            'import' => 'ImportController',
            'markup' => 'MarkupController',
            '/' => 'ProductsController',
        ]
    ],
    //	Оплата.
    'onlinepay' => [
        'routes' => [
            'gazprom' => 'GazpromController',
            'cash' => 'CashController',
            'bill' => 'BillController',
        ]
    ],
    //  Сайт.
    '' => [
        'routes' => [
            'user' => 'UserController',
            'cities' => 'CitiesController',
            'opinions' => 'OpinionsController',
            'cart' => 'CartController',
            'order' => 'OrderController',
            'filter' => 'FilterController',
            '404' => 'NotFoundController',
            '500' => 'FailController',
            'sitemap' => 'SitemapController',
            'events' => 'EventsController',
            'notify' => 'ProductsNotificationsController',
            'recommend' => 'RecommendController',
            'import' => 'ImportController',
            'modal' => 'ModalController',
            'catalog' => ['controller' => 'CatalogController', 'alias' =>'catalog'], //массив передает псевдоним контроллеру, задействовано в breadcrumbs
            'news' => ['controller' => 'NewsController', 'alias' =>'news'],
            'team' => ['controller' => 'TeamController', 'alias' =>'team'],
            '/' => ['controller' => 'IndexController', 'alias' =>'home'] // внимание! главную страницу оставлять последним в массиве $routeMap
        ]
    ]
];

Route::filter(
    'csrf', function () {
    if (Request::getMethod() !== 'GET' && Session::token() != Input::get('_token')) {
        throw new Illuminate\Session\TokenMismatchException;
    }
}
);

/**
 * Build.
 */
foreach ($routeMap as $moduleName => $module) {
    $params = isset($module['params']) ? (array)$module['params'] : [];
    $routes = (array)$module['routes'];

    $routeParams = [
        'namespace' => 'modules\\' . (empty($moduleName) ? 'main' : $moduleName) . '\\controllers'
    ];

    if (!empty($moduleName)) {
        $routeParams['prefix'] = $moduleName;
    }

    Route::group(
        $routeParams, function () use ($routes) {
        //  Остальные routes.
        foreach ($routes as $name => $route) {
            if (is_array($route)) { // есть псевдоним
                Route::controller($name, $route['controller'],[$route['alias']]); //3-е значение - псевдоним - должен быть массивом
            } else {
                Route::controller($name, $route);
            }
        }
    }
    );
}