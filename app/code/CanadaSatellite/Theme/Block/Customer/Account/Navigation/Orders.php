<?php

namespace CanadaSatellite\Theme\Block\Customer\Account\Navigation;

class Orders extends \Magento\Customer\Block\Account\SortLink
{

    protected $_orders;

    public function __construct(
        \Magento\Sales\Block\Order\History $orders,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        array $data = [])
    {
        $this->_orders = $orders;
        parent::__construct($context, $defaultPath, $data);
    }

    protected function _toHtml()
    {
        if ($this->_orders->getOrders() && count($this->_orders->getOrders())) {
            return parent::_toHtml();
        }
        return '';
    }

}