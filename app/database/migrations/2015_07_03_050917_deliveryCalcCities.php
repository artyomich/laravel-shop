<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeliveryCalcCities extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {

        $states = [
            'Москва' => [
                'Балашиха',
                'Королев',
                'Химки',
                'Подольск',
                'Люберцы',
                'Мытищи',
                'Электросталь',
                'Коломна',
                'Одинцово',
                'Красногорск'
            ],
            'Нижний Новгород' => [
                'Дзержинск',
                'Арзамас',
                'Саров',
                'Бор',
                'Кстово',
                'Павлово',
                'Выкса',
                'Балахна',
                'Заволжье',
                'Богородск'
            ],
            'Новокузнецк' => [
                'Кемерово',
                'Прокопьевск',
                'Ленинск-Кузнецкий',
                'Междуреченск',
                'Киселевск',
                'Юрга',
                'Анжеро-Судженск',
                'Белово',
                'Березовский',
                'Осинники'
            ],
            'Новосибирск' => [
                'Бердск',
                'Искитим',
                'Куйбышев',
                'Барабинск',
                'Карасук',
                'Обь',
                'Татарск',
                'Тогучин',
                'Краснообск',
                'Линево'
            ],
            'Ставрополь' => [
                'Пятигорск',
                'Кисловодск',
                'Невинномысск',
                'Ессентуки',
                'Михайловск',
                'Минеральные Воды',
                'Георгиевск',
                'Буденновск',
                'Изобильный',
                'Светлоград'
            ],
            'Волгоград' => [
                'Волжский',
                'Камышин',
                'Михайловка',
                'Урюпинск',
                'Фролово',
                'Калач-на-Дону',
                'Котово',
                'Городище',
                'Котельниково',
                'Суровикино'
            ],
            'Челябинск' => [
                'Магнитогорск',
                'Златоуст',
                'Миасс',
                'Копейск',
                'Озерск',
                'Троицк',
                'Снежинск',
                'Сатка',
                'Чебаркуль',
                'Кыштым'
            ],
            'Белгород' => [
                'Старый Оскол',
                'Губкин',
                'Шебекино',
                'Алексеевка',
                'Валуйки',
                'Строитель',
                'Новый Оскол',
                'Разумное',
                'Чернянка',
                'Борисовка'
            ],
            'Красноярск' => [
                'Норильск',
                'Ачинск',
                'Канск',
                'Железногорск',
                'Минусинск',
                'Зеленогорск',
                'Сосновоборск',
                'Шарыпово',
                'Назарово',
                'Лесосибирск'
            ],
            'Екатеринбург' => [
                'Нижний Тагил',
                'Каменск-Уральский',
                'Первоуральск',
                'Серов',
                'Новоуральск',
                'Асбест',
                'Верхняя Пышма',
                'Полевской',
                'Ревда',
                'Краснотурьинск'
            ],
            'Томск' => [
                'Северск',
                'Стрежевой',
                'Асино',
                'Колпашево',
                'Белый Яр',
                'Мельниково',
                'Кожевниково',
                'Каргасок',
                'Светлый',
                'Александровское'
            ],
            'Иркутск' => [
                'Братск',
                'Ангарск',
                'Усть-Илимск',
                'Усолье-Сибирское',
                'Черемхово',
                'Шелехов',
                'Усть-Кут',
                'Тулун',
                'Саянск',
                'Нижнеудинск'
            ],
            'Барнаул' => [
                'Бийск',
                'Рубцовск',
                'Новоалтайск',
                'Заринск',
                'Камень-на-Оби',
                'Славгород',
                'Алейск',
                'Южный',
                'Тальменка',
                'Яровое'
            ]
        ];
        \Schema::table('cities', function (Blueprint $table) {
            $table->integer('cdek_id')->unique()->nullable();
        });
        \Schema::create('cities_states_delivery', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('city_id');
            $table->string('name');
        });
        $cdek_cities = \helpers\CsvToArray::toArray(app_path() . '/database/seeds/cdek_city_rus_20150603.csv', ';');
        foreach ($states as $cityName => $cities) {
            $city = \models\Cities::where(['name' => $cityName])->first();
            if (!$city) {
                throw new Exception('Город не найден в БД');
            }
            $city->cdek_id = $this->getCdekID($cdek_cities, $cityName, trans('cities.obl_short.nominative.' . $cityName));
            $city->save();
            foreach ($cities as $name) {
                $model = new \modules\deliverycalc\models\CitiesStatesDelivery;
                $model->city_id = $city->id;
                $model->name = $name;
                $model->id = $this->getCdekID($cdek_cities, $name, trans('cities.obl_short.nominative.' . $cityName));
                if (!$model->save()) {
                    throw new Exception('Не удалось сохранить модель');
                }
            }
        }
    }

    private function getCdekID($cdek_cities, $city, $obl) {
        foreach ($cdek_cities as $cdekCity) {
            if ($cdekCity['CityName'] === $city AND $cdekCity['OblName'] === $obl) {
                return $cdekCity['ID'];
            }
        }
        foreach ($cdek_cities as $cdekCity) {
            if ($cdekCity['CityName'] === $city AND $cdekCity['OblName'] === $city) {
                return $cdekCity['ID'];
            }
        }
        return null;
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        \Schema::drop('cities_states_delivery');
        \Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn('cdek_id');
        });
    }

}
