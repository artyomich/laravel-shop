<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\admin\controllers;

use models\Menus;
use models\MenusTypes;
use models\Pages;
use models\Transactions;
use models\TransactionsSearch;
use SoapBox\Formatter\Formatter;

/**
 * Контроллер транзакций.
 */
class TransactionsController extends \modules\admin\components\BaseController
{
	/**
	 * @var array
	 */
	protected $homeLink
		= ['label' => '<i class="glyphicon glyphicon-file"></i> Транзакции', 'link' => '/admin/pages'];

	/**
	 * Метод экспорта транзакций для 1С.
	 *
	 * @return \Illuminate\View\View
	 */
	public function anyExport()
	{
		$result = [];
		$model = new TransactionsSearch;
		$search = $model->search(\Input::all())
			->where('date_update', '>=', \Input::get('date', '1990-01-01'))
			->where('status', Transactions::STATUS_SUCCESS);

		foreach ($search->get() as $transaction) {
			$result[] = $transaction->toArray();
		}

		array_shift($result);

		$formatter = Formatter::make($result, Formatter::ARR);

		return $formatter->toXml();
	}
}
