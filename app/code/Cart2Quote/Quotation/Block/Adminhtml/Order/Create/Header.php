<?php
/**
 * Cart2Quote
 */
namespace Cart2Quote\Quotation\Block\Adminhtml\Order\Create;

/**
 * Create order/quote form header
 */
class Header extends \Magento\Sales\Block\Adminhtml\Order\Create\Header
{
    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        if ($this->_getSession()->getOrder()->getId()) {
            return __('Edit Order/Quote #%1', $this->_getSession()->getOrder()->getIncrementId());
        }
        $out = $this->_getCreateOrderTitle();
        return $this->escapeHtml($out);
    }

    /**
     * Generate title for new order creation page.
     * @return string
     */
    protected function _getCreateOrderTitle()
    {
        $customerId = $this->getCustomerId();
        $storeId = $this->getStoreId();
        $out = '';
        if ($customerId && $storeId) {
            $out .= __(
                'Create New Order/Quote for %1 in %2',
                $this->_getCustomerName($customerId),
                $this->getStore()->getName()
            );
            return $out;
        } elseif (!$customerId && $storeId) {
            $out .= __('Create New Order/Quote for New Customer in %1', $this->getStore()->getName());
            return $out;
        } elseif ($customerId && !$storeId) {
            $out .= __('Create New Order/Quote for %1', $this->_getCustomerName($customerId));
            return $out;
        } elseif (!$customerId && !$storeId) {
            $out .= __('Create New Order/Quote for New Customer');
            return $out;
        }

        return $out;
    }
}
