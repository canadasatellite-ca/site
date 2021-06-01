<?php

namespace CanadaSatellite\Theme\Plugin\Model;

use Magento\Catalog\Controller\Adminhtml\Product\Save;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Directory\Model\Currency;

class ProductUpdateCurrency {

    /**
     * @var CollectionFactory
     */
    private $_productCollectionFactory;

    /**
     * @var Currency
     */
    private $_currencyModel;

    /**
     * Is need too call after method
     */
    private $_isCallAfter = false;

    /**
     * ProductUpdateCurrency constructor.
     * @param CollectionFactory $collectionFactory
     * @param Currency $currencyModel
     */
    function __construct(
        CollectionFactory $collectionFactory,
        Currency $currencyModel
    ){
        $this->_productCollectionFactory = $collectionFactory;
        $this->_currencyModel = $currencyModel;
    }

    function beforeExecute(Save $subject) {
        $currentIsUsd = $subject->getRequest()->getParams()['product']['usd_is_base_price'];

        if (isset($subject->getRequest()->getParams()['product']['current_product_id'])) {
            $currentProductId = $subject->getRequest()->getParams()['product']['current_product_id'];

            $isUsd = $this->getUsdIsBase($currentProductId);

            if ($currentIsUsd != $isUsd) {
                if ($currentIsUsd) {
                    $this->_isCallAfter = true;
                }
            }
        };

        return null;
    }

    function afterExecute(Save $subject, $result) {
        if ($this->_isCallAfter) {
            $rates = $this->_currencyModel->getConfigAllowCurrencies();
            $currencyRates = [];
            foreach ($rates as $rateFirst) {
                if ($rateFirst != 'AUD'){
                    foreach ($rates as $rateSecond) {
                        if ($rateFirst != $rateSecond) {
                            $currencyRates[$rateFirst][$rateSecond] = $this->_currencyModel->getCurrencyRates($rateFirst, $rateSecond)[$rateSecond];
                        }
                    }
                }
            }
            $this->_currencyModel->saveRates($currencyRates);
        }
        return $result;
    }

    private function getUsdIsBase($currentProductId) {
        /**
         * @var $collection Collection
         */
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*')
            ->addAttributeToFilter('entity_id', $currentProductId)
            ->setPageSize(1);

        $result = false;
        if ($collection->count()) {
            $result = $collection->getFirstItem()->getUsdIsBasePrice();
        }

        return $result;
    }

}