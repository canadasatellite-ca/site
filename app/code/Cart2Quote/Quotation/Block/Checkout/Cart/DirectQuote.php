<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Checkout\Cart;

/**
 * Class DirectQuote
 * @package Cart2Quote\Quotation\Block\Checkout\Cart
 */
class DirectQuote extends \Magento\Framework\View\Element\Template
{
    /**
     * @var bool
     */
    protected $visibilityEnabled;

    /**
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $quotationHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * DirectQuote constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Cart2Quote\Quotation\Helper\Data $quotationHelper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Cart2Quote\Quotation\Helper\Data $quotationHelper,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->quotationHelper = $quotationHelper;
        parent::__construct($context, $data);
    }

    /**
     * Check if Cart2Quote visibility is enabled
     *
     * @return bool
     */
    public function getIsQuotationEnabled()
    {
        if (isset($this->visibilityEnabled)) {
            return $this->visibilityEnabled;
        }

        $this->visibilityEnabled = false;

        if ($this->quotationHelper->isFrontendEnabled()) {
            $this->visibilityEnabled = true;
        }

        return $this->visibilityEnabled;
    }

    /**
     * Check hide order references
     *
     * @return boolean
     */
    public function getShowOrderReferences()
    {
        return $this->quotationHelper->getShowOrderReferences();
    }

    /**
     * Check is Move to Quote setting enabled
     *
     * @return bool
     */
    public function isDirectQuoteEnabled()
    {
        return $this->quotationHelper->isDirectQuoteEnabled();
    }

    /**
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->customerSession->getCustomerGroupId() != \Magento\Customer\Model\Group::NOT_LOGGED_IN_ID;
    }
}
