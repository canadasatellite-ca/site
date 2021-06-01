<?php

namespace CanadaSatellite\Theme\Plugin\Model;

use Magento\Directory\Model\Currency as ModelCurrency;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class Currency {

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
     * Currency constructor.
     * @param CollectionFactory $collectionFactory
     * @param StoreManagerInterface $storeManager
     * @param PriceCurrencyInterface $priceCurrency
     */
    function __construct(
        CollectionFactory $collectionFactory,
        StoreManagerInterface $storeManager,
        PriceCurrencyInterface $priceCurrency
    ){
        $this->_productCollectionFactory = $collectionFactory;
        $this->_storeManager = $storeManager;
        $this->_priceCurrency = $priceCurrency;
    }

    /**
     * @param ModelCurrency $subject
     * @param $result
     * @param $rates
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    function afterSaveRates(ModelCurrency $subject, $result, $rates) {
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
                }
            }
            if ($product->getSpecialPrice() && $product->getTypeId() !== 'bundle') {
                $convertedSpecialPrice = $this->getPrice($product, 'special_price_usd');
                if (($convertedSpecialPrice !== false) && ($product->getSpecialPrice() != $convertedSpecialPrice)) {
                    $product->addAttributeUpdate('special_price', $convertedSpecialPrice, $storeId);
                }
            }
        }
        return $result;
    }

    /**
     * @param $product
     * @param $priceKey
     * @return bool|float|int|mixed
     */
    function getPrice($product, $priceKey) {
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
