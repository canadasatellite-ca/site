<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote\View;

/**
 * View quote expiry date calendar
 */
class ExpiryDate extends \Cart2Quote\Quotation\Block\Adminhtml\Quote\View\AbstractView
{
    /**
     * Get header css class
     *
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'head-comment';
    }

    /**
     * Get header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Expiry Date');
    }

    /**
     * Check expiry date active status
     *
     * @return string
     */
    public function isActiveExpiryDate()
    {
        $availableStatus = [
            \Cart2Quote\Quotation\Model\Quote\Status::STATUS_NEW,
            \Cart2Quote\Quotation\Model\Quote\Status::STATUS_OPEN
        ];

        $quote = $this->getQuote();
        if (in_array($quote->getStatus(), $availableStatus)) {
            return '';
        } else {
            return 'disabled';
        }
    }
}
