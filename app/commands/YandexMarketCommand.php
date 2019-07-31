<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use components\yandex\YandexContentApi;
use components\yandex\YandexPartnerApi;

class YandexMarketCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'command:yandexmarket';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Синхронизация с маркетом.';

    /**
     * @var YandexContentApi
     */
    private $_content;

    /**
     * @var YandexPartnerApi
     */
    private $_partner;

    /**
     * @var bool
     */
    private $_is_debug = false;

    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct();

        $this->_partner = new YandexPartnerApi(\Config::get('yandex-api.partner.access_token'));
        $this->_partner->setClientId(\Config::get('yandex-api.partner.id'));
        $this->_partner->setDebugMode(\Config::get('yandex-api.is_debug'));

        $this->_content = new YandexContentApi;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        if (\Config::get('yandex-api.is_debug')) {
            echo "Включен режим разработки в котором есть ограничения данных\n";
        }

        $this->syncOpinions();
    }

    /**
     * Синхронизация отзывов.
     */
    private function syncOpinions()
    {
        //$models = $this->_content->request('campaigns/3934473/offers.json');
        $campaigns = $this->_partner->getCampaigns()->getAll();
        /** @var \Yandex\Market\Models\Campaign $campaign */
        foreach ($campaigns as $campaign) {
            sleep(1);
            $this->comment('Campaign ' . $campaign->getId());

            //  На некоторые компании кидает исключения.
            try {
                $this->_partner->setCampaignId($campaign->getId());
                $this->_partner->getOffersResponse();

                $items = $this->_partner->getAllOffers()->getAll();
            } catch (Exception $e) {
                continue;
            }

            foreach ($items as $key => $item) {
                sleep(1);

                //  Если нет модели товара.
                if (!$item->getModelId()) {
                    continue;
                }

                $this->comment('Offer ' . $item->getId());

                $opinions = $this->_content->request('model/' . $item->getModelId() . '/opinion.json');
                if (!isset($opinions) || !isset($opinions->modelOpinions)) {
                    continue;
                }

                foreach ($opinions->modelOpinions->opinion as $opinion) {
                    $this->comment('Opinion ' . $opinion->id);

                    $model = \models\ProductsOpinions::where(['market_opinion_id' => $opinion->id])->first();
                    if (!isset($model) && isset($opinion)) {
                        $model = new \models\ProductsOpinions();
                        $model->market_opinion_id = $opinion->id;
                        $model->market_model_id = $item->getModelId();
                        $model->product_id = $item->getId();
                        $model->user_fullname = isset($opinion->author) ? $opinion->author : null;
                        $model->user_advantages = isset($opinion->pro) ? $opinion->pro : '';
                        $model->user_disadvantages = isset($opinion->contra) ? $opinion->contra : '';
                        $model->user_comment = isset($opinion->text) ? $opinion->text : '';
                        $model->rating = $opinion->grade + 3;
                        $model->date_create = date('Y-m-d H:i:s', (int)$opinion->date / 1000);
                        /*if (!$model->save()) {
                            $this->error(reset($model->getErrors()));
                        }*/
                        $model->save();
                    }
                }

                if (\Config::get('yandex-api.is_debug') && $key >= 5) {
                    break;
                }
            }

        }

        die();
    }
}