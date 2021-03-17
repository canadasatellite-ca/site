<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote\View;

/**
 * View quote status dropdown
 */
class QuoteStatus extends \Cart2Quote\Quotation\Block\Adminhtml\Quote\View\AbstractView
{
    /**
     * Data Form object
     *
     * @var \Magento\Framework\Data\Form
     */
    protected $_form;

    /**
     * Quote Status Collection
     *
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Status\Collection
     */
    protected $_statusCollection;

    /**
     * @var \Cart2Quote\Quotation\Helper\LockQuote
     */
    protected $lockQuoteHelper;

    /**
     * QuoteStatus constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Cart2Quote\Quotation\Model\Quote $quoteCreate
     * @param \Magento\Sales\Model\AdminOrder\Create $orderCreate
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\Registry $registry
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Status\Collection $statusCollection
     * @param \Cart2Quote\Quotation\Helper\LockQuote $lockQuoteHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Cart2Quote\Quotation\Model\Quote $quoteCreate,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Registry $registry,
        \Cart2Quote\Quotation\Model\ResourceModel\Status\Collection $statusCollection,
        \Cart2Quote\Quotation\Helper\LockQuote $lockQuoteHelper,
        array $data
    ) {
        $this->_statusCollection = $statusCollection;
        $this->lockQuoteHelper = $lockQuoteHelper;
        parent::__construct(
            $context,
            $sessionQuote,
            $quoteCreate,
            $orderCreate,
            $priceCurrency,
            $registry,
            $data
        );
    }

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
        return __('Quote Status');
    }

    /**
     * Get status label
     *
     * @return string
     */
    public function getStatusLabel()
    {
        return $this->escapeHtml($this->getQuote()->getStatusLabel());
    }

    /**
     * Get status label
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->escapeHtml($this->getQuote()->getStatus());
    }

    /**
     * Get all statuses in ascending order
     *
     * @return array
     */
    public function getAllStatussesAsArray()
    {
        return $this->_statusCollection->addOrder('sort', 'ASC')->toOptionArray();
    }

    /**
     * Get statuses configuration settings
     *
     * @return string
     */
    public function getStatusesGridConfig()
    {
        $array = $this->lockQuoteHelper->getQuoteStatusesConfigArray();
        if ($array == null) {
            $array = [];
        }

        return json_encode($array);
    }
}
