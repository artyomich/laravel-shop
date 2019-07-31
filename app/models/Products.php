<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace models;

use components\ActiveRecord;
use helpers\ArrayHelper;
use helpers\Html;
use Illuminate\Database\Eloquent\Builder;
use \modules\deliverycalc\models\TyresWeight;

/**
 * ActiveRecord таблицы `products`.
 *
 * @package models
 */
class Products extends \components\ActiveRecord
{
    /**
     * @var string название таблицы.
     */
    protected $table = 'products';

    /**
     * @var array загруженные изображения.
     */
    public $uploaded = [];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    /*protected $fillable = [
        'id', 'name', 'alias', 'category_id', 'meta_title', 'meta_description', 'meta_keywords', 'stock', 'cost', 'id_1c',
        'is_on_index', 'description', 'is_visible'
    ];*/

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'               => 'ID',
            'name'             => 'Название',
            'alias'            => 'Псевдоним',
            'category_id'      => 'Категория',
            'meta_title'       => 'Meta-title',
            'meta_description' => 'Meta-description',
            'meta_keywords'    => 'Meta-keywords',
            'stock'            => 'Количество на складе',
            'cost'             => 'Стоимость',
            'id_1c'            => 'ID 1C',
            'is_on_index'      => 'На главной',
            'description'      => 'Описание',
            'is_visible'       => 'Выводить в каталоге'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'required|min:5'],
            ['alias', 'required|unique:products,alias']
        ];
    }

    /**
     * @inheritdoc
     */
    public function defaultColumns()
    {
        return [
            'id_1c',
            'name',
            'category_id',
            '_actions'
        ];
    }

    /**
     * @inheritdoc
     */
    public function columns()
    {
        $columns = $this->defaultColumns();
        $result = [];
        $isDoubles = (bool)\Input::get('showDoubles');

        foreach ($columns as $column) {
            switch ($column) {
                case 'id_1c':
                    $result[$column] = $this->getColumn(
                        $column, [
                            //'filter' => Html::textInput($column, '', ['class' => 'form-control']),
                            'filter' => $isDoubles ? null : Html::activeTextInput($this, $column, ['class' => 'form-control']),
                            'class' => 'col-xs-1'
                        ]
                    );
                    break;
                case 'name':
                    $result[$column] = $this->getColumn(
                        $column, [
                            'value' => '<a href="/admin/products/update/{$data->id}">{$data->$column}</a>',
                            'filter' => $isDoubles ? null : Html::activeTextInput($this, $column, ['class' => 'form-control']),
                            'class' => 'col-xs-8'
                        ]
                    );
                    break;
                case 'category_id':
                    $result[$column] = $this->getColumn(
                        $column, [
                            'value' => '{$data->getCategoryName()}',
                            'filter' => $isDoubles ? null : Html::activeDropDownList(
                                $this, $column,
                                ArrayHelper::map(Categories::all(), 'id', 'name'),
                                ['class' => 'form-control', 'prompt' => '']
                            ),
                        ]
                    );
                    break;
                case '_actions':
                    $result[$column] = $this->getColumn($column);
                    break;
            }
        }

        return $result;
    }

    /**
     * FIXME: Переделать. Никаких $this->categories()->first(). Сделать одной стокой, доработвть связи.
     *
     * @return string вернет название категории этого товара.
     */
    public function getCategoryName()
    {
        $categories = $this->categories()->first();
        return !empty($categories) ? $categories->name : '';
    }

    /**
     * Заготовка для вывода продуктов в каталоге.
     *
     * @param integer $categoryId
     *
     * @return mixed
     */
    public static function getCatalog($categoryId)
    {
        return self::with(['balance', 'images'])
            ->join(
                'products_balances', function ($join) {
                    $join->on('products_balances.product_id', '=', 'products.id');
                }
            )
            ->where('category_id', $categoryId)
            ->where('is_visible', 't')
            ->where('products_balances.cost', '>', 0)
            ->where('products_balances.city_id', '=', \Cookie::get('city_id'))
            ->paginate(20);
    }

    /**
     * Вернет товары для главной странице.
     *
     * @return array[ActiveRecord]
     */
    public static function getAllOnIndex()
    {
        return self::with(['balance', 'images'])
            ->join(
                'products_balances', function ($join) {
                    $join
                        ->on('products_balances.product_id', '=', 'products.id')
                        ->where('products_balances.cost', '>', 0)
                        ->where('products_balances.city_id', '=', \Cookie::get('city_id'));
                }
            )
            ->where('is_on_index', 't')
            ->where('is_visible', 't')
            ->get();
    }

    /**
     * Вернет свойства товара.
     */
    public function properties()
    {
        return $this->hasOne('\models\ProductsProperties', 'product_id')->remember(120);
    }

    /**
     * Вернет цену и сроки доставки
     */
    public function calcDeliveryCost()
    {
        if (count($this->vendorsBalances)) {
            foreach ($this->vendorsBalances as &$item) {
                if (Users::getCdekUser($item->vendor_id) && Users::getCdekUser($item->vendor_id) <> Cities::getCurrentCity()->cdek_id) {
                    $cdek = Products::getCdekPrice([
                        'senderCityId' => Users::getCdekUser($item->vendor_id),
                        'receiverCityId' => Cities::getCurrentCity()->cdek_id,
                        'width_mm' => $this->properties["width_mm"],
                        'width_inch' => $this->properties["width_inch"],
                        'series' => $this->properties["series"],
                        'diameter_inch' => $this->properties["diameter_inch"],
                        'diameter_mm' => $this->properties["diameter_mm"],
                        'diameter_outside' => $this->properties["diameter_outside"],
                    ]);
                    if (isset($cdek->result)) {
                        $cdek = $cdek->result;
                        $item->delivery = $cdek->price;
                        $item->deliveryPeriodMin = $cdek->deliveryPeriodMin;
                        $item->deliveryPeriodMax = $cdek->deliveryPeriodMax;
                    }
                }
                $item->costMarkUp = ceil($item->cost + $item->cost * \models\Markup::where('name', 'Общая наценка')->first()->value / 100);
            }
        }
        return $this;
    }
    /**
     * Вернет товар по псевдониму.
     *
     * @param string $alias псевдоним.
     */
    public static function getByAlias($alias)
    {
        $product = self::with('balance')->where(['alias' => $alias])->first();
        if (!$product) {
            \App::abort(404, 'Товар не найден');
        }
        return $product;
    }

    /**
     * Вернет остатки шин в городе.
     *
     * @param integer $id идентификатор города.
     *
     * @return mixed
     */
    public function getBalanceByCityId($id)
    {
        return $this->belongsTo('\models\ProductsBalances', 'id', 'product_id')
            ->where('city_id', $id)->remember(120)->first();
    }

    /**
     * Вернет остатки шин в текущем городе.
     *
     * @return mixed
     */
    public function getCurrentBalance()
    {
        return $this->belongsTo('\models\ProductsBalances', 'id', 'product_id')
            ->where('city_id', Cities::getCurrentCity()->id)
            ->first();
    }

    /**
     * Вернет сруднюю оценку товара.
     *
     * @return float
     */
    public function getAverageRating()
    {
        $rating = 0;
        $checked = $this->getCheckedOpinions();
        foreach ($checked as $opinion) {
            $rating += $opinion->rating;
        }
        return count($checked) ? round($rating / count($checked)) : 3;
    }

    /**
     * @inheritdoc
     */
    public function afterSave()
    {
        parent::afterSave();

        //  Если не пришли какие то старые фото, то удалим их.
        $uploaded = \Input::get($this->formName());
        $uploaded = isset($uploaded['uploaded']) ? $uploaded['uploaded'] : [];

        //  Делаем невозмодность удаления фото, если не пришли.
        if (empty($uploaded)) {
            return;
        }

        //  Получим все id изображений этого товара.
        $ids = [];
        foreach ($this->images as $image) {
            $ids[] = $image->id;
        }

        //  Получим разницу.
        $diff = array_diff($ids, $uploaded);

        //  Идатим все различные ID т.к. они не пришли.
        foreach ($diff as $id) {
            Images::find($id)->delete();
        }

        //  Если есть новые изображения.
        $files = \Input::file('Images');
        if (isset($files)) {
            foreach ($files as $file) {
                $image = new Images;
                $image->file = $file;
                $image->productId = $this->id;
                $image->save();
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        //  Удаление изображений.
        foreach ($this->images as $image) {
            /** @var ActiveRecord $image */
            $image->delete();
        }

        ProductsBalances::where('product_id', $this->id)->delete();
        ProductsProperties::where('product_id', $this->id)->delete();

        return parent::beforeDelete();
    }

    /**
     * Связь с таблицей `categories`.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function categories()
    {
        return $this->belongsTo('\models\Categories', 'category_id');
    }

    /**
     * Связь с таблицей `images`.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function images()
    {
        return $this->belongsToMany('\models\Images', 'products_images_relation', 'product_id', 'image_id')->remember(120);
    }

    /**
     * Связь с таблицей `products_balances`.
     * Выбор данных по всем городам.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function balances()
    {
        return $this->hasMany('\models\ProductsBalances', 'product_id')->remember(120);
    }

    /**
     * Связь с таблицей `products_balances`.
     * Получение предложений поставщиков
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vendorsBalances()
    {
        return $this->hasMany('\models\ProductsBalances', 'product_id')->whereNotNull('vendor_id')->remember(120);
    }

    /**
     * Связь с таблицей `products_balances`.
     * Выбор данных по текущему городу.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function balance($fields = ['*'])
    {
        $selectFields = array_merge($fields, ProductsBalances::getCostFields());
        return $this
            ->belongsTo('\models\ProductsBalances', 'id', 'product_id')
            ->select($selectFields)
            ->where('city_id', Cities::getCurrentCity()->id)
            ->remember(120);
    }

    /**
     * Вернет true если товар в текущем городе имеет специальную цену.
     *
     * @return boolean
     */
    public function isSpecCost()
    {
        $balance = $this->balances[0];
        return isset($balance) && $balance->is_spec_cost;
    }

    /**
     *
     * Выбор остатков по текущему или по поставщику.
     *
     * @param integer $cityId идентификатор города.
     * @param integer $vendorId идентификатор поставщика.
     */
    public function getBalances($cityId = null, $vendorId = null)
    {
        $selectFields = array_merge(['*'], ProductsBalances::getCostFields());
        $vendorId && $cityId = null;
        return ProductsBalances::where('product_id', $this->id)->whereRaw(!is_null($cityId) ? "city_id = $cityId" : "vendor_id = $vendorId")->select($selectFields)->first();
    }

    /**
     * Связь с таблицей `products_opinions`.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function opinions()
    {
        return $this->hasMany('\models\ProductsOpinions', 'product_id')->remember(120);
    }

    /**
     * Вернет список подтвержденных отзывов.
     * @return ProductsOpinions[]
     */
    public function getNumCheckedOpinions()
    {
        $opinions = $this->opinions;
        $result = 0;
        if (!empty($opinions)) {
            foreach ($opinions as $opinion) {
                if ($opinion->is_checked) {
                    ++$result;
                }
            }
        }
        return $result;
    }

    /**
     * Вернет список подтвержденных отзывов.
     * @return ProductsOpinions[]
     */
    public function getCheckedOpinions()
    {
        return $this->opinions()->where('is_checked', 't')->orderBy('date_create', 'DESC')->remember(120)->get();
    }

    /**
     * Вернет сроки и стимость доставки.
     * @param array $item данные о товаре.
     * @param object $city город получатель
     */
    public static function getCdekPrice($item)
    {
        $data['version'] = '1.0';
        $data['senderCityId'] = $item['senderCityId'];
        $data['receiverCityId'] = $item['receiverCityId'];
        $data['tariffId'] = '1';
        $data['goods'] = [['weight' => TyresWeight::weight((object)$item), 'length' => $item['diameter_outside'] / 10, 'width' => $item['diameter_outside'] / 10, 'height' => $item['width_mm'] / 10]];
        $bodyData = ['json' => json_encode($data)];
        $myCurl = curl_init();
        curl_setopt_array($myCurl, array(
            CURLOPT_URL => 'http://api.cdek.ru/calculator/calculate_price_by_json_request.php',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($bodyData)
        ));
        $response = curl_exec($myCurl);
        curl_close($myCurl);
        return json_decode($response);
    }

}