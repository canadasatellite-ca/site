<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote\Create;

/**
 * Create order/quote form header
 */
class Header extends \Magento\Sales\Block\Adminhtml\Order\Create\Header
{
    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote $quoteResourceModel
     */
    protected $quoteResourceModel;

    /**
     * Header constructor.
     *
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote $quoteResourceModel
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Magento\Sales\Model\AdminOrder\Create $orderCreate
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Customer\Helper\View $customerViewHelper
     * @param array $data
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\ResourceModel\Quote $quoteResourceModel,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Helper\View $customerViewHelper,
        array $data = []
    ) {
        $this->quoteResourceModel = $quoteResourceModel;
        parent::__construct(
            $context,
            $sessionQuote,
            $orderCreate,
            $priceCurrency,
            $customerRepository,
            $customerViewHelper,
            $data
        );
    }

    /**
     * To html function
     */
    protected function _toHtml()
    {
        $quotationQuote = $this->getQuote();
        if ($id = $quotationQuote->getId()) {
            $this->quoteResourceModel->load($quotationQuote, $id);
            if ($incrementId = $quotationQuote->getIncrementId()) {
                return sprintf("%s%s", __('Edit Quote #'), $incrementId);
            }
        }
        $out = $this->_getCreateOrderTitle();

        return $this->escapeHtml($out);
    }

    /**
     * Generate title for new order creation page.
     *
     * @return string
     */
    protected function _getCreateOrderTitle()
    {
        $customerId = $this->getCustomerId();
        $storeId = $this->getStoreId();
        $out = '';

        if ($customerId && $storeId) {
            $out .= __('Create Quote for %1 in %2', $this->_getCustomerName($customerId), $this->getStore()->getName());
        } elseif (!$customerId && $storeId) {
            $out .= __('Create Quote for New Customer in %1', $this->getStore()->getName());
        } elseif ($customerId && !$storeId) {
            $out .= __('Create Quote for %1', $this->_getCustomerName($customerId));
        } elseif (!$customerId && !$storeId) {
            $out .= __('Create Quote for New Customer');
        }

        return $out;
    }
}
