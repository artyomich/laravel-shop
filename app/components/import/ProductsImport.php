<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace components\import;

use components\ActiveRecord;
use components\BaseImport;
use components\UploadedFile;
use helpers\ArrayHelper;
use models\Categories;
use models\Images;
use models\Products;
use models\ProductsImagesRelation;
use models\ProductsProperties;
use models\YandexMarket;

/**
 * Родительский класс для всех импортов.
 *
 * @package components\import
 */
class ProductsImport extends BaseImport
{
    /**
     * @var
     */
    private $_result = '';

    /**
     * @var array[ActiveRecord]
     */
    private $_cities = [];

    /**
     * @var array[ActiveRecord]
     */
    private $_categories = [];

    /**
     * @var array[ActiveRecord]
     */
    private $_properties = [];

    /**
     * @var array поля, которые импортируются вручную.
     */
    private $_blackListProperies
        = [
            'Код',
            'Изображение 1 (малое)',
            'Изображение 2 (большое)',
            'Марка',
            'Группа шин',
            'Группа',
            'Префикс',
            'Сортировка',
            'Title',
            'Keywords',
            'Description',
            'Назначение',
            'Описание',
            'На главную',
            'Столбец1'
        ];

    protected $enableExport = false;

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
        $this->_cities = \models\Cities::all();

        $errors = [];
        $ids1C = [];            //  id продуктов из 1с, которые были импортированы с ошибками.
        $importedIDS1C = [];
        $progress = 0;
        $filter = [];           //  Данные для формирования фильтра на главной.
        $filterDataDisk = [
            'ТипДиска' => [],
            'Сверловка' => [],
            'ДиаметрDIA' => [],
            'Диаметр' => [],
            'Вылет' => [],
            'Ширина' => [],
            'Бренд' => [],
        ];
        $filterData = [
            'Диаметр' => [],
            'Ширина' => [],
            'Высота' => [],
            'Бренд' => [],
            'Рисунок' => [],
        ];
        $filterDataByCategories = [];

        foreach ($data->import as $k => $itemData) {
            $product = Products::where('id_1c', '=', $itemData['Код'])->first();
            if (!isset($product)) {
                $product = new Products;
                $product->id_1c = $itemData['Код'];
            }

            //  FIXME: Кастыль >_<, т.к. группы называются например не "Грузовые", а "Грузовые шины".
            //  -------------------------------------------------------------------------------------
            $itemData['Группа шин'] = trim($itemData['Группа шин']);
            if (in_array($itemData['Группа шин'], ['Шины для сельхозтехники', 'Шины Для Сельхозтехники'])) {
                $itemData['Группа шин'] = 'Сельскохозяйственные';
            } else {
                if (in_array($itemData['Группа шин'], ['Грузовые Шины', 'Грузовые шины'])) {
                    $itemData['Группа шин'] = 'Грузовые';
                } else {
                    if (in_array($itemData['Группа шин'], ['Легкогрузовые Шины', 'Легкогрузовые шины'])) {
                        $itemData['Группа шин'] = 'Легкогрузовые';
                    } else {
                        if (in_array($itemData['Группа шин'], ['Легковые Шины', 'Легковые шины'])) {
                            $itemData['Группа шин'] = 'Легковые';
                        } else {
                            if (in_array($itemData['Группа шин'], ['Индустриальные шины и КГШ'])) {
                                $itemData['Группа шин'] = 'Индустриальные и КГШ';
                            }
                        }
                    }
                }
            }
            //  -------------------------------------------------------------------------------------

            //  Добываем категорию если такой нету.
            /** @var Categories $category */
            if (!isset($this->_categories[$itemData['Группа шин']])) {
                $category = Categories::where('name', '=', $itemData['Группа шин'])->first();
                if (!isset($category)) {
                    $errors[] = 'Ошибка при импорте. Категория не найдена';
                    continue;
                }

                $this->_categories[$itemData['Группа шин']] = $category;
            }

            //  Сохранение информации о продукте.
            $status = $this->saveProduct($product, $this->_categories[$itemData['Группа шин']]->id, $itemData, $this->_categories[$itemData['Группа шин']]->type);
            if ($status !== true) {
                $product->alias .= '_' . $product->id;
                if (!$product->save()) {
                    $errors[] = print_r($status, true);
                    $ids1C[] = $itemData['Код'];
                    if (!$product->isNewRecord()) {
                        $product->delete();
                    }
                    continue;
                }
            };

            //  Сохранение свойств продукта.
            $status = $this->saveProperties($product, $itemData, $this->_categories[$itemData['Группа шин']]->type);
            if ($status !== true) {
                $errors[] = print_r($status, true);
                $ids1C[] = $itemData['Код'];
                $product->delete();
                continue;
            };

            //  Сохранение цен и остатков.
            $status = $this->saveBalances($product, $itemData);
            if (!$product || $status !== true) {
                $errors[] = print_r($status, true);
                $ids1C[] = $itemData['Код'];
                $product->delete();
                continue;
            };

            //  Сораняем фотографии если продукт ранее не загружали.
            $status = $this->savePhotos($product, $itemData);
            if ($status !== true) {
                $errors[] = print_r($status, true);
                $ids1C[] = $itemData['Код'];
                $product->delete();
                continue;
            };

            \Cache::flush();

            //  Фильтр.
            switch ($this->_categories[$itemData['Группа шин']]->type) {
                case Categories::TYPE_TIRES:
                    $this->cacheFilterTires($filter, $filterData, $filterDataByCategories, $itemData);
                    break;
                case Categories::TYPE_DISKS:
                    $this->cacheFilterDisk($itemData, $filterDataDisk);
                    break;
                default:
                    continue;
            }

            unset($product);

            $importedIDS1C[] = $itemData['Код'];

            if (!$this->is1C()) {
                //  Прогресс.
                if (!ob_start("ob_gzhandler")) {
                    ob_start();
                }

                ++$progress;
                echo '[' . $progress . '] Импортировано ' . $itemData['Код'] . '<br/>';
                ob_get_contents();

                ob_end_flush();

                echo str_repeat(' ', 65536);
            }
        }

        //  Уникальность.
        //  TODO: Переделать масисвы под $filterData['Тип']['Диаметр']...
        foreach ($filterData as $key => $item) {
            $filterData[$key] = array_unique($filterData[$key]);
        }
        foreach ($filterDataDisk as $key => $item) {
            $filterDataDisk[$key] = array_values(array_unique($filterDataDisk[$key]));
            sort($filterDataDisk[$key]);
        }
        
        \Cache::flush();
        
        //  FIXME: Убрать все в кеш. Сделать по аналогичной схеме, что и rememberForever ниже, только записывать в цикле.
        //  Сохраним фильтр в файл.
        file_put_contents(
            app_path() . '/config/filterData.php', '<?php return ' . var_export($filterData, true) . '; ?>'
        );
        file_put_contents(
            app_path() . '/config/filterDataByCategories.php', '<?php return ' .
            var_export($filterDataByCategories, true) . '; ?>'
        );
        file_put_contents(app_path() . '/config/filter.php', '<?php return ' . var_export($filter, true) . '; ?>');
        file_put_contents(app_path() . '/config/filterDisk.php', '<?php return ' . var_export($filterDataDisk, true) . '; ?>');

        //  Генерация карты сайта.
        file_get_contents('http://poshk.ru/sitemap/');

        //	Обнорвление файлов для выгрузку в маркет.
        YandexMarket::updateXml();

        //  Последний штрих - делаем невидимыми все, что не были импортированы.
        $products = Products::whereNotIn('id_1c', $importedIDS1C);
        $products->update(['is_visible' => false]);

        //  Обнуление остатков.
        \DB::table('products_balances')
            ->whereIn('product_id', array_keys(ArrayHelper::map($products->get(), 'id', 'id')))
            ->update([
                'cost' => 0,
                'balance' => 0
            ]);

        //  Удаляем все закешированные изображения.
        $resizedDirName = app_path() . '/../public/files/images/products/resized';
        $objects = scandir($resizedDirName);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($resizedDirName . "/" . $object) != "dir") {
                    unlink($resizedDirName . "/" . $object);
                }
            }
        }

        if (count($errors)) {
            $emailMessage = '<strong>Данные импортированы с ошибками :(</strong><br/>' .
                'Импортировано: ' . count($importedIDS1C) . '<br/>' .
                'Ошибки: ' . count($errors) . '<br/>' .
                '1C IDS: ' . implode(', ', $ids1C) . '<br/>' .
                'Дополнительная информация для отдела разработки:<br/><small>' .
                implode('<br/>', is_array($errors) ? $errors : []) . '</small>';
        } else {
            $emailMessage = '<strong>Данные импортированы успешно!</strong><br/>' .
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
                        ->subject('Отчет о выполнении скрипта испорта данных');
                }
            );
        }

        if (!$this->is1C()) {
            \Session::flash(count($errors) ? 'error' : 'success', $emailMessage);
            return \Redirect::to('/admin/import/');
        } else {
            return 'Не импортировано: ' . count($errors);
        }
    }

    /**
     * Обновит информацию о продукте.
     *
     * @param Products $product
     * @param integer $categoryId
     * @param array $itemData
     * @param $type
     *
     * @return bool|string
     */
    private function saveProduct(&$product, $categoryId, $itemData, $type)
    {
        //  Основная инофомация.
        $name = (empty($itemData['Марка']) ? $itemData['Бренд'] : $itemData['Марка']) . ' ' .
            $itemData['Модель'] . ' ' . $itemData['Типоразмер'];
        $short = (empty($itemData['Марка']) ? $itemData['Бренд'] : $itemData['Марка']) . ' ' . $itemData['Модель'];
        $product->setAttributes(
            [
                'category_id' => $categoryId,
                'name' => $name,
                'name_short' => $short,
                'alias' => $this->generateAlias($product, $itemData, $type),
                'is_visible' => 'TRUE',
                'is_on_index' => !empty($itemData['На главную']) ? 'TRUE' : 'FALSE',
                'aim_use' => $itemData['Назначение'],
                'sorting' => isset($itemData['Сортировка']) ? $itemData['Сортировка'] : null
            ]
        );

        return $product->save() ? true : $product->getErrors();
    }

    /**
     * Сохранит свойства товара.
     *
     * @param $product
     * @param $itemData
     * @param $type
     * @return bool
     */
    private function saveProperties(&$product, &$itemData, $type)
    {
        //  Стираем все.
        \DB::statement(\DB::raw("DELETE FROM products_properties WHERE product_id = " . $product->id));

        $properties = new ProductsProperties;
        $properties->product_id = $product->id;

        $attributes = [
            //  Шины.
            'brand' => 'Бренд',
            'manufacturer' => 'Производитель',
            'model' => 'Модель',
            'size' => 'Типоразмер',
            'series' => 'Серия профиля, %',
            'layouts_normal' => 'Норма слойности',
            'index_speed' => 'Индекс скорости',
            'index_load' => 'Индекс нагрузки',
            'season' => 'Сезон',
            'spikes' => 'Шипы',
            'image_axis' => 'Рисунок/ось',
            'completeness' => 'Комплектность',
            'camera' => '',

            //  Диски.
            'construction' => 'ТипДиска',
            'offset' => 'Вылет',
            'drilling' => 'Сверловка',
            'diameter_inside' => 'ДиаметрDIA',
        ];

        foreach ($attributes as $k => $v) {
            $properties->$k = trim(ArrayHelper::getValue($itemData, $v, ''));
        }

        $properties->diameter_inch = trim(str_replace('.', ',', $itemData['Посадочный диаметр, дюймы']));
        $properties->diameter_mm = trim(str_replace(' ', '', $itemData['Посадочный диаметр, мм']));
        $properties->width_inch = trim(str_replace('.', ',', $itemData['Ширина профиля, дюймы']));
        $properties->width_mm = trim(str_replace(' ', '', $itemData['Ширина профиля, мм']));
        $properties->diameter_outside = trim(str_replace(' ', '', $itemData['Наружный диаметр, мм.']));
        return $properties->save();
    }

    /**
     * Сохраненит остатки и стоимости шин в разных городах.
     *
     * @param ActiveRecord $product
     * @param array $data
     *
     * @return bool
     */
    public function saveBalances(&$product, $data)
    {
        \DB::statement(\DB::raw("DELETE FROM products_balances WHERE vendor_id is null and product_id = " . $product->id));

        $queriesData = [];

        foreach ($this->_cities as $city) {
            $cost = isset($data['Цена ' . $city->name]) ? (int)str_replace(' ', '', $data['Цена ' . $city->name]) : 0;
            $cost_opt_small = isset($data['Цена мелк.опт ' . $city->name]) ? (int)str_replace(' ', '', $data['Цена мелк.опт ' . $city->name]) : 0;
            $balance = isset($data['Остатки свободные ' . $city->name]) ? (int)$data['Остатки свободные ' . $city->name] : 0;
            $balance_full = isset($data['Остатки ' . $city->name]) ? (int)$data['Остатки ' . $city->name] : 0;
            $isSpec = (isset($data['Акция ' . $city->name]) && $data['Акция ' . $city->name] == '1') ? 't' : 'f';

            if ($cost) {
                $queriesData[] = "(" . $city->id . ", " . $product->id . ", $cost, $cost_opt_small, $balance, '$isSpec', $balance_full)";
            }

            unset($cost);
            unset($balance);
        }

        if (!count($queriesData)) {
            $product->delete();
        } else {
            \DB::statement(
                \DB::raw(
                    "INSERT INTO products_balances (city_id, product_id, cost, cost_opt_small, balance, is_spec_cost, balance_full)" .
                    "VALUES" . implode(', ', $queriesData) . ";"
                )
            );
        }

        unset($queriesData);

        return true;
    }

    /**
     * Сохранит фотографии шины.
     *
     * @return bool
     */
    public function savePhotos(&$product, $data)
    {
        $path = $this->importDir . 'images/' . $data['Изображение 2 (большое)'];
        if (!is_file($path) || $data['Изображение 2 (большое)'] == '.') {
            //  Если фото нет, и есть фото у товара, удаляем старое.
            if (count($product->images) && isset($product->images[0])) {
                $sourceName = public_path() . '/files/images/' . str_replace(':', '/originals/', $product->images[0]->filename);
                if (!is_file($sourceName)) {
                    $product->images[0]->delete();
                }
            }
            return true;
        }

        $imgRel = ProductsImagesRelation::where(['product_id' => $product->id])->get();
        $image = count($imgRel) && $imgRel[0]->image ? $imgRel[0]->image : new Images;

        if (is_file($path)) {
            $image->file = new UploadedFile($path, $data['Изображение 2 (большое)'], null, null, null, true);
        }

        $image->productId = $product->id;
        $image->scenario = Images::SCENARIO_PRODUCTS;
        $image->filename = substr($product->alias, 0, 48) . '.' . $image->file->getClientOriginalExtension();
        if (!$image->save()) {
            return $image->getErrors();
        }

        unset($image);

        return true;
    }

    /**
     * Кеширует данные фильтра для определенного города.
     *
     * @param $itemData
     */
    public function cacheFilterTires(&$filter, &$filterData, &$filterDataByCategories, &$itemData)
    {
        //  Сначала посмотрим остатки в разных городах.
        foreach ($this->_cities as $city) {
            $cost = isset($itemData['Цена ' . $city->name]) ? (int)str_replace(
                ' ', '', $itemData['Цена ' . $city->name]
            ) : 0;
            $balance = isset($itemData['Остатки ' . $city->name]) ? (int)str_replace(
                ' ', '', $itemData['Остатки ' . $city->name]
            ) : 0;
            if ($cost && $balance) {
                if (!isset($filter[$city->id])) {
                    $filter[$city->id] = [
                        'Диаметр' => [],
                        'Ширина' => [],
                        'Высота' => [],
                        'Бренд' => [],
                        'Рисунок' => []
                    ];
                    $filterDataByCategories[$city->id] = [];
                }

                $categoryId = $this->_categories[$itemData['Группа шин']]->id;
                if (!isset($filterDataByCategories[$city->id][$categoryId])) {
                    $filterDataByCategories[$city->id][$categoryId] = [
                        'Диаметр' => [],
                        'Ширина' => [],
                        'Высота' => [],
                        'Бренд' => [],
                        'Рисунок' => []
                    ];
                }

                //  Диаметр. Тут все кешируем с точками.
                $key = str_replace(',', '.', $itemData['Посадочный диаметр, дюймы']);
                if ($key) {
                    if (!isset($filter[$city->id]['Диаметр'][$key])) {
                        $filter[$city->id]['Диаметр'][$key] = [];
                    }

                    //  Запихиваем сюда ширину т.е. образовываем связку диаметр => ширина.
                    $filter[$city->id]['Диаметр'][$key][] = str_replace(',', '.', trim($itemData['Ширина профиля, дюймы']));
                    $filter[$city->id]['Диаметр'][$key][] = $itemData['Ширина профиля, мм'];
                }

                //  Теперь тоже самое, но для "Посадочный диаметр, мм".
                $key = $itemData['Посадочный диаметр, мм'];
                if (!isset($filter[$city->id]['Диаметр'][$key])) {
                    $filter[$city->id]['Диаметр'][$key] = [];
                }

                if ($key) {
                    $filter[$city->id]['Диаметр'][$key][] = trim($itemData['Ширина профиля, дюймы']);
                    $filter[$city->id]['Диаметр'][$key][] = trim($itemData['Ширина профиля, мм']);
                }

                if (isset($filter[$city->id]['Диаметр'][$key])) {
                    $filter[$city->id]['Диаметр'][$key] = array_unique(
                        $filter[$city->id]['Диаметр'][$key]
                    );
                }


                //  Ширина.
                //  Тут серию профиля бывают устанавливают в 0, по этому мы такие значения просто исключаем.
                //  Есть 82, но о них не знают, по этому их ставят в 0.
                $val = trim($itemData['Серия профиля, %']);
                $key = str_replace(',', '.', trim($itemData['Ширина профиля, дюймы']));
                if (!isset($filter[$city->id]['Ширина'][$key])) {
                    $filter[$city->id]['Ширина'][$key] = [];
                }

                if ($key && $key != '0') {
                    //  Запихиваем сюда ширину т.е. образовываем связку ширина => высота.
                    $filter[$city->id]['Ширина'][$key][] = $val;
                }

                //  Теперь тоже самое, но для "Посадочный диаметр, мм".
                $key = trim($itemData['Ширина профиля, мм']);
                if ($key && $key != '0') {
                    $filter[$city->id]['Ширина'][$key][] = $val;
                }

                if (isset($filter[$city->id]['Ширина'][$key])) {
                    $filter[$city->id]['Ширина'][$key] = array_unique($filter[$city->id]['Ширина'][$key]);
                }

                //  Высота.
                $key = $val;
                if ($key && $key != '0') {
                    if (!isset($filter[$city->id]['Высота'][$key])) {
                        $filter[$city->id]['Высота'][$key] = [];
                    }

                    $filter[$city->id]['Высота'][$key][] = trim($itemData['Бренд']);
                    $filter[$city->id]['Высота'][$key] = array_unique($filter[$city->id]['Высота'][$key]);
                }

                //  Бренд.
                $key = $itemData['Бренд'];
                if ($key) {
                    if (!isset($filter[$city->id]['Бренд'][$key])) {
                        $filter[$city->id]['Бренд'][$key] = [];
                    }

                    $filter[$city->id]['Бренд'][$key][] = trim($itemData['Рисунок/ось']);

                    $filter[$city->id]['Бренд'][$key] = array_unique($filter[$city->id]['Бренд'][$key]);
                }

                //  Рисунок.
                $key = $itemData['Рисунок/ось'];
                if ($key) {
                    if (!isset($filter[$city->id]['Рисунок'][$key])) {
                        $filter[$city->id]['Рисунок'][$key] = [];
                    }
                    $filter[$city->id]['Рисунок'][$key][] = trim($key);
                }

                //  Данные фильтра.
                $filterData['Диаметр'][] = str_replace(',', '.', str_replace(' ', '', trim($itemData['Посадочный диаметр, дюймы'])));
                $filterData['Диаметр'][] = str_replace(' ', '', trim($itemData['Посадочный диаметр, мм']));
                $filterData['Ширина'][] = str_replace(',', '.', str_replace(' ', '', trim($itemData['Ширина профиля, дюймы'])));
                $filterData['Ширина'][] = str_replace(' ', '', trim($itemData['Ширина профиля, мм']));
                $filterData['Высота'][] = str_replace(' ', '', trim($itemData['Серия профиля, %']));
                $filterData['Бренд'][] = str_replace(' ', '', trim($itemData['Бренд']));
                $filterData['Рисунок'][] = str_replace(' ', '', trim($itemData['Рисунок/ось']));

                $filterDataByCategories[$city->id][$categoryId]['Диаметр'][] = trim(
                    str_replace(' ', '', $itemData['Посадочный диаметр, дюймы'])
                );
                $filterDataByCategories[$city->id][$categoryId]['Диаметр'][] = trim(
                    str_replace(' ', '', $itemData['Посадочный диаметр, мм'])
                );
                $filterDataByCategories[$city->id][$categoryId]['Ширина'][] = trim(
                    str_replace(' ', '', $itemData['Ширина профиля, дюймы'])
                );
                $filterDataByCategories[$city->id][$categoryId]['Ширина'][] = trim(
                    str_replace(' ', '', $itemData['Ширина профиля, мм'])
                );
                $filterDataByCategories[$city->id][$categoryId]['Высота'][] = trim($itemData['Серия профиля, %']);
                $filterDataByCategories[$city->id][$categoryId]['Бренд'][] = trim($itemData['Бренд']);
                $filterDataByCategories[$city->id][$categoryId]['Рисунок'][] = trim($itemData['Рисунок/ось']);

                $filterDataByCategories[$city->id][$categoryId]['Диаметр'] = array_unique(
                    $filterDataByCategories[$city->id][$categoryId]['Диаметр']
                );
                $filterDataByCategories[$city->id][$categoryId]['Диаметр'] = array_unique(
                    $filterDataByCategories[$city->id][$categoryId]['Диаметр']
                );
                $filterDataByCategories[$city->id][$categoryId]['Ширина'] = array_unique(
                    $filterDataByCategories[$city->id][$categoryId]['Ширина']
                );
                $filterDataByCategories[$city->id][$categoryId]['Ширина'] = array_unique(
                    $filterDataByCategories[$city->id][$categoryId]['Ширина']
                );
                $filterDataByCategories[$city->id][$categoryId]['Высота'] = array_unique(
                    $filterDataByCategories[$city->id][$categoryId]['Высота']
                );
                $filterDataByCategories[$city->id][$categoryId]['Бренд'] = array_unique(
                    $filterDataByCategories[$city->id][$categoryId]['Бренд']
                );
                $filterDataByCategories[$city->id][$categoryId]['Рисунок'] = array_unique(
                    $filterDataByCategories[$city->id][$categoryId]['Рисунок']
                );
            }
        }
    }

    /**
     * Выполнит кеширование фильтра дисков.
     *
     * @param array $itemData
     * @param array $filterData
     */
    private function cacheFilterDisk(&$itemData, &$filterData)
    {
        foreach ($this->_cities as $city) {
            $cost = (int)str_replace(' ', '', ArrayHelper::getValue($itemData, 'Цена ' . $city->name, ''));
            $balance = (int)str_replace(' ', '', ArrayHelper::getValue($itemData, 'Остатки ' . $city->name, ''));

            if ($cost && $balance) {
                $filterData['ТипДиска'][] = trim($itemData['ТипДиска']);
                $filterData['ДиаметрПосадочный'][] = trim($itemData['Посадочный диаметр, дюймы']);
                $filterData['ДиаметрDIA'][] = trim($itemData['ДиаметрDIA']);
                $filterData['Сверловка'][] = trim($itemData['Сверловка']);
                $filterData['Вылет'][] = trim($itemData['Вылет']);
                $filterData['Ширина'][] = trim($itemData['Ширина профиля, дюймы']);
                $filterData['Бренд'][] = trim($itemData['Бренд']);  // Используем марку вместо брендов.
            }
        }
    }

    /**
     * Сгенерирует алиас для товара по параметрам.
     *
     * @param \models\Products $product
     * @param array $itemData
     * @param $type
     * @return bool|mixed|string
     */
    private function generateAlias(&$product, $itemData, $type)
    {
        //  Если запись существует, то не менять у нее alias.
        if (!$product->isNewRecord()) {
            return $product->alias;
        }

        $alias = '';
        switch ($type) {
            case Categories::TYPE_TIRES:
                $alias = $itemData['Бренд'] . ' ' . $itemData['Модель'] . ' ' . $itemData['Типоразмер'];
                break;
            case Categories::TYPE_DISKS:
                $alias = $itemData['Марка'] . ' ' . $itemData['Модель'] . ' ' . $itemData['Типоразмер'];
                break;
        }

        $alias .= !empty($itemData['Норма слойности'])
            ? (' ' . $itemData['Норма слойности'])
            : (' ' . $product->id);

        $alias = strip_tags($alias);
        $alias = \helpers\StringHelper::translit($alias);
        $alias = str_replace(['.', ' ', '*', '/'], '_', $alias);
        //$alias = str_replace(['R'], '', $alias);
        return mb_strtolower($alias, 'utf8');
    }
}