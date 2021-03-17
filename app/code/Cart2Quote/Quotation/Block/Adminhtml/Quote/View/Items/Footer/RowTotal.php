<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\Footer;

/**
 * Class RowTotal
 *
 * @package Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\Footer
 */
class RowTotal extends \Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\DefaultRenderer
{
    /**
     * The base total amount including tax for the whole quote
     *
     * @return int|double
     */
    public function getQuoteBaseTotalAmountInclTax()
    {
        $total = 0;
        $quote = $this->getOrder();
        foreach ($quote->getAllVisibleItems() as $quoteItem) {
            $total += parent::getBaseTotalAmountInclTax($quoteItem);
        }

        return $total;
    }

    /**
     * The the base total amount including tax for the whole quote
     *
     * @return int|double
     */
    public function getQuoteTotalAmountInclTax()
    {
        $total = 0;
        $quote = $this->getOrder();
        foreach ($quote->getAllVisibleItems() as $quoteItem) {
            $total += parent::getTotalAmountInclTax($quoteItem);
        }

        return $total;
    }
}
