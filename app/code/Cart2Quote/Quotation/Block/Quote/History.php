<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Quote;

/**
 * Quotation quote history block
 */
class History extends AbstractQuote
{
    /**
     * @var string
     */
    protected $_template = 'quote/history.phtml';

    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\CollectionFactory
     */
    protected $_quoteCollectionFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Config
     */
    protected $_quoteConfig;

    /** @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\Collection */
    protected $quotes;

    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Status
     */
    protected $status;

    /**
     * Quote Repository
     *
     * @var \Cart2Quote\Quotation\Api\QuoteRepositoryInterface
     */
    protected $quoteRepositoryInterface;

    /**
     * History constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\CollectionFactory $quoteCollectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Cart2Quote\Quotation\Model\Session $quotationSession
     * @param \Cart2Quote\Quotation\Helper\Data $quotationHelper
     * @param \Cart2Quote\Quotation\Model\Quote\Config $quoteConfig
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @param \Cart2Quote\Quotation\Model\Quote\Status $status
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\CollectionFactory $quoteCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Cart2Quote\Quotation\Model\Session $quotationSession,
        \Cart2Quote\Quotation\Helper\Data $quotationHelper,
        \Cart2Quote\Quotation\Model\Quote\Config $quoteConfig,
        \Cart2Quote\Quotation\Model\Quote $quote,
        \Cart2Quote\Quotation\Model\Quote\Status $status,
        \Cart2Quote\Quotation\Api\QuoteRepositoryInterface $quoteRepositoryInterface,
        array $data = []
    ) {
        $this->_quoteCollectionFactory = $quoteCollectionFactory;
        $this->_customerSession = $customerSession;
        $this->_quoteConfig = $quoteConfig;
        $this->quote = $quote;
        $this->status = $status;
        $this->quoteRepositoryInterface = $quoteRepositoryInterface;
        parent::__construct($context, $customerSession, $quotationSession, $quotationHelper, $data);
    }

    /**
     * Get pager html
     *
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Get quote view url
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return string
     */
    public function getViewUrl($quote)
    {
        $quotable = $this->status->showQuotableQuote($quote->getState(), $quote->getStatus());
        if (!$quotable && $quote->getClonedQuoteId()) {
            return $this->getUrl('quotation/quote/view', ['quote_id' => $quote->getClonedQuoteId()]);
        }

        return $this->getUrl('quotation/quote/view', ['quote_id' => $quote->getId()]);
    }

    /**
     * Get back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');
    }

    /**
     * Construct
     */
    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('My Quotes'));
    }

    /**
     * Prepair layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getQuotes()) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'sales.quote.history.pager'
            )->setCollection(
                $this->getQuotes()
            );
            $this->setChild('pager', $pager);
            $this->getQuotes()->load();
        }
        return $this;
    }

    /**
     * Get quote collection
     *
     * @return bool|\Cart2Quote\Quotation\Model\ResourceModel\Quote\Collection
     */
    public function getQuotes()
    {
        if (!($customerId = $this->_customerSession->getCustomerId())) {
            return false;
        }
        if (!$this->quotes) {
            $this->quotes = $this->_quoteCollectionFactory->create()->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'customer_id',
                $customerId
            )->addFieldToFilter(
                'is_quote',
                \Cart2Quote\Quotation\Model\Quote::IS_QUOTE
            )->addFieldToFilter(
                'status',
                ['in' => $this->_quoteConfig->getVisibleOnFrontStatuses()]
            )->addFieldToFilter(
                'cloned_quote',
                ['neq' => \Cart2Quote\Quotation\Model\Quote::ORIGINAL_QUOTE]
            )->setOrder(
                'quotation_created_at',
                'desc'
            );
        }
        return $this->quotes;
    }

    /**
     * Format total value based on quote currency
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function formatValue($quote)
    {
        $quotable = $this->status->showQuotableQuote($quote->getState(), $quote->getStatus());
        if (!$quotable && $quote->getClonedQuoteId()) {
            $quote = $this->quoteRepositoryInterface->get($quote->getClonedQuoteId());

            return $quote->formatPrice($quote->getBaseGrandTotal());
        }

        return $quote->formatPrice($quote->getBaseGrandTotal());
    }

    /**
     * Get config setting for hide prices dashboard
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return bool
     */
    public function isHidePrices($quote)
    {
        return $this->quotationHelper->isHidePrices($quote);
    }
}
