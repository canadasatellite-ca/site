<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageSuper\Casat\Plugin\Product\View\Options\Type;

use \Magento\Catalog\Block\Product\View\Options\Type\Select;
use \Magento\Directory\Model\Currency;
use Magento\Store\Model\StoreManagerInterface;
use CanadaSatellite\Theme\Plugin\Model\Currency as CasatCurrency;

class BeforeSelectValuesHtml
{
    /**
     * @var Currency
     */
    protected $_currencyModel;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    function __construct(
        Currency $currencyModel,
        StoreManagerInterface $storeManager
    ){
        $this->_currencyModel = $currencyModel;
        $this->_storeManager = $storeManager;
    }

    function beforeGetValuesHtml(Select $subject)
    {
        $option = $subject->getOption();
        $currencyCode = $option->getCurrencyCode();
        $optionCurrencyRate = $this->_currencyModel->getCurrencyRates($currencyCode, 'CAD')['CAD'];
        $frontendCurrency = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();

        foreach ($option->getValues() as $value) {
            if (!isset($value['is_changed'])) {
                if ($frontendCurrency !== $currencyCode) {
                    $price = $value->getPrice() * $optionCurrencyRate * CasatCurrency::EXCHANGE_RATE;
                } else {
                    $price = $value->getPrice() * $optionCurrencyRate;
                }
                $value->setPrice($price);
                $value->setDefaultPrice($price);
                $value['is_changed'] = true;
            }
        }

        if($option->getType()==\Magento\Catalog\Model\Product\Option::OPTION_TYPE_CHECKBOX){
            foreach ($option->getValues() as $value) {
                $title = $value->getTitle();
                $new_title = $title . ' ' . $option->getTitle();
                $value->setTitle($new_title);
            }
        }

        return [];
    }
}
