<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\admin\controllers;

use components\ActiveRecord;
use models\Categories;
use models\Cities;
use models\Employers;
use models\OnlineConsult;
use models\Products;
use Whoops\Example\Exception;

/**
 * Контроллер категорий.
 */
class HdbkCitiesController extends \modules\admin\components\BaseController
{
    /**
     * @var array домашняя ссылка для хлебных крошек.
     */
    protected $homeLink
        = ['label' => '<i class="glyphicon glyphicon-book"></i> Справочники', 'link' => '/admin/hdbks/'];

    /**
     * Редактирование города.
     *
     * @param integer $id
     *
     * @return \Illuminate\View\View
     */
    public function anyUpdate($id)
    {
        $model = $this->loadModel($id);

        $this->title = $model->name;
        $this->breadcrumbs[] = 'Города';
        $this->breadcrumbs[] = $this->title;
        $viewParams = [
			'model' => $model,
            'employers'=> Employers::all()
        ];

		if (!$model->consult) {
			$consult = new OnlineConsult;
			$consult->city_id = $model->id;
			$consult->city_key = 'enter code here';
			$consult->save();
		}

        if (\Request::isMethod('post')) {
            $model->load(\Input::all());
			$model->consult->load(\Input::all());
			if ($model->save() && $model->consult->save()) {
                return $this->redirect('/admin/hdbkcities/update/' . $model->id);
            }
        }

        return $this->render('form', $viewParams);
    }

    /**
     * Создание нового города.
     *
     * @return \Illuminate\View\View
     */
    public function anyCreate()
    {
        $this->title = 'Новый город';
        $this->breadcrumbs[] = $this->title;

        $model = new Cities;
        $viewParams = [
            'model' => $model,
            'employers'=> Employers::all()
        ];

        if (\Request::isMethod('post')) {
            return !$model->load(\Input::all()) || !$model->save()
                ? $this->render('form', $viewParams, false)
                : $this->redirect('/admin/hdbkcities/update/' . $model->id);
        }

        return $this->render('form', $viewParams);
    }

	/**
	 * Вкл/Выкл эквайринга.
	 *
	 * @param string $state может принимать только enable или disable.
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function getAcquiring($state = 'disable')
	{
		Cities::where([])->update(['enable_acquiring' => $state == 'enable' ? 't' : 'f']);
		return \Redirect::to(\URL::previous());
	}

    /**
     * Удаление существующего города.
     *
     * @param integer $id
     *
     * @return string
     */
    public function postDelete($id)
    {
        try {
            $this->loadModel($id)->delete();
        } catch (\Exception $e) {
            return $this->errorAjax('Не удалось удалить город. ' . $e->getMessage());
        }

        return $this->answerAjax('Город был успешно удален.');
    }

    /**
     * @inheritdoc
     */
    public static function modelName()
    {
        return Cities::className();
    }
}
