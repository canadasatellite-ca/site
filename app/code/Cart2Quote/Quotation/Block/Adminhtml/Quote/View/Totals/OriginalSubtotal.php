<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Totals;

/**
 * Class OriginalSubtotal
 * @package Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Totals
 */
class OriginalSubtotal extends \Magento\Sales\Block\Adminhtml\Order\Create\Totals\Subtotal
{
    /**
     * Check if subtotal including tax needs to be displayed
     *
     * @return bool
     */
    public function displayCartPricesInclTax()
    {
        /**
         * Check without store parameter - we will get admin configuration value
         */
        return $this->_taxConfig->displayCartSubtotalInclTax();
    }
}