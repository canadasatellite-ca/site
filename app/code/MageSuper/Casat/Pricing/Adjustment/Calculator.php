<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MageSuper\Casat\Pricing\Adjustment;

use Magento\Bundle\Model\Product\Price;
use Magento\Bundle\Pricing\Price\BundleOptionPrice;
use Magento\Bundle\Pricing\Price\BundleSelectionFactory;
use Magento\Catalog\Model\Product;
use Magento\Framework\Pricing\Adjustment\Calculator as CalculatorBase;
use Magento\Framework\Pricing\Amount\AmountFactory;
use Magento\Framework\Pricing\SaleableInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Store\Model\Store;
use Magento\Tax\Api\TaxCalculationInterface;
use Magento\Tax\Helper\Data as TaxHelper;

/**
 * Bundle price calculator
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Calculator extends \Magento\Bundle\Pricing\Adjustment\Calculator implements \Magento\Bundle\Pricing\Adjustment\BundleCalculatorInterface
{

    function calculateBundleAmount($basePriceValue, $bundleProduct, $selectionPriceList, $exclude = null)
    {
        if ($bundleProduct->getPriceType() == Price::PRICE_TYPE_FIXED) {
            //return $this->calculateFixedBundleAmount($basePriceValue, $bundleProduct, $selectionPriceList, $exclude);
            return $this->calculator->getAmount($basePriceValue, $bundleProduct, $exclude);
        } else {
            return $this->calculateDynamicBundleAmount(0, $bundleProduct, $selectionPriceList, $exclude);
        }
    }
}
