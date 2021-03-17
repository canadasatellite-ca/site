<?php

namespace MageSuper\Casat\Block\Customer;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;

class CompareProductLink extends \Magento\Framework\View\Element\Template
{

    protected $_registry;

    public function __construct(
        Registry $registry,
        Template\Context $context,
        array $data = [])
    {
        $this->_registry = $registry;
        parent::__construct($context, $data);
    }

    public function isNeedRefreshCustomerData()
    {
        return $this->_registry->registry('is_need_refresh_customer_data');
    }
}
