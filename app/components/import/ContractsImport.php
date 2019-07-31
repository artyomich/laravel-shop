<?php
/**
 * @author Artyom Arifulin
 */
namespace components\import;
use components\BaseImport;
use models\Users;
use models\UsersContracts;

/**
 *
 * @package components\import
 */
class ContractsImport extends BaseImport
{
    protected $enableExport = false; //no export in xml is necessary
    public $errors = [];

    /**
     * @inheritdoc
     */
    public static function run($className = self::class)
    {
        return parent::run($className);
    }

    /**
     * Функция ищет вхождения в строке искомых значений
     * @param string $str word for searching entries
     * @return array $res if found
     */
    public function findTypePrice($str)
    {
        $res = ''; //result string type by default
        $possibleVariesOfTypesFrom1C = [
            [['Розница', 'Интернет', ''], 'type' => 'opt_small', 'name' => 'Мелкий опт'],
            [['мелк.опт'], 'type' => 'opt_small', 'name' => 'Мелкий опт'],
            [['средн.опт'], 'type' => 'opt_middle', 'name' => 'Средний опт'],
            [['крупн.опт'], 'type' => 'opt_big', 'name' => 'Крупный опт'],
            [['Мин.цена'], 'type' => 'min', 'name' => 'Минимальная цена'],
            [['спец. цена', 'СпецЦена'], 'type' => 'spec', 'name' => 'Спец. цена'],
        ];

        if (empty($str)){
            $res = $possibleVariesOfTypesFrom1C[0]; //Если $str пустая, тогда 'Розничная цена'
        } else {
            foreach ($possibleVariesOfTypesFrom1C as $type) { //ищем в массиве, 1й уровень
                foreach ($type[0] as $shablon) { //ищем во втором уровне массива
                    $rr = stripos($str, $shablon); //возвращаем позицию вхождений
                    if (!is_numeric($rr)) { //если позиция не найдена продолжаем искать во втором массиве
                        continue;
                    }
                    $res = $type; //позиция найдена $res = массиву второго уровня
                    break;
                }
            }
        }

        if (!is_array($res)) { //если не найдено, то значение не массив
            $errors[] = 'Ошибка поиска по типу цены: ' . $str . ' Тип цены определить невозможно';
        }

        return $res;
    }

    /**
     * Ищем тип отсрочки в днях, возвращаем число, буквы отбрасываем
     *
     * @param string $str word for searching numbers
     * @return array $res if found
     */
    public function findDelayType($str)
    {
        return preg_replace('/\D/', '', $str); //only numbers
    }

    /**
     * Функция импорта, импортирует данные с csv в базу данных таблицу "users_contracts"
     * @param array $data data from csv file
     * @inheritdoc
     */
    protected function import($data)
    {
        $errors = []; //сюда пишем все ошибки

        if (isset($data->import)) {
            foreach ($data->import as $itemData) {
                //ищем по UIN 1c
                $user = Users::where('id_1c', '=', $itemData['UIN Контрагента'])->first();

                if (!is_object($user)) {//пользователь не найден по UIN
                    continue;
                }

                $typePrice = $this->findTypePrice($itemData['Тип цены']); //тип цены определяем

                if (!is_array($typePrice)) { //невозможно определить тип цены
                    continue;
                }

                $name = $typePrice['name']; //"Розничная цена";
                $costType = $typePrice['type']; //"retail"

                $delayType = $this->findDelayType($itemData['График оплаты']); //только числа

                $data = [
                    'name' => $name,
                    'user_id' => $user->id,
                    'delay_type' => (int)$delayType,
                    'id_1c' => $itemData['UIN соглашения'],
                    'cost_type' => $costType,
                    'city_id' => !is_numeric($itemData['City_id']) ? '1' : $itemData['City_id']];

                $agreement = UsersContracts::where('id_1c', $itemData['UIN соглашения'])->first();

                if (!is_object($agreement)) { //не найдено соглашение
                    $agreement = new UsersContracts;
                }
                $agreement->fill($data);
                $agreement->save(); //сохраняем соглашение в БД

                unset($agreement);
                unset($data);
            }

        } else {
            $errors['No correct data provided!'];
        }
    }
}