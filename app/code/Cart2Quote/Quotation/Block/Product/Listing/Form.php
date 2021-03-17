<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Product\Listing;

use Magento\Catalog\Model\Product;

/**
 * Class Form
 *
 * @package Cart2Quote\Quotation\Block\Product\Listing
 */
class Form extends \Magento\Catalog\Block\Product\ProductList\Item\Block
{
    /**
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $quotationHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Cart2Quote\Quotation\Helper\QuotationCart
     */
    protected $quotationCartHelper;

    /**
     * Form constructor
     *
     * @param \Cart2Quote\Quotation\Helper\QuotationCart $quotationCartHelper
     * @param \Cart2Quote\Quotation\Helper\Data $quotationHelper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param array $data
     */
    public function __construct(
        \Cart2Quote\Quotation\Helper\QuotationCart $quotationCartHelper,
        \Cart2Quote\Quotation\Helper\Data $quotationHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Block\Product\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->quotationHelper = $quotationHelper;
        $this->customerSession = $customerSession;
        $this->quotationCartHelper = $quotationCartHelper;
    }

    /**
     * Check if we can show the quote button for the current product and customer group
     *
     * @return bool
     */
    public function showQuoteButton()
    {
        return $this->quotationHelper->showButtonOnList(
            $this->getProduct(),
            $this->customerSession->getCustomerGroupId()
        );
    }

    /**
     * Check if we can show the not logged in message
     *
     * @return bool
     */
    public function showMessageNotLoggedIn()
    {
        $configShowMessage = $this->_scopeConfig->getValue(
            'cart2quote_quotation/global/show_message_not_logged_in',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return $this->showQuoteButton() && !$this->isLoggedIn() && $configShowMessage;
    }

    /**
     * Get the add to quote product url
     *
     * @param array $additional
     * @return string
     */
    public function getWidgetAddToQuoteUrl($additional = [])
    {
        return $this->quotationCartHelper->getAddUrl(
            $this->getProduct(),
            $additional
        );
    }

    /**
     * Getter for the parent block
     *
     * @return bool|\Magento\Framework\View\Element\AbstractBlock
     */
    public function getParentBlock()
    {
        $parentBlock = parent::getParentBlock();
        if ($parentBlock instanceof \Cart2Quote\Quotation\Block\Quote\Request\RequestStrategyContainer) {
            return $parentBlock->getParentBlock();
        }

        return $parentBlock;
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
    public function isQuotationFrontendVisible()
    {
        return $this->quotationHelper->isFrontendEnabled();
    }
}
