<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\main\widgets;

use components\Widget;

/**
 * OnlineConsult widget.
 */
class OnlineConsult extends Widget
{
	/**
	 * @inheritdoc
	 */
	public function run()
	{
		$model = \models\OnlineConsult::getCurrent();
		return $model->is_enable ? $this->render('index', ['model' => $model]) : '';
	}
}