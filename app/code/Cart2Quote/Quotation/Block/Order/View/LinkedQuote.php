<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Order\View;

/**
 * Class LinkedQuote
 * @package Cart2Quote\Quotation\Block\Order\View
 */
class LinkedQuote extends \Magento\Sales\Block\Order\View
{
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     */
    private $orderCollectionFactory;

    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory
     */
    private $quoteCollection;

    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\CollectionFactory
     */
    private $quotationQuoteCollection;

    /**
     * LinkedQuote constructor.
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\CollectionFactory $quotationQuoteCollection
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory $quoteCollection
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Payment\Helper\Data $paymentHelper
     * @param array $data
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\CollectionFactory $quotationQuoteCollection,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory $quoteCollection,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Payment\Helper\Data $paymentHelper,
        array $data = []
    ) {
        $this->quotationQuoteCollection = $quotationQuoteCollection;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->quoteCollection = $quoteCollection;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $registry, $httpContext, $paymentHelper, $data);
    }

    /**
     * @param int $quoteId
     * @return int|bool
     */
    public function getQuotationQuoteId($quoteId)
    {
        $quotationQuoteId = false;
        $quotationQuoteData = $this->quoteCollection
            ->create()->addFieldToSelect(['linked_quotation_id'])
            ->addFieldToFilter('main_table.entity_id', $quoteId)->getItems();

        if (isset($quotationQuoteData)
            && isset($quotationQuoteData[$quoteId])
            && !empty($quotationQuoteData[$quoteId])
        ) {
            $quotationQuoteId = $quotationQuoteData[$quoteId]->getLinkedQuotationId();
        }

        return $quotationQuoteId;
    }

    /**
     * @param int $quotationQuoteIdBlock
     * @return int
     */
    public function getQuotationQuoteIncrementId($quotationQuoteIdBlock)
    {
        $quotationQuote = $this->quotationQuoteCollection
            ->create()->addFieldToSelect(['increment_id'])
            ->addFieldToFilter('main_table.quote_id', $quotationQuoteIdBlock)->getFirstItem();
        $quotationQuoteIncrementId = $quotationQuote->getIncrementId();

        return $quotationQuoteIncrementId;
    }

    /**
     * @param int $quoteId
     * @return string
     */
    public function getQuoteViewByQuotationId($quoteId)
    {
        $item = $this->quotationQuoteCollection->create()
            ->addFieldToFilter('quote_id', $quoteId)->getFirstItem();
        $itemView = $this->getUrl('quotation/quote/view',
            [
                'quote_id' => $item->getQuoteId()
            ]
        );

        return $itemView;
    }
}
