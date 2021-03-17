<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Interactivated\Price\Pricing\Price;

use Magento\Catalog\Model\Product;
use Magento\Framework\Pricing\Adjustment\CalculatorInterface;
use Magento\Framework\Pricing\Price\AbstractPrice;
use Magento\Framework\Pricing\Price\BasePriceProviderInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Special price model
 */
class SpecialPrice extends \Magento\Catalog\Pricing\Price\SpecialPrice implements \Magento\Catalog\Pricing\Price\SpecialPriceInterface, BasePriceProviderInterface
{

    /**
     * Returns special price
     *
     * @return float
     */
    public function getSpecialPrice()
    {
        $specialPrice = $this->product->getSpecialPrice();
        if ($specialPrice !== null && $specialPrice !== false && !$this->isPercentageDiscount()) {
            $currency = $this->priceCurrency->getCurrency();
            $usdPrice = $this->product->getData('special_price_usd');
            if ($currency->getCode()=='USD' && $usdPrice) {
                $specialPrice = $usdPrice;
            } else {
                $specialPrice = $this->priceCurrency->convertAndRound($specialPrice);
            }
        }
        return $specialPrice;
    }
}
