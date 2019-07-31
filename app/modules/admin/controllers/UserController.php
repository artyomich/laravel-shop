<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\admin\controllers;

use Input;
use Redirect;

/**
 * Контроллер пользователя.
 */
class UserController extends \modules\admin\components\BaseController
{

    /**
     * Отображает страницу входа.
     *
     * @return \Illuminate\View\View
     */
    public function getLogin()
    {
        //  Если пользоватль уже авторизирван, то тогда преадрсуем его на другую страницу.
        if (\Sentry::check()) {
            return Redirect::to('/admin/products/');
        }

        $this->title = 'Авторизация';
        return $this->render('login');
    }

    /**
     * Попытка авторизации пользователя.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postLogin()
    {
        Input::flash();

        try {
            $credentials = array(
                'email'    => Input::get('email'),
                'password' => Input::get('password')
            );
            \Sentry::authenticate($credentials, Input::get('remember-me'));
        } catch (\Exception $e) {
            return Redirect::to('admin/user/login')->withErrors(array($e->getMessage()));
        }

        return Redirect::to('admin/products/index');
    }

    /**
     * Обрабатывает выход.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getLogout()
    {
        \Sentry::logout();
        return Redirect::to('admin/user/login');
    }

}
