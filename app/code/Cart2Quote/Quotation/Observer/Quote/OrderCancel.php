<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Observer\Quote;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class OrderCancel
 *
 * @package Cart2Quote\Quotation\Observer\Quote
 */
class OrderCancel implements ObserverInterface
{
    /**
     * Quote factory
     *
     * @var \Cart2Quote\Quotation\Model\QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var \Magento\Quote\Model\Resourcemodel\Quote\Collection
     */
    private $quoteCollection;

    /**
     * OrderCancel constructor
     *
     * @param \Magento\Quote\Model\Resourcemodel\Quote\Collection $quoteCollection
     * @param \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory
     */
    public function __construct(
        \Magento\Quote\Model\Resourcemodel\Quote\Collection $quoteCollection,
        \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory
    ) {
        $this->quoteCollection = $quoteCollection;
        $this->quoteFactory = $quoteFactory;
    }

    /**
     * Set the quote to canceled
     *
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        $order = $observer->getOrder();
        $quoteId = $order->getQuoteId();
        $quotationId = $this->getLinkedQuotation($quoteId);

        if (!empty($quotationId)) {
            $quotationQuote = $this->quoteFactory->create()->load($quotationId);
            $this->changeQuoteStatus($quotationQuote);
        }
    }

    /**
     * Change Cart2Quote quote status
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quotationQuote
     * @throws \Exception
     */
    protected function changeQuoteStatus($quotationQuote)
    {
        $quotationQuote->setState(\Cart2Quote\Quotation\Model\Quote\Status::STATE_CANCELED)
            ->setStatus(\Cart2Quote\Quotation\Model\Quote\Status::STATUS_CANCELED)->save();
    }

    /**
     * Get linked quote number
     *
     * @param int $quoteId
     * @return int|null $linkedQuoteId
     */
    public function getLinkedQuotation($quoteId)
    {
        $quote = $this->quoteCollection->load($quoteId)->getFirstItem();
        $linkedQuoteId = $quote->getLinkedQuotationId();

        return $linkedQuoteId;
    }
}

