<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Observer\Quote;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;

/**
 * Class ConvertedToOrder
 *
 * @package Cart2Quote\Quotation\Observer\Quote
 */
class ConvertedToOrder implements ObserverInterface
{
    /**
     * Quote factory
     *
     * @var \Cart2Quote\Quotation\Model\QuoteFactory
     */
    protected $quoteFactory;

    /**
     * Checkout session
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * Mage Quote Factory
     *
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $mageQuoteFactory;

    /**
     * @var \Magento\Backend\Model\Session\Quote
     */
    protected $adminhtmlQuoteSession;

    /**
     * ConvertedToOrder constructor
     *
     * @param \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Quote\Model\QuoteFactory $mageQuoteFactory
     * @param \Magento\Backend\Model\Session\Quote $adminhtmlQuoteSession
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Model\QuoteFactory $mageQuoteFactory,
        \Magento\Backend\Model\Session\Quote $adminhtmlQuoteSession
    ) {
        $this->quoteFactory = $quoteFactory;
        $this->checkoutSession = $checkoutSession;
        $this->mageQuoteFactory = $mageQuoteFactory;
        $this->adminhtmlQuoteSession = $adminhtmlQuoteSession;
    }

    /**
     * Set the quote to complete and ordered
     *
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        $quote = $observer->getQuote();
        $setQuotationQuote = false;
        $quotationId = $quote->getLinkedQuotationId();
        if (!isset($quotationId)) {
            $quotationId = $this->adminhtmlQuoteSession->getQuotationQuoteId();
            $setQuotationQuote = true;
        }

        if ($quotationId) {
            $quotationQuote = $this->quoteFactory->create()->load($quotationId);
            $quoteId = $quotationQuote->getQuoteId();

            if (isset($quoteId)) {
                $this->changeQuoteStatus($quote, $quotationQuote, $setQuotationQuote);
            }
        }
    }

    /**
     * Get empty checkout quote
     *
     * @param int $storeId
     * @return \Magento\Quote\Model\Quote
     */
    protected function getEmptyCheckoutQuote($storeId)
    {
        return $this->mageQuoteFactory->create()
            ->setStoreId($storeId)
            ->setIsActive(true)
            ->collectTotals()
            ->save();
    }

    /**
     * Change Cart2Quote quote status
     *
     * @param Quote $quote
     * @param \Cart2Quote\Quotation\Model\Quote $quotationQuote
     * @param bool $setQuotationQuote
     */
    protected function changeQuoteStatus($quote, $quotationQuote, $setQuotationQuote)
    {
        $quotationQuote->setState(\Cart2Quote\Quotation\Model\Quote\Status::STATE_COMPLETED)
            ->setStatus(\Cart2Quote\Quotation\Model\Quote\Status::STATUS_ORDERED)->save();

        if (!$setQuotationQuote) {
            $this->checkoutSession->replaceQuote(
                $this->getEmptyCheckoutQuote($quotationQuote->getStoreId())
            );
        } else {
            $quote->setLinkedQuotationId($quotationQuote->getId());
            //$quote->setIsQuotationQuote(true); //Do not set this on a checkout quote
            $quote->save();
        }
    }
}
