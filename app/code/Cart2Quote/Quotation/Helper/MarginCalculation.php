<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Class MarginCalculation
 *
 * @package Cart2Quote\Quotation\Helper
 */
class MarginCalculation extends AbstractHelper
{
    /**
     * Caclulate cost percentage
     *
     * @param float $price
     * @param float $cost
     * @return float
     */
    public function calculatePercentage($price, $cost)
    {
        if ($price == $cost || $price == 0) {
            return 0.00;
        }

        return round((($price - $cost) / $price) * 100, 1);
    }

    /**
     * Get item margin
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return float | null
     */
    public function itemMargin(\Magento\Quote\Model\Quote\Item $item)
    {
        $tierItem = $item->getTierItem();
        $price = $tierItem->getCustomPrice();
        if ($price > 0) {
            if ($item['no_discount'] == false) {
                $price *= ((100 - $item['discount_percent']) / 100);
            }
            $cost = $item->getProduct()->getCost();

            if ($tierItem->getBaseCost()) {
                $cost = $tierItem->getBaseCost();
            }

            /**
             * If cost is not known, no GPMargin is calculated
             */
            if ($cost == null) {
                return null;
            }

            return $this->calculatePercentage($price, $cost);
        }
    }
}
