<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Totals;

/**
 * Class DefaultTotals
 *
 * @package Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Totals
 */
class DefaultTotals extends \Magento\Sales\Block\Adminhtml\Order\Create\Totals\DefaultTotals
{
    /**
     * Template
     *
     * @var string
     */
    protected $_template = 'Magento_Sales::order/create/totals/default.phtml';

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
