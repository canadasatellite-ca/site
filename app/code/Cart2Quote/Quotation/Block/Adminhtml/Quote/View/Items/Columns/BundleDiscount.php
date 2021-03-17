<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\Columns;

/**
 * Class BundleDiscount
 *
 * @package Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\Columns
 */
class BundleDiscount extends \Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\DefaultRenderer
{
    /**
     * Get total discount for a bundle
     *
     * @param bool $strong
     * @param string $separator
     * @return float
     */
    public function getTotalDiscount($strong = false, $separator = '<br />')
    {
        $totalDiscount = 0;
        $baseTotalDiscount = 0;
        foreach ($this->getItem()->getChildren() as $item) {
            $totalDiscount += $item->getDiscountAmount();
            $baseTotalDiscount += $item->getBaseDiscountAmount();
        }

        return $this->displayPrices(
            $baseTotalDiscount,
            $totalDiscount,
            $strong,
            $separator
        );
    }
}
