<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace components;

/**
 * Родительский класс для всех контроллеров.
 *
 * @package components
 */
class BaseController extends \Controller
{
    /**
     * @var string заголовок.
     */
    protected $title = '';

    /**
     * @var string
     */
    protected $keywords = '';

    /**
     * @var string
     */
    protected $description = '';

    /**
     * @var string путь до представлений.
     */
    protected $viewsPath = '';

    /**
     * @var array
     */
    protected $breadcrumbs = [];

    /**
     * @var array домашняя ссылка для хлебных крошек.
     */
    protected $homeLink = ['label' => 'Главная', 'link' => '/'];

    /**
     * @var array пути для views.
     */
    //protected $viewsDirs = [];

    /**
     * @constructor
     */
    public function __construct()
    {
        /**
         * <code>
         * @use('\helpers\Image')
         * </code>
         */
        \Blade::extend(
            function ($value) {
                return preg_replace("/@use\('(.+)'\)/Us", '<?php use $1; ?>', $value);
            }
        );

        /**
         * <code>
         * {? $var = "wtf!?" ?}
         * </code>
         */
        \Blade::extend(
            function ($value) {
                return preg_replace('/\{\?(.+)\?\}/s', '<?php $1 ?>', $value);
            }
        );

        \Blade::extend(function ($value) {
            return preg_replace('/(\s*)@break(\s*)/', '$1<?php break; ?>$2', $value);
        });
    }

	/**
	 * @return string вернет название вызванного класса.
	 */
	public static function className()
	{
		return get_called_class();
	}

    /**
     * Рендер view действия.
     *
     * @param       $viewName
     * @param array $params
     */
    public function render($viewName, $params = [])
    {
        $route = explode('@', \Route::currentRouteAction());
        $controller = explode('\\', $route[0]);
        $controller = end($controller);

        //  Если вначале названия вьюхи ставится ":", то убираем название модуля из начала тем самым можно рендерить вьюхи из других модулей.
        if ($viewName[0] != ':') {
            $view = strtolower(substr($controller, 0, strlen($controller) - 10)) . '/' . $viewName;
        } else {
            $view = substr($viewName, 1);
        }

        $params = array_merge(
            $params, [
                'title'       => $this->title,
                'keywords'    => $this->keywords,
                'description' => $this->description,
                'version' => \Config::get('version'),
                'isPost'      => \Request::isMethod('POST'),
                'isAjax'      => \Request::ajax()
            ]
        );

        //  Home link.
        array_unshift($this->breadcrumbs, $this->homeLink);
        $params['breadcrumbs'] = $this->breadcrumbs;

        if (!empty($this->viewsPath)) {
            \View::addLocation($this->viewsPath);
        } else {
            $path = explode('\\', get_called_class());
            array_pop($path);
            array_pop($path);
            $path[] = 'views';
            \View::addLocation(__DIR__ . '/../' . implode('/', $path));
        }

        if ($view == 'index/404') {
            return $this->renderError('404', $params, 404);
        } else {
            return \View::make($view, $params);
        }
    }

    /**
     * Рендер view действия (ошибка).
     *
     * @param       $viewName
     * @param array $params
     */
    public function renderError($viewName, $params = [], $code = 500)
    {
        $route = explode('@', \Route::currentRouteAction());
        $controller = explode('\\', $route[0]);
        $controller = end($controller);
        $view = strtolower(substr($controller, 0, strlen($controller) - 10)) . '/' . $viewName;
        $params = array_merge(
            $params, [
                'title' => $this->title,
                'keywords' => $this->keywords,
                'description' => $this->description,
                'isPost' => \Request::isMethod('POST'),
                'isAjax' => \Request::ajax(),
                'version' => \Config::get('version')
            ]
        );

        //  Home link.
        array_unshift($this->breadcrumbs, $this->homeLink);
        $params['breadcrumbs'] = $this->breadcrumbs;

        if (!empty($this->viewsPath)) {
            \View::addLocation($this->viewsPath);
        } else {
            $path = explode('\\', get_called_class());
            array_pop($path);
            array_pop($path);
            $path[] = 'views';
            \View::addLocation(__DIR__ . '/../' . implode('/', $path));
        }

        return \Response::view($view, $params, $code);
    }

    /**
     * Рендер view действия для ajax запроса.
     *
     * @param         $viewName
     * @param array   $params
     * @param bool    $reloadPage
     * @param integer $status
     */
    public function renderAjax($viewName, $params = [], $reloadPage = false, $status = 200)
    {
        return json_encode(
            [
                'status'     => $status,
                'html'       => (string)$this->render($viewName, $params),
                'reloadPage' => $reloadPage
            ]
        );
    }

    /**
     * Отправит JSON ответ.
     *
     * @param string $message
     * @param bool   $reloadPage
     *
     * @return string
     */
    public function answerAjax($message = '', $reloadPage = false)
    {
        return json_encode(
            [
                'status'     => 200,
                'html'       => $message,
                'reloadPage' => $reloadPage
            ]
        );
    }

    /**
     * Отправит JSON ответ с ошибкой.
     *
     * @param string $message
     *
     * @return string
     */
    public function errorAjax($message, $code = 500)
    {
        return json_encode(
            [
                'status'  => $code,
                'message' => $message
            ]
        );
    }

    /**
     * Проверит тип запроса и выдаст exception в случае если это не ajax запрос.
     */
    public function ajaxOnly()
    {
        if (!\Request::ajax()) {
            \App::abort(500, 'This is not ajax request');
        }
    }

    /**
     * Отправит JSON ответ на переадресацию.
     *
     * @param string $url
     *
     * @return string
     */
    public function redirectAjax($url)
    {
        return json_encode(
            [
                'status'   => 200,
                'redirect' => $url
            ]
        );
    }

    /**
     * Переадресует на другую страницу.
     *
     * @param string $url
     *
     * @return string
     */
    public function redirect($url)
    {
        return \Redirect::to($url);
    }

    /**
     * Setup the layout used by the controller.
     */
    protected function setupLayout()
    {
        if (!is_null($this->layout)) {
            $this->layout = \View::make($this->layout);
        }
    }

    /**
     * @return string|ActiveRecord вернет полное имя модели по умолчанию для этого контроллера.
     */
    public static function modelName()
    {
        $className = explode('\\', get_called_class());
        return '\\models\\' . substr(end($className), 0, -10);
    }

    /**
     * Загрузит модель этого контроллера по идентификатору.
     *
     * @param $id
     *
     * @return ActiveRecord
     */
    public function loadModel($id)
    {
        /** @var ActiveRecord $model */
        $modelName = $this->modelName();
        $model = $modelName::find($id);
        if (!$model) {
            \App::abort(404, 'Model not found');
        }

        return $model;
    }
}
