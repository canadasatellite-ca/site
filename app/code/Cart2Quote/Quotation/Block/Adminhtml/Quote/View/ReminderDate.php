<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote\View;

/**
 * Class ReminderDate
 * - View quote reminder date calendar
 *
 * @package Cart2Quote\Quotation\Block\Adminhtml\Quote\View
 */
class ReminderDate extends \Cart2Quote\Quotation\Block\Adminhtml\Quote\View\AbstractView
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
        return __('Reminder Date');
    }

    /**
     * Check reminder date active status
     *
     * @return string
     */
    public function isActiveReminderDate()
    {
        $availableStatus = [
            \Cart2Quote\Quotation\Model\Quote\Status::STATUS_NEW,
            \Cart2Quote\Quotation\Model\Quote\Status::STATUS_OPEN,
            \Cart2Quote\Quotation\Model\Quote\Status::STATUS_PROPOSAL_SENT
        ];

        $quote = $this->getQuote();
        if (in_array($quote->getStatus(), $availableStatus)) {
            return '';
        }

        return 'disabled';
    }
}
