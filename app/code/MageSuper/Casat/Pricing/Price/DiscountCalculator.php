<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MageSuper\Casat\Pricing\Price;

use Magento\Catalog\Model\Product;

/**
 * Class DiscountCalculator
 */
class DiscountCalculator extends  \Magento\Bundle\Pricing\Price\DiscountCalculator
{
    /**
     * Apply percentage discount
     *
     * @param Product $product
     * @param float|null $value
     * @return float|null
     */
    function calculateDiscount(Product $product, $value = null)
    {
        if ($value === null) {
            $value = $product->getPriceInfo()->getPrice(FinalPrice::PRICE_CODE)->getValue();
        }

        $discount = null;
        /*foreach ($product->getPriceInfo()->getPrices() as $price) {
            if ($price instanceof DiscountProviderInterface && $price->getDiscountPercent()) {
                $discount = min($price->getDiscountPercent(), $discount ?: $price->getDiscountPercent());
            }
        }*/
        return (null !== $discount) ?  $discount/100 * $value : $value;
    }
}
