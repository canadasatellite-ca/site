<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Plugin\Quotation\CustomerData;

/**
 * Class QuotePlugin
 *
 * @package Cart2Quote\Quotation\Plugin\Quotation\CustomerData
 */
class QuotePlugin extends \Magento\Tax\Plugin\Checkout\CustomerData\Cart
{
    /**
     * @var QuotePlugin|null
     */
    protected $quote = null;

    /**
     * @var \Cart2Quote\Quotation\Model\Session
     */
    protected $quotationSession;

    /**
     * QuotePlugin constructor.
     *
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Checkout\Helper\Data $checkoutHelper
     * @param \Magento\Tax\Block\Item\Price\Renderer $itemPriceRenderer
     * @param \Cart2Quote\Quotation\Model\Session $quotationSession
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Checkout\Helper\Data $checkoutHelper,
        \Magento\Tax\Block\Item\Price\Renderer $itemPriceRenderer,
        \Cart2Quote\Quotation\Model\Session $quotationSession
    ) {
        $this->quotationSession = $quotationSession;
        parent::__construct($checkoutSession, $checkoutHelper, $itemPriceRenderer);
    }

    /**
     * Get active quote
     *
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        if (null === $this->quote) {
            $this->quote = $this->quotationSession->getQuote();
        }

        return $this->quote;
    }
}
