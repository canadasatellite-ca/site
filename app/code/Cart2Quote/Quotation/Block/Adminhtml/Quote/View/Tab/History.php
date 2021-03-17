<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Tab;

use Magento\Sales\Block\Adminhtml\Order\View\Tab\History as MageHistory;
use Magento\Backend\Block\Widget\Tab\TabInterface;

/**
 * Quote history tab
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class History extends MageHistory implements TabInterface
{
    /**
     * Template
     *
     * @var string
     */
    protected $_template = 'quote/view/tab/history.phtml';

    /**
     * Compose and get quote full history.
     * - Consists of the status history comments as well as of invoices, shipments and creditmemos creations
     *
     * @return array
     */
    public function getFullHistory()
    {
        $quote = $this->getQuote();

        $history = [];
        foreach ($quote->getAllStatusHistory() as $quoteComment) {
            $history[] = $this->_prepareHistoryItem(
                $quoteComment->getStatusLabel(),
                $quoteComment->getIsCustomerNotified(),
                $this->getQuoteAdminDate($quoteComment->getQuotationCreatedAt()),
                $this->escapeHtml($quoteComment->getComment())
            );
        }

        usort($history, [__CLASS__, 'sortHistoryByTimestamp']);
        return $history;
    }

    /**
     * Retrieve quote model instance
     *
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    public function getQuote()
    {
        return $this->_coreRegistry->registry('current_quote');
    }

    /**
     * Get quote admin date
     *
     * @param int $createdAt
     * @return \DateTime
     */
    public function getQuoteAdminDate($createdAt)
    {
        return $this->_localeDate->date(new \DateTime($createdAt));
    }

    /**
     * Get Table Title
     */
    public function getTabTitle()
    {
        return __('Quote History');
    }

    /**
     * Customer Notification Applicable check method
     *
     * @param array $historyItem
     * @return bool
     */
    public function isCustomerNotificationNotApplicable($historyItem)
    {
        return $historyItem['notified'] ==
            \Cart2Quote\Quotation\Model\Quote\Status\History::CUSTOMER_NOTIFICATION_NOT_APPLICABLE;
    }
}
