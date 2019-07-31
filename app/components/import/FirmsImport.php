<?php
/**
 * @author Dmitriy Koshelev
 */
namespace components\import;

use Cartalyst\Sentry\Users\Eloquent\User;
use components\BaseImport;
use SoapBox\Formatter\Formatter;


/**
 * Родительский класс для всех импортов.
 *
 * @package components\import
 */
class FirmsImport extends BaseImport
{

    /**
     * @inheritdoc
     */
    public static function run($className = self::class)
    {
        return parent::run($className);
    }

    /**
     * @inheritdoc
     */
    protected function import($data)
    {
        if (isset($data->import['item'])) {
            $errors = [];
            $items = isset($data->import['item'][0]) ? $data->import['item'] : [$data->import['item']];
            foreach ($items as $k => $itemData) {
                $user = User::find($itemData['id']);
                if ($user != null) {
                    $user->id_1c = $itemData['id_1c'];
                    $user->access = $itemData['access'];
                    $user->save();
                } else {
                    $errors[] = 'Пользователя с ID ' . $itemData['id'] . ', нет в базе';
                }
            }
        }
    }

    protected function export($importResult = [])
    {
        $users = User::where('is_firm', true)->where('id_1c', '=', null)->select('id', 'id_1c', 'first_name', 'email', 'phone', 'firm', 'address', 'actual_address', 'inn', 'ogrn', 'kpp', 'rs', 'ks', 'bik', 'bank')->get()->toArray();
        return $users;
    }

}