<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace models;

use components\UploadedFile;

/**
 * ActiveRecord таблицы `categories`
 *
 * @package models
 */
class Images extends \components\ActiveRecord
{
    /**
     * @var string название таблицы.
     */
    protected $table = 'images';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var UploadedFile
     */
    public $file;

    /**
     * @var integer
     */
    public $productId;

    /**
     * @var string
     */
    public $scenario;

    /**
     * @var string
     */
    private $filepath = '';

    /**
     * @var array
     */
    public $whiteScenariosList
        = [
            self::SCENARIO_PRODUCTS,
            self::SCENARIO_PAGES,
            self::SCENARIO_BANNERS,
            self::SCENARIO_EMPLOYERS,
            self::SCENARIO_OPINIONS
        ];

    /**
     * @const string
     */
    const SCENARIO_PRODUCTS = 'products';

    /**
     * @const string
     */
    const SCENARIO_PAGES = 'pages';

    /**
     * @const string
     */
    const SCENARIO_BANNERS = 'banners';

    /**
     * @const string
     */
    const SCENARIO_EMPLOYERS = 'employers';

    /**
     * @const string
     */
    const SCENARIO_OPINIONS = 'opinions';

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'       => 'ID',
            'name'     => 'Название',
            'filename' => 'Название файла',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['filename'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        if (isset($this->file) && empty($this->filename) && $this->isNewRecord()) {
            $this->filename = sha1(time() . uniqid()) . '.' . $this->file->getClientOriginalExtension();
        }

        return parent::beforeValidate();
    }

    /**
     * @inheritdoc
     */
    public function beforeSave()
    {
        if (!isset($this->scenario) || !in_array($this->scenario, $this->whiteScenariosList)) {
            $this->scenario = self::SCENARIO_PRODUCTS;
        }

        $path = public_path() . '/files/images/' . $this->scenario . '/originals/';
        $_filename = explode(':', $this->filename);
        $this->filepath = $path . end($_filename);

        if ($this->isNewRecord() || !is_file($this->filepath) || md5_file($this->file->getPathname()) != md5_file($this->filepath)) {
            if (is_file($this->filepath)) {
                unlink($this->filepath);
            }

			if (method_exists($this->file, 'copy')) {
				$this->file->copy($path, $this->filename);
			} else {
				$this->file->move($path, $this->filename);
			}
        }

        $this->filename = $this->scenario . ':' . $this->filename;

        return parent::beforeSave();
    }

    /**
     * @inheritdoc
     */
    public function afterSave()
    {
        parent::afterSave();

        switch ($this->scenario) {
            //  Сохраняем данные в таблицу `product_images_relation`
            case self::SCENARIO_PRODUCTS:
				//	Поиск дублей.
				if (ProductsImagesRelation::where(['product_id' => $this->productId, 'image_id' => $this->id])->first()) {
					return;
				}

				$relation = ProductsImagesRelation::where(['product_id' => $this->productId])->first();
				if ($relation) {
					$relation->image_id = $this->id;
					$relation->save();
					break;
				}

				//	Связываем протукт с изображением.
				\DB::insert(
                    'insert into products_images_relation (product_id, image_id) values (?, ?)',
                    [$this->productId, $this->id]
                );

                break;

            //  Сохраняем данные в таблицу `pages_images_relation`
            case self::SCENARIO_PAGES:
                if ($this->productId) {
                    \DB::insert(
                        'insert into pages_images_relation (page_id, image_id) values (?, ?)',
                        [$this->productId, $this->id]
                    );
                }
                break;
        }
    }

    /**
     * Почистит за собой файлы после удаления.
     */
    public function afterDelete()
    {
        $_parts = explode(':', $this->filename);
        $dirName = public_path() . '/files/images/' . $_parts[0] . '/';
        $fileName = current(explode('.', $_parts[1]));

        //  Удаляем оригинал.
        if (is_file($dirName . 'originals/' . $_parts[1])) {
            unlink($dirName . 'originals/' . $_parts[1]);
        }

        //  Удаляем копии.
        $scanned = scandir($dirName . 'resized/');
        foreach ($scanned as $file) {
            if (strpos($file, $fileName) !== false && is_file($dirName . 'resized/' . $file)) {
                unlink($dirName . 'resized/' . $file);
            }
        }
    }

    /**
     * Вернет все категории предварительно отсортировав их.
     *
     * @return mixed
     */
    public static function getAll($id = null)
    {
        $query = static::orderBy('sorting', 'ASC');
        if (isset($id)) {
            $query->where('category_id', '=', $id);
        }
        return $query->get();
    }

    /**
     * Вернет изображения товара по идентификатору.
     * @param integer $productId
     */
    public static function getByProductId($productId)
    {
        $rel = ProductsImagesRelation::where('product_id', '=', $productId)->first();
        return $rel && $rel->image ? $rel->image->filename : '';
    }
}
