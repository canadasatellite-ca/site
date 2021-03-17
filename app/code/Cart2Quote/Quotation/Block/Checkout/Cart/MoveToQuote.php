<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Checkout\Cart;

/**
 * One page checkout success page
 */
class MoveToQuote extends \Magento\Framework\View\Element\Template
{
    /**
     * @var bool
     */
    protected $_visibilityEnabled;

    /**
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $cart2QuoteHelper;

    /**
     * MoveToQuote constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Cart2Quote\Quotation\Helper\Data $cart2QuoteHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Cart2Quote\Quotation\Helper\Data $cart2QuoteHelper,
        array $data = []
    ) {
        $this->cart2QuoteHelper = $cart2QuoteHelper;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Check if Cart2Quote visibility is enabled
     *
     * @return bool
     */
    public function getIsQuotationEnabled()
    {
        if (isset($this->_visibilityEnabled)) {
            return $this->_visibilityEnabled;
        }

        if ($this->cart2QuoteHelper->isFrontendEnabled()) {
            $this->_visibilityEnabled = true;
            return true;
        }

        $this->_visibilityEnabled = false;
        return false;
    }

    /**
     * Check hide order references
     *
     * @return boolean
     */
    public function getShowOrderReferences()
    {
        return $this->cart2QuoteHelper->getShowOrderReferences();
    }

    /**
     * Check is Move to Quote setting enabled
     *
     * @return bool
     */
    public function isMoveToQuoteEnabled()
    {
        return $this->cart2QuoteHelper->isMoveToQuoteEnabled();
    }
}
