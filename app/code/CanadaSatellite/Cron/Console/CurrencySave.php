<?php
namespace CanadaSatellite\Cron\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\App\State;

class CurrencySave extends Command
{

    const EXCHANGE_RATE = 1.03;

    /**
     * @var CollectionFactory
     */
    private $_productCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    private $_storeManager;

    /**
     * @var PriceCurrencyInterface
     */
    private $_priceCurrency;

    /**
     * @var State
     */
    private $_state;

    public function __construct(
        CollectionFactory $collectionFactory,
        StoreManagerInterface $storeManager,
        PriceCurrencyInterface $priceCurrency,
        State $state,
        string $name = null
    )
    {
        $this->_productCollectionFactory = $collectionFactory;
        $this->_storeManager = $storeManager;
        $this->_priceCurrency = $priceCurrency;
        $this->_state = $state;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('currency:cron:save');
        $this->setDescription('Save currency rate after update');
        parent::configure();
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $this->_state->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND);

        /**
         * @var $collection Collection
         */
        $storeId = $this->_storeManager->getStore()->getId();
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*')
            ->addAttributeToFilter('usd_is_base_price', 1)
            ->load();
        foreach ($collection as $product) {
            /**
             * @var $product \Magento\Catalog\Model\Product
             */
            if ($product->getPrice() && $product->getTypeId() !== 'bundle') {
                $convertedPrice = $this->getPrice($product, 'price_usd');
                if (($convertedPrice !== false) && ($product->getPrice() != $convertedPrice)) {
                    $product->addAttributeUpdate('price', $convertedPrice, $storeId);
                    $product->addAttributeUpdate('price', $convertedPrice, 0);
                }
            }
            if (($product->getSpecialPrice() || $product->getSpecialPriceUsd()) && $product->getTypeId() !== 'bundle') {
                $convertedSpecialPrice = $this->getPrice($product, 'special_price_usd');
                if (($convertedSpecialPrice !== false) && ($product->getSpecialPrice() != $convertedSpecialPrice)) {
                    $product->addAttributeUpdate('special_price', $convertedSpecialPrice, $storeId);
                    $product->addAttributeUpdate('special_price', $convertedSpecialPrice, 0);
                }
            }
        }

        $output->writeln("Currency rate after update was successfully saved");
    }

    /**
     * @param $product
     * @param $priceKey
     * @return bool|float|int|mixed
     */
    public function getPrice($product, $priceKey) {
        if ($price = $product->getData($priceKey)) {
            $price = str_replace(',', '', $price);
            $rate = $this->_priceCurrency->convert(1000000, null, 'USD');
            $rate = $rate / self::EXCHANGE_RATE; //add 3% exchange
            $price = $price * 1000000 / $rate;
            return $price;
        }
        return false;
    }
}