<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\admin\components;

use models\Cities;
use models\Orders;

/**
 * Базовый контроллер для панели администрирования.
 */
class BaseController extends \components\BaseController
{
    /**
     * @var array домашняя ссылка для хлебных крошек.
     */
    protected $homeLink = ['label' => '<i class="glyphicon glyphicon-list-alt"></i> Каталог', 'link' => '/admin/'];

    /**
     * @__construct
     */
    public function __construct()
    {
        $except = ['getLogin', 'postLogin'];
        $importKey = \Input::get('key');

        if (!empty($importKey) && $importKey == \Config::get('app.importKey')) {
            $except[] = 'getIndex';
            $except[] = 'postIndex';
            $except[] = 'postProductsUpdate';
            $except[] = 'anyExport';
            $except[] = 'postFirms';
            $except[] = 'postOrders';
            $except[] = 'postProducts';
            $except[] = 'postProductsUpdate';
            $except[] = 'postContractorsImport';
            $except[] = 'postContractsImport';
            $except[] = 'getCallback';
            $except[] = 'getProductsNotifications';
        }

        //  Для админки в любом случае юзаем фильтры.
        $this->beforeFilter('auth', ['except' => $except]);

        parent::__construct();
    }

    /**
     * Рендер view действия.
     *
     * @param       $viewName
     * @param array $params
     */
    public function render($viewName, $params = [])
    {
        $user = \Sentry::getUser();

        //  Проверка прав на доступ к текущей странице без префикса admin.
        $uri = \Route::current()->getUri();
        $uri = explode('/', $uri);
        if (count($uri) < 4) {
            $uri[] = 'index';
        }
        $uri = array_diff($uri, ['{one?}', '{two?}', '{three?}', '{four?}', '{five?}']);
        $uri = implode('.', $uri);

        if (isset($user)) {
            if (!$user->hasAccess($uri)) {
                \App::abort(500, 'У вас не достаточно прав для совершения этого действия');
            }

            //  Город менеджера.
            $city = Cities::find($user->city_id);

            /**
             * Получим информацию о текущих заказах.
             */
            $newOrdersCount = Orders::where('status', Orders::STATUS_NEW)
                ->where('city_id', $city->id)
                ->where(
                    function ($query) {
                        $query->where('manager_id', \Sentry::getUser()->id)
                            ->orWhereNull('manager_id');
                    }
                )
                ->count();
            $myOrdersCount = Orders::where('manager_id', $user->id)
                ->where('status', Orders::STATUS_ACCEPTED)
                ->count();
            $completedOrdersCount = Orders::where('manager_id', $user->id)
                ->where('status', Orders::STATUS_COMPLETED)
                ->count();

            $ordersLabel = '<i class="fa fa-shopping-cart"></i> <span>Заказы</span>';
            $newOrdersCount && $ordersLabel .= '<span class="badge pull-right alert-danger" style="margin-left: 3px;">' . $newOrdersCount . '</span>';
            $myOrdersCount && $ordersLabel .= '<span class="badge pull-right alert-info">' . $myOrdersCount . '</span>';


            //  Меню навигации.
            $navigation = [];
            $user->hasAccess('admin.products.index') &&
                $navigation[] = [
                    'label' => '<i class="glyphicon glyphicon-list-alt"></i> <span>Каталог</span>',
                    'link'  => '/admin/products/'
                ];

            $user->hasAccess('admin.orders.index') && $navigation[] = ['label' => $ordersLabel, 'link' => '/admin/orders/'];

            $user->hasAccess('admin.menus.index') &&
                $navigation[] = [
                    'label' => '<i class="fa fa-bars"></i> <span>Навигация</span>',
                    'link'  => '/admin/menus/'
                ];

            $user->hasAccess('admin.pages.index') &&
                $navigation[] = [
                    'label' => '<i class="glyphicon glyphicon-file"></i> <span>Страницы</span>',
                    'link'  => '/admin/pages/'
                ];

            $user->hasAccess('admin.banners.index') &&
                $navigation[] = [
                    'label' => '<i class="fa fa-th-list"></i> <span>Банеры</span>',
                    'link'  => '/admin/banners/'
                ];

            $user->hasAccess('admin.opinions.index') &&
                $navigation[] = [
                    'label' => '<i class="glyphicon glyphicon-comment"></i> <span>Отзывы</span>',
                    'link'  => '/admin/opinions/'
                ];

            $user->hasAccess('admin.hdbks.index') &&
                $navigation[] = [
                    'label' => '<i class="glyphicon glyphicon-book"></i> <span>Справочники</span>',
                    'link'  => '/admin/hdbks/'
                ];

            $user->hasAccess('admin.users.index') &&
                $navigation[] = [
                    'label' => '<i class="fa fa-users"></i> <span>Пользователи</span>',
                    'link'  => '/admin/users/'
                ];

            $user->hasAccess('admin.import.index') &&
                $navigation[] = [
                    'label' => '<i class="glyphicon glyphicon-import"></i> <span>Импорт</span>',
                    'link'  => '/admin/import/'
                ];

            $user->hasAccess('admin.markup.index') &&
                $navigation[] = [
                    'label' => '<i class="glyphicon glyphicon-piggy-bank"></i> <span>Наценка</span>',
                    'link'  => '/admin/markup/'
                ];

        } else {
            $navigation = [];
            $newOrdersCount = $myOrdersCount = $completedOrdersCount = 0;
            $city = Cities::first();
        }

        return parent::render(
            $viewName, array_merge(
                $params, [
                    'navigation'           => $navigation,
                    'newOrdersCount'       => $newOrdersCount,
                    'myOrdersCount'        => $myOrdersCount,
                    'completedOrdersCount' => $completedOrdersCount,
                    'city'                 => $city
                ]
            )
        );
    }
}