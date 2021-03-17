<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote\View;

/**
 * Class QuoteDate
 *
 * @package Cart2Quote\Quotation\Block\Adminhtml\Quote\View
 */
class QuoteDate extends \Cart2Quote\Quotation\Block\Adminhtml\Quote\View\AbstractView
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
        return __('Quote Date (%1)', $this->getQuoteLocaleDateTimezone());
    }

    /**
     * Get Quote date as saved in the database
     *
     * @return string
     */
    public function getPersistedQuoteRequestDate()
    {
        return $this->getQuote()->getQuotationCreatedAt();
    }

    /**
     * Get quote date converted to locale time
     *
     * @return \DateTime
     */
    public function getQuoteLocaleDate()
    {
        return $this->_localeDate->date(new \DateTime($this->getPersistedQuoteRequestDate()));
    }

    /**
     * Return timezone for quote date in admin
     *
     * @return string
     */
    public function getQuoteLocaleDateTimezone()
    {
        return $this->getQuoteLocaleDate()->getTimezone()->getName();
    }

    /**
     * Return formatted locale quote date
     *
     * @return string
     */
    public function getFormattedQuoteLocaleDate()
    {
        return $this->formatDate($this->getQuoteLocaleDate(), \IntlDateFormatter::MEDIUM, true);
    }
}
