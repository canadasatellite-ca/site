<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Totals;

/**
 * Class Discount
 *
 * @package Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Totals
 */
class Discount extends \Magento\Sales\Block\Adminhtml\Order\Create\Totals\Discount
{
    /**
     * Retrieve formatted price
     *
     * @param float $value
     * @return string
     */
    public function formatPrice($value)
    {
        return $this->getLayout()->getBlock('totals')->formatPrice($value);
    }
}
