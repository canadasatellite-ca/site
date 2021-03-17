<?php

namespace MW\Onestepcheckout\Block\Adminhtml\Sales\Order\View\Tab\Info;

class DeliveryDate extends \Magento\Framework\View\Element\Template
{
	protected $_coreRegistry;

	/**
	 * @param \Magento\Framework\View\Element\Template\Context $context
	 * @param \Magento\Framework\Registry $coreRegistry
	 * @param array $data
	 */
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Framework\Registry $coreRegistry,
		array $data = []
	) {
		$this->_coreRegistry = $coreRegistry;
		parent::__construct($context, $data);
	}

	public function getCurrentOrder()
	{
		return $this->_coreRegistry->registry('current_order');
	}
}
