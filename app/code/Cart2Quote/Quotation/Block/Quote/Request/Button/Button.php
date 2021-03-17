<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Quote\Request\Button;

/**
 * Class Button
 *
 * @package Cart2Quote\Quotation\Block\Quote\Request\Button
 */
class Button extends \Magento\Catalog\Block\Product\AbstractProduct
{
    /**
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    private $quotationHelper;
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * Button constructor
     *
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Cart2Quote\Quotation\Helper\Data $quotationHelper
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Cart2Quote\Quotation\Helper\Data $quotationHelper,
        \Magento\Catalog\Block\Product\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->quotationHelper = $quotationHelper;
        $this->customerSession = $customerSession;
    }

    /**
     * Check if button should be shown
     *
     * @return bool
     */
    public function showButton()
    {
        return $this->quotationHelper->showButtonOnProductView(
            $this->getProduct(),
            $this->customerSession->getCustomerGroupId()
        );
    }

    /**
     * Check if not logged in message should be shown
     *
     * @return bool
     */
    public function showMessageNotLoggedIn()
    {
        $configShowMessage = $this->_scopeConfig->getValue(
            'cart2quote_quotation/global/show_message_not_logged_in',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return $this->showButton() && !$this->isLoggedIn() && $configShowMessage;
    }

    /**
     * Check if customer is logged in
     * - using the group id check, its a full page cache safe way to check if the customer is logged in.
     * - Required as of Magento 2.3.1
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->customerSession->getCustomerGroupId() != \Magento\Customer\Model\Group::NOT_LOGGED_IN_ID;
    }

    /**
     * @return bool
     */
    public function directQuote()
    {
        return $this->quotationHelper->isDirectPrintEnabled();
    }
}
