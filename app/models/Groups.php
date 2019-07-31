<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace models;

use helpers\Html;

/**
 * ActiveRecord таблицы `users_groups`
 *
 * @package models
 */
class Groups extends \components\ActiveRecord
{
    /**
     * @var string название таблицы.
     */
    protected $table = 'groups';

    /**
     * @const
     */
    const CREATED_AT = 'created_at';

    /**
     * @const
     */
    const UPDATED_AT = 'updated_at';

    /**
     * @const разрешить действие.
     */
    const RULE_ACCEPT = 1;

    /**
     * @const запретить действие.
     */
    const RULE_DENY = -1;

    /**
     * @const унаследовать действие.
     */
    const RULE_INHERIT = 0;

    /**
     * @var array массив правил для записи в базу.
     */
    public $userPermissions = [];

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'    => 'ID',
            'name'  => 'Название',
            'alias' => 'Псевдоним (лат.)'
        ];
    }

    /**
     * @return array вернет список правил с описаниями.
     */
    public function permissionsList()
    {
        return [
            'admin.index'                 => 'Доступ к админке',

            //  Каталог.
            'admin.products.index'        => 'Просмотр каталога',
            'admin.products.update'       => 'Редактирование товаров в каталоге',
            'admin.products.create'       => 'Создание товаров в каталоге',

            //  Категории в каталоге.
            'admin.categories.create'     => 'Создание категорий',
            'admin.categories.update'     => 'Редактирование категорий',

            //  Заказы.
            'admin.orders.index'          => 'Просмотр списка заказов',
            'admin.orders.update'         => 'Редактирование заказов',
            'admin.orders.orderNew'       => 'Просмотр списка новых заказов',
            'admin.orders.orderAccepted'  => 'Просмотр списка принятых заказов',
            'admin.orders.orderComplited' => 'Просмотр списков выполненых заказов',
            'admin.orders.viewAll'       => 'Способность видеть все заказы во всех городах',

            //  Навигация.
            'admin.menus.index'           => 'Просмотр списка меню',
            'admin.menus.create'          => 'Создание пунктов меню',
            'admin.menus.update'          => 'Редактирование пунктов меню',
            'admin.menusTypes.update'     => 'Редактирование типов меню',
            'admin.menusTypes.create'     => 'Создание типов меню',

            //  Страницы.
            'admin.pages.index'           => 'Просмотр списка страниц',
            'admin.pages.update'          => 'Редактирование страниц',
            'admin.pages.create'          => 'Создание страниц',

            //  Банеры.
            'admin.banners.index'         => 'Просмотр списка банеров',
            'admin.banners.create'        => 'Создание банеров',
            'admin.banners.update'        => 'Редактирование банеров',
            'admin.banners.groupupdate'  => 'Создание групп банеров',
            'admin.banners.groupcreate'  => 'Редактирование групп банеров',

            //  Отзывы.
            'admin.opinions.index'       => 'Просмотр списка отзывов',
            'admin.opinions.create'      => 'Создание отзывов',
            'admin.opinions.update'      => 'Редактирование отзывов',

            //  Справочники.
            'admin.hdbks.index'           => 'Просмотр справочников',
            'admin.hdbkcities.index'      => 'Просмотр списка городов',
            'admin.hdbkcities.create'     => 'Создание городов',
            'admin.hdbkcities.update'     => 'Редактирование городов',
            'admin.hdbkemployers.index'  => 'Просмотр списка сотрудников',
            'admin.hdbkemployers.create' => 'Создание сотрудников',
            'admin.hdbkemployers.update' => 'Редактирование сотрудников',
            'admin.hdbkredirects.index' => 'Просмотр списка переадресаций',
            'admin.hdbkredirects.create' => 'Создание переадресации',
            'admin.hdbkredirects.update' => 'Редактирование переадресации',
            'admin.hdbkfilter.index' => 'Просмотр списка фильтра',
            'admin.hdbkfilter.create' => 'Создание записи фильтра',
            'admin.hdbkfilter.update' => 'Редактирование записи фильтра',

            //  Пользователи.
            'admin.users.index'           => 'Просмотр списка пользователей',
            'admin.users.create'          => 'Создание новых пользователей',
            'admin.users.update'          => 'Редактирование существующих пользователей',
            'admin.users.groupupdate'     => 'Редактирование групп пользователей',

            //  Импорт.
            'admin.import.index'          => 'Импорт товаров',

            //Наценка
            'admin.markup.index'          => 'Наценка товаров поставщиков'
        ];
    }

    /**
     * @inheritdoc
     */
    public static function find($id, $columns = ['*'])
    {
        /** @var Groups $instance */
        $instance = parent::find($id, $columns);
        $instance->permissions = json_decode($instance->permissions);
        return $instance;
    }

    /**
     * Вернет список правил.
     * В эту функцию передаются правила из модели. Если например мы добавили новое правило, то автоматически оно не
     * появится во всех группах пользователей, но при редактировании оно добавится к правилам этой модели
     * для дальнейшего взаимодействия .
     *
     * @param array $permissions правила из самой модели.
     *
     * @return array
     */
    public function getPermissions($permissions = null)
    {
        if (!empty($permissions)) {
            foreach ($this->permissionsList() as $rule => $label) {
                if (!isset($permissions[$rule])) {
                    $permissions[$rule] = self::RULE_DENY;
                }
            }

            return $permissions;
        }

        return array_merge(
            array_combine(
                array_keys($this->permissionsList()),
                array_fill(0, count($this->permissionsList()), self::RULE_DENY)
            ),
            (array)$this->permissions
        );
    }

    /**
     * Проверит правило у этой группы.
     *
     * @param string $permission название правила.
     *
     * @return bool вернет true, если правило разрешено.
     */
    public function check($permission)
    {
        return isset($this->permissions->$permission) && $this->permissions->$permission == 1;
    }

    /**
     * @inheritdoc
     */
    public function beforeSave()
    {
        $this->permissions = json_encode($this->getPermissions($this->userPermissions));
        return parent::beforeSave();
    }
}