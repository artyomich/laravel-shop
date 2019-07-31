<?php
/**
 * @author Artyom Arifulin
 */
namespace components\import;

use Cartalyst\Sentry\Users\Eloquent\User;
use components\BaseImport;
use models\Users;


/**
 *
 * @package components\import
 */
class ContractorsImport extends BaseImport
{
    protected $enableExport = false;
    /**
     * @inheritdoc
     */
    public static function run($className = self::class)
    {
        return parent::run($className);
    }

    public function mergeArrays($arrays, $field)
    {
        //take array in arrays for retrive structure after merging
        $clean_array = current($arrays);
        foreach ($clean_array as $i => $value) {
            $clean_array[$i]='';
        }

        $merged_array = [];
        $name = '';
        foreach ($arrays as $array){
            $array = array_filter($array); //clean array from empty values
            if ($name == $array[$field]) {
                $merged_array[$name] = array_merge($merged_array[$name], $array);
                $name = $array[$field];
            } else {
                $name = $array[$field];
                $merged_array[$name] = $array;
            }
        }
        //have to be cleaned from array 'field' signs to return original structure of arrays
        foreach ($merged_array as $array){
            $ready_array[] = array_merge($clean_array, $array);
        }

        return $ready_array;
    }

    /**
     * @inheritdoc
     */
    protected function import($data)
    {
        $errors = [];
        $importedIDS1C = [];

        if (isset($data->import)) {

            $result_array = $this->mergeArrays($data->import,'UIN Контрагента');
            foreach ($result_array as $itemData) {
                $is_user_found = 0;

                //try to find by UIN 1c
                $user = Users::where('id_1c', '=', $itemData['UIN Контрагента'])->first();

                //fix email to lower case
                $email = strtolower($itemData['email']);

                //email have to be validated
                if (strpos($email, '@') == false) { //if not contain @, then write error and continue to other elements
                    $errors['E-mail ' . $email . ' is invalid for contractor: ' . $itemData['Наименование'] . '.'];
                    continue;
                }

                if ($user == null) { //If not found by uin, then try to find by email
                    $user = Users::where('email', '=', $email)->first();
                    if ($user != null) {//user is found by email
                        $is_user_found = 1;
                    }
                } else {
                    $is_user_found = 1;
                }

                $registration = [
                    'email' => $email,
                    'password' => str_random(8),
                    'first_name' => $itemData['Контактное лицо'],
                    'firm' => $itemData['Наименование'],
                    'is_firm' => 't',
                    'inn' => $itemData['ИНН'],
                    'kpp' => $itemData['КПП'],
                    'address' => $itemData['Адрес'],
                    'rs' => $itemData['р/с'],
                    'bik' => $itemData['БИК'],
                    'bank' => $itemData['Банк'],
                    'phone' => $itemData['Телефон'],
                    'type' => 'firm',
                    'city_id' => !is_numeric($itemData['City_id_договор']) ? '1' : $itemData['City_id_договор'],
                    'activated' => true];

                if ($is_user_found != 1) { //not found user
                    $user = \Sentry::createUser($registration);
                    $clientGroup = \Sentry::findGroupByName('Клиенты');
                    $user->addGroup($clientGroup);

                    $toCSV[] = ['email' => $registration['email'], 'firm' => $registration['firm'], 'city_id' => $registration['city_id'], 'password' => $registration['password']];

                    $importedIDS1C[] = $itemData['Наименование'];
                } else { //found by UID or email
                    $registration = array_except($registration, ['password']); //do not update password for existing user
                    $user = \Sentry::findUserById($user->id);
                    $user->fill($registration); //don't work
                    $user->save();

                    $toCSV[] = ['email' => $registration['email'], 'firm' => $registration['firm'], 'city_id' => $registration['city_id'], 'password' => ''];

                    $importedIDS1C[] = $itemData['Наименование'];
                }

                unset($is_user_found);
                unset($user);

            }

        } else {
            $errors['No correct data provided!'];
        }

        if (count($errors)) {
            $emailMessage = '<strong>Данные обновлены с ошибками :(</strong><br>' .
                'Обновлено: ' . count($importedIDS1C) . '<br>' .
                'Ошибки: ' . count($errors) . '<br>' .
                'Дополнительная информация для отдела разработки:<br><small>' .
                implode('<br>', is_array($errors) ? $errors : []) . '</small>';
        } else {
            $emailMessage = '<strong>Данные импортированы успешно!</strong><br>' .
                'Импортировано: ' . count($importedIDS1C);
        }

        if (!\App::environment('local')) {
            \Mail::send(
                'emails/1c-import',
                [
                    'text' => $emailMessage
                ],
                function ($message) {
                    $message
                        ->to(\Config::get('mail.addresses.1cImport'))
                        ->subject('Отчет о выполнении скрипта апдейта данных');
                }
            );
        }
        $this->saveToXls($toCSV);

        return $emailMessage;
    }

    public function saveToXls($toCSV)
    {
         \Excel::create('contractors', function ($excel) use ($toCSV) {
             $excel->sheet('Лист1', function ($sheet) use ($toCSV) {
                 $sheet->appendRow(['email', 'Наименование', 'Пароль', 'Город']);
                 foreach ($toCSV as $c) {
                     $sheet->appendRow([
                         $c['email'],
                         $c['firm'],
                         $c['password'],
                         $c['city_id']
                     ]);
                 }
             }
             );
         }
         )->download('xls');
    }

}