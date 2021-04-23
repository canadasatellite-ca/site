<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MageSuper\Casat\Plugin\Bundle\Model\Product;

use Magento\Customer\Api\GroupManagementInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Bundle Price Model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Price
{
    protected $_localeDate;
    protected $_eventManager;

    function __construct(
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Event\ManagerInterface $eventManager
    )
    {
        $this->_localeDate = $localeDate;
        $this->_eventManager = $eventManager;
    }

    /**
     * Calculate and apply special price
     *
     * @param float $finalPrice
     * @param float $specialPrice
     * @param string $specialPriceFrom
     * @param string $specialPriceTo
     * @param mixed $store
     * @return float
     */
    function aroundCalculateSpecialPrice(
        \Magento\Bundle\Model\Product\Price $subject, \Closure $proceed,
        $finalPrice,
        $specialPrice,
        $specialPriceFrom,
        $specialPriceTo,
        $store = null
    )
    {
        if ($specialPrice !== null && $specialPrice != false) {
            if ($this->_localeDate->isScopeDateInInterval($store, $specialPriceFrom, $specialPriceTo)) {
                /*$specialPrice = $finalPrice * ($specialPrice / 100);*/
                $specialPrice = $specialPrice;
                $finalPrice = min($finalPrice, $specialPrice);
            }
        }

        return $finalPrice;
    }

    function aroundGetFinalPrice(
        \Magento\Bundle\Model\Product\Price $subject, \Closure $proceed,
        $qty, $product)
    {
        if ($qty === null && $product->getCalculatedFinalPrice() !== null) {
            return $product->getCalculatedFinalPrice();
        }

        $finalPrice = $subject->getBasePrice($product, $qty);
        $specialPrice = $product->getSpecialPrice();
        if (!$finalPrice && $specialPrice){
            $finalPrice = $specialPrice;
        }

        $product->setFinalPrice($finalPrice);
        $this->_eventManager->dispatch('catalog_product_get_final_price', ['product' => $product, 'qty' => $qty]);
        $finalPrice = $product->getData('final_price');

        $finalPrice = $this->_applyOptionsPrice($product, $qty, $finalPrice);
        //$finalPrice += $subject->getTotalBundleItemsPrice($product, $qty);

        $f = $proceed($qty, $product);

        $finalPrice = max(0, $finalPrice);
        $product->setFinalPrice($finalPrice);
        return $finalPrice;
    }

    protected function _applyOptionsPrice($product, $qty, $finalPrice)
    {
        $optionIds = $product->getCustomOption('option_ids');
        if ($optionIds) {
            $basePrice = $finalPrice;
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                if ($option = $product->getOptionById($optionId)) {
                    $confItemOption = $product->getCustomOption('option_' . $option->getId());

                    $group = $option->groupFactory($option->getType())
                        ->setOption($option)
                        ->setConfigurationItemOption($confItemOption);
                    $finalPrice += $group->getOptionPrice($confItemOption->getValue(), $basePrice);
                }
            }
        }

        return $finalPrice;
    }
}
