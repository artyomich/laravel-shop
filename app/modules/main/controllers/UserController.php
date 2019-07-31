<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\main\controllers;

use Cartalyst\Sentry\Users\Eloquent\User;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use components\ActiveRecord;
use helpers\CsvToArray;
use models\Cities;
use models\Orders;
use models\Employers;
use models\Products;
use models\Users;
use Input;
use models\UsersContracts;
use Redirect;

/**
 * Контроллер пользователя.
 */
class UserController extends \modules\main\components\BaseController
{
    /**
     * @var Users
     */
    protected $curentUser;

    /**
     * @__constructor
     */
    public function __construct()
    {
        parent::__construct();
        if (\Sentry::check()) {
            $this->curentUser = Users::find(\Sentry::getUser()->id);
        }
    }

    /**
     * Отображает страницу входа.
     *
     * @return \Illuminate\View\View
     */
    public function getLogin()
    {
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
                'email' => Input::get('email'),
                'password' => Input::get('password')
            );
            \Sentry::authenticate($credentials);
        } catch (\Exception $e) {
            $err_msg = $e->getMessage();
            $clear_msg = preg_replace('/\[(\w+)\]/i', '[]', $err_msg);//удаляем содержимое в квадратных скобках типа w
            $clear_msg = preg_replace('/\[([^@\s]*@[^@\s]*\.[^@\s]*)\]/i', '[]', $clear_msg);//удаляем email из квадратных скобок
            return Lang::get('auth.'.$clear_msg);
        }
        $city = new CitiesController();
        $city->postChange(\Sentry::getUser()->city_id ? \Sentry::getUser()->city_id : 1);
        return 'ok';
    }

    /**
     * Обрабатывает выход.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postLogout()
    {
        \Sentry::logout();
    }

    /**
     * Регистрация нового пользователя
     */
    public function postRegistration()
    {
        Input::flash();

        try {
            $password = str_random(8);
            $registration = array(
                'email' => Input::get('email'),
                'first_name' => Input::get('name'),
                'password' => $password,
                'activated' => true,
            );
            $user = \Sentry::createUser($registration);
            $clientGroup = \Sentry::findGroupByName('Клиенты');
            $user->addGroup($clientGroup);
        } catch (\Exception $e) {
            $err_msg = $e->getMessage();
            $clear_msg = preg_replace('/\[(\w+)\]/i', '[]', $err_msg);//удаляем содержимое в квадратных скобках типа w
            $clear_msg = preg_replace('/\[([^@\s]*@[^@\s]*\.[^@\s]*)\]/i', '[]', $clear_msg);//удаляем email из квадратных скобок
            return Lang::get('auth.'.$clear_msg);
        }
        \Mail::send('emails/registration', ['registration' => $registration], function ($message) use ($registration) {
            $message->to($registration['email']);
        });

        return 'ok';
    }

    /**
     * Восстановление пароля пользователя.
     *
     * @return int
     */
    public function anyRestorePassword()
    {
        $model = new Users();
        $status = \Input::get('mode', '');

        if ($status == 'activate') {
            //  Активация(установка) нового пароля.
            $user = \Sentry::findUserById(Input::get('u'));
            if (!$user->checkResetPasswordCode(Input::get('code'))) {
                return Redirect::to('/');
            }

            if (\Request::isMethod('post')) {
                $model->load(Input::all(), false);
                if (!$model->validatePassword() || !$user->attemptResetPassword(Input::get('code'), Input::get('Users.password'))) {
                    $model->addError('password', 'Не удалось изменить пароль');
                } else {
                    $status = 'success';
                }
            }
        } elseif (\Request::isMethod('post')) {
            //  Когда пришел запрос на восстановление пароля.
            $model->load(\Request::input());
            $_model = User::where(['email' => $model->email])->first();
            if (!$_model) {
                $model->addError('email', 'Нельзя восстановить пароль для данного пользователя.');
            } else {
                $user = \Sentry::findUserByLogin($model->email);
                $code = $user->getResetPasswordCode();
                \Mail::send(
                    'emails/restore-password', [
                    'user' => $user,
                    'model' => $_model,
                    'code' => $code
                ], function ($message) use ($_model) {
                    $message
                        ->to($_model->email, $_model->name)
                        ->subject('Восстановление пароля на сайте POSHK.ru');
                });

                $status = 'sended';
            }
        }

        return $this->render('restore-password', [
            'model' => $model,
            'status' => $status
        ]);
    }

    /**
     * ###
     */
    public function anyOffice()
    {
        if (!\Sentry::check()) {
            return \Redirect::to('/');
        } else {
            $user = $this->curentUser;
            if (\Request::isMethod('post')) {
                $data = Input::all();

                /** @var Users $user */
                if (is_null($user->getOriginal('type'))) {
                    $user->is_firm = $data['Users']['type'] !== 'physical';
                    $user->is_vendor = $data['Users']['type'] === 'vendor';
                } else {
                    $user->is_firm = $user->getOriginal('is_firm');
                }
                $user->city_id = Cities::getCurrentCity()->id;
                if (!$user->load($data)) {
                    return $user->getErrors();
                }

                if (!empty($user->password)) {
                    $user->isSetNewPassword = true;
                }

                return !$user->saveUser() ? $user->getErrors() : '';
            }
            $defaultManager = Cities::getCurrentCity();
            $employers = Employers::where('id', $defaultManager->default_manager)->first();
            if (($defaultManager->work_begin < date('H')) and ($defaultManager->work_end > date('H'))) {
                $is_work = 'Офис сейчас открыт';
            } else {
                $is_work = 'Офис сейчас закрыт';
            }

            if ($user->is_firm && !$user->is_vendor) {
                $firmStat['ordersCount'] = Orders::where('id_user', $user->id)->count();
                $firmStat['ordersTotal'] = Orders::where('id_user', $user->id)->sum('cost');
                $firmStat['ordersTotalClose'] = Orders::where('id_user', $user->id)->where('status', Orders::STATUS_CLOSED)->sum('cost');
                $firmStat['lastOrder'] = Orders::where('id_user', $user->id)->orderBy('id', 'DESC')->where('status', Orders::STATUS_CLOSED)->first();
                $firmStat['typeCost'] = UsersContracts::getFirstCostType();
                $template = 'officePartner';
            } else {
                $template = 'office';
                $firmStat = false;
            }
            return $this->render($template, [
                'isFirm' => $user->is_firm,
                'isVendor' => $user->is_vendor,
                'orders' => Orders::with('items')->where('id_user', $user->id)->orderBy('id', 'DESC')->take(5)->get(),
                'closedOrders' => Orders::with('items')->where('id_user', $user->id)->where('status', Orders::STATUS_CLOSED)->orderBy('id', 'DESC')->take(5)->get(),
                'ordersCount' => Orders::where('id_user', $user->id)->count(),
                'closedOrdersCount' => Orders::where('id_user', $user->id)->where('status', Orders::STATUS_CLOSED)->count(),
                'employers' => $employers,
                'isWork' => $is_work,
                'model' => $user,
                'firmStat' => $firmStat
            ]);
        }
    }


    /**
     * Вывод общего списка заказов
     */
    public function anyOrderlist()
    {
        if (\Sentry::check()) {
            $user = \Sentry::getUser();
            return $this->render('orderlist', ['orders' => Orders::with('items')->where('id_user', $user->id)->orderBy('id', 'DESC')->get()]);
        } else {
            return \Redirect::to('/');
        }
    }

    /**
     * Вывод списка отгрузок (заказы со статусом "Закрыт")
     */
    public function anyOrderCloseList()
    {
        if (\Sentry::check()) {
            $user = \Sentry::getUser();
            return $this->render('orderCloseList', ['orders' => Orders::with('items')->where('id_user', $user->id)->where('status', Orders::STATUS_CLOSED)->orderBy('id', 'DESC')->get()]);
        } else {
            return \Redirect::to('/');
        }
    }

    /**
     * ###
     */
    public function anyVendor()
    {
        if (!\Sentry::check()) {
            return \Redirect::to('/');
        }
        return $this->render('vendor');
    }

    /**
     * ###
     */
    public function getSaveToXls()
    {
        if (!\Sentry::check()) {
            return \Redirect::to('/');
        }
        $user = \Sentry::getUser();
        $products = Products::with('properties')->orderBy('category_id')->get();
        \Excel::create('catalog', function ($excel) use ($products, $user) {
            $excel->sheet('Лист1', function ($sheet) use ($products, $user) {
                $sheet->appendRow(['Код поставщика', $user->id, 'Наименование поставщика', $user->firm, 'ИНН', $user->inn, 'КПП', $user->kpp]);
                $sheet->appendRow(['Код', 'Бренд', 'Модель', 'Типоразмер',
                    'Норма слойности', 'Сезон', 'Шипы', 'Рисунок/ось', 'Комплектность', 'Код контрагента', 'Название контрагента', 'Остатки', 'Цена']);
                foreach ($products as $product) {
                    $sheet->appendRow([
                        $product->id_1c,
                        ($product->properties->brand ? $product->properties->brand : ''),
                        ($product->properties->model ? $product->properties->model : ''),
                        ($product->properties->size ? $product->properties->size : ''),
                        ($product->properties->layouts_normal ? $product->properties->layouts_normal : ''),
                        ($product->properties->season ? $product->properties->season : ''),
                        ($product->properties->spikes ? $product->properties->spikes : ''),
                        ($product->properties->image_axis ? $product->properties->image_axis : ''),
                        ($product->properties->completeness ? $product->properties->completeness : ''),
                    ]);
                }
            }
            );
        }
        )->download('xls');
    }
}