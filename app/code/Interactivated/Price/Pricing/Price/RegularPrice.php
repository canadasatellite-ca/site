<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Interactivated\Price\Pricing\Price;

use Magento\Framework\Pricing\Price\AbstractPrice;
use Magento\Framework\Pricing\Price\BasePriceProviderInterface;

/**
 * Class RegularPrice
 */
class RegularPrice extends \Magento\Catalog\Pricing\Price\RegularPrice  implements BasePriceProviderInterface
{
    /**
     * Get price value
     *
     * @return float|bool
     */
    function getValue()
    {
        if ($this->value === null) {
            $price = $this->product->getPrice();
            $currency = $this->priceCurrency->getCurrency();
            $usdPrice = $this->product->getData('price_usd');
            if($currency->getCode()=='USD' && $usdPrice){
                $priceInCurrentCurrency = $usdPrice;
            } else {
                $priceInCurrentCurrency = $this->priceCurrency->convertAndRound($price);
            }

            $this->value = $priceInCurrentCurrency ? floatval($priceInCurrentCurrency) : false;
        }
        return $this->value;
    }
}
