<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\Order\View;

/**
 * Class LinkedQuote
 *
 * @package Cart2Quote\Quotation\Block\Adminhtml\Order\View
 */
class LinkedQuote extends \Magento\Sales\Block\Adminhtml\Order\AbstractOrder
{
    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\Collection\Factory $quoteCollectionFactory
     */
    protected $quoteCollectionFactory;

    /**
     * @var \Magento\Quote\Model\Resourcemodel\Quote\Collection $quoteCollection
     */
    protected $quoteCollection;

    /**
     * LinkedQuote constructor.
     *
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\Collection $quoteCollectionFactory
     * @param \Magento\Quote\Model\Resourcemodel\Quote\Collection $quoteCollection
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Helper\Admin $adminHelper
     * @param array $data
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\Collection $quoteCollectionFactory,
        \Magento\Quote\Model\Resourcemodel\Quote\Collection $quoteCollection,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        array $data = []
    ) {
        $this->quoteCollection = $quoteCollection;
        $this->quoteCollectionFactory = $quoteCollectionFactory;
        parent::__construct($context, $registry, $adminHelper, $data);
    }

    /**
     * Get quote view URL
     *
     * @param int $quoteId
     * @return string
     */
    public function getQuoteViewUrl($quoteId)
    {
        $quoteId = $this->getLinkedQuotation($quoteId);

        return $this->getUrl('quotation/quote/view', ['quote_id' => $quoteId]);
    }

    /**
     * Get quote increment id
     *
     * @param int $quoteId
     * @return string|bool
     */
    public function getQuoteNumber($quoteId)
    {
        $quoteId = $this->getLinkedQuotation($quoteId);
        $quote = $this->quoteCollectionFactory->getQuote($quoteId);

        return is_array($quote) ? $quote['increment_id'] : false;
    }

    /**
     * Get linked quote number
     *
     * @param int $quoteId
     * @return int $quoteId
     */
    public function getLinkedQuotation($quoteId)
    {
        $quote = $this->quoteCollection->addFieldToFilter('entity_id', ['eq' => $quoteId])->load()->getFirstItem();
        $linkedQuoteId = $quote->getLinkedQuotationId();

        return isset($linkedQuoteId) ? $linkedQuoteId : $quoteId;
    }
}
