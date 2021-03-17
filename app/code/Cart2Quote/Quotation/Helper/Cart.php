<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Helper;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Quote\Model\QuoteRepository;

/**
 * Class Cart
 * @package Cart2Quote\Quotation\Helper
 */
class Cart extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;
    
    /**
     * @var QuoteRepository
     */
    protected $quoteRepository;

    /**
     * Cart constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        CheckoutSession $checkoutSession,
        QuoteRepository $quoteRepository
    ) {
        parent::__construct($context);
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * Deletes the quote which was created from quotation quote
     */
    public function clearCart()
    {
        $quote = $this->checkoutSession->getQuote();

        if ($quote->getLinkedQuotationId()) {
            $this->quoteRepository->delete($quote);
        }
    }
}