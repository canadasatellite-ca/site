<?php

namespace Interactivated\Quotecheckout\Block\Sales\Info;

class Deliverydate extends \Magento\Framework\View\Element\Template
{
	/**
	 * @var \Magento\Framework\Registry
	 */
	protected $_coreRegistry;

	function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Framework\Registry $coreRegistry,
		array $data = []
	) {
		$this->_coreRegistry = $coreRegistry;
		parent::__construct($context, $data);
	}

	/**
	 * Retrive current order
	 * 
	 * @return \Magento\Sales\Model\Order
	 */
	function getOrderInformation()
	{
		return $this->_coreRegistry->registry('current_order');
	}
}
