<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Checkout;

use Magento\Framework\View\Element\Template;

/**
 * Class QuoteNotice
 *
 * @package Cart2Quote\Quotation\Block\Checkout
 */
class QuoteNotice extends \Magento\Framework\View\Element\Template
{

    /**
     * Quotation Session
     *
     * @var \Cart2Quote\Quotation\Model\Session
     */
    protected $quotationSession;

    /**
     * QuoteNotice constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Cart2Quote\Quotation\Model\Session $quotationSession
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Cart2Quote\Quotation\Model\Session $quotationSession,
        array $data = []
    ) {
        $this->quotationSession = $quotationSession;
        parent::__construct($context, $data);
    }

    /**
     * Get Quote
     *
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    public function getQuote()
    {
        return $this->quotationSession->getQuote();
    }

    /**
     * @return string
     */
    public function getLinkToQuote()
    {
        return $this->getUrl('quotation/quote/index');
    }
}
