<?php
	
	namespace Digit\OrderButtons\Block\Widget\Button;
	
	use Magento\Backend\Block\Widget\Button\Toolbar as ToolbarContext;
	use Magento\Framework\View\Element\AbstractBlock;
	use Magento\Backend\Block\Widget\Button\ButtonList;
	
	class Toolbar
	{
		protected $_logger;
		protected $_context;
		
		protected $_objectManager;
		protected $_registry;
		public function __construct(
		
        \Psr\Log\LoggerInterface $logger,
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\Magento\Framework\Registry $registry,
        array $data = []
		)
		{
			
			$this->_logger = $logger;
			$this->_registry = $registry;
			$this->_objectManager = $objectManager;
		}
		/**
			* @param ToolbarContext $toolbar
			* @param AbstractBlock $context
			* @param ButtonList $buttonList
			* @return array
		*/
		public function beforePushButtons(
		ToolbarContext $toolbar,
        AbstractBlock $context,
        ButtonList $buttonList
		) {
			
			if (!$context instanceof \Magento\Sales\Block\Adminhtml\Order\View) {
				return [$context, $buttonList];
			}
			$buttonList->add('order_prev',
            [
			'label' => __(''),
			'onclick' => 'setLocation(\'' . $this->getPreviousOrderUrl() . '\')',
			'class' => 'order-previous order-next-previous-buttons'
            ]
			);
			$buttonList->add('order_next',
            [
			'label' => __(''),
			'onclick' => 'setLocation(\'' . $this->getNextOrderUrl(). '\')',
			'class' => 'order-next order-next-previous-buttons'
            ]
			);
			$buttonList->add('digit_print',
            [
			'label' => __('Print Order'),
			'onclick' => 'setLocation(\'' . $this->getOrderPdfPrintUrl(). '\')',
			'class' => 'print'
            ]
			);
			$buttonList->add('digit_print_packing',
            [
			'label' => __('Print Packing Slip'),
			'onclick' => 'setLocation(\'' . $this->getPackingSlipPdfPrintUrl(). '\')',
			'class' => 'print'
            ]
			);
			return [$context, $buttonList];
		}
		/**
			* @return string
		*/
		public function getPackingSlipPdfPrintUrl()
		{
			$_helper = $this->_objectManager->create('\Magento\Backend\Helper\Data');
			return $_helper->getUrl('digit_orderbuttons/packingslip/print/order_id/' . $this->getOrderId());
		}
		/**
			* @return string
		*/
		public function getOrderPdfPrintUrl()
		{
			$_helper = $this->_objectManager->create('\Magento\Backend\Helper\Data');
			return $_helper->getUrl('digit_orderbuttons/order/print/order_id/' . $this->getOrderId());
		}
		
		/**
			* @return integer
		*/
		public function getOrderId()
		{
			return  $this->_registry->registry('current_order')->getId();;
		}
		
		/**
			* @return Mage_Sales_Model_Order
		*/
		public function getPreviousOrderUrl()
		{
			$orderIds = $this->getOrderIds();
			$currentId = $this->_registry->registry('current_order')->getId();
			//$this->_logger->addDebug('after currentId'); // log location: var/log/system.log
			//$this->_logger->addDebug('after currentId'.$currentId); // log location: var/log/system.log
			$currentKey = array_search($currentId, $orderIds);
			
			//$this->_logger->addDebug('after currentKey'.$currentKey); // log location: var/log/system.log
			$previousKey = $currentKey - 1;
			if($previousKey >= 0 && isset($orderIds[$previousKey])) {
				$previousId = $orderIds[$previousKey];
				//$this->_logger->addDebug('after previousId'.$previousId); // log location: var/log/system.log
				$_helper = $this->_objectManager->create('\Magento\Backend\Helper\Data');
				return $_helper->getUrl('sales/order/view/order_id/'.$previousId);
				//return $this->_context->getUrl('sales/order/view/order_id/'.$previousId);
				
			}
		}
		
		/**
			* @return Mage_Sales_Model_Order
		*/
		public function getNextOrderUrl()
		{
			//$this->_logger->addDebug('getNext'); // log location: var/log/system.log
			$orderIds = $this->getOrderIds();
			//$this->_logger->addDebug('before currentId'); // log location: var/log/system.log
			$currentId = $this->_registry->registry('current_order')->getId();
			//$this->_logger->addDebug('after currentId'); // log location: var/log/system.log
			//$this->_logger->addDebug('after currentId'.$currentId); // log location: var/log/system.log
			$currentKey = array_search($currentId, $orderIds);
			$nextKey = $currentKey + 1;
			if(isset($orderIds[$nextKey])) {
				$nextId = $orderIds[$nextKey];
				//$this->_logger->addDebug('after nextId'.$nextId); // log location: var/log/system.log
				$_helper = $this->_objectManager->create('\Magento\Backend\Helper\Data');
				return $_helper->getUrl('sales/order/view/order_id/'.$nextId);
				// $this->_context->getUrl('sales/order/view/order_id/'.$nextId);
				
			}
		}
		
		/**
			* @return array
		*/
		public function getOrderIds()
		{
			//$this->_logger->addDebug('getOrderIds'); // log location: var/log/system.log
			$collection = $this->_objectManager->create(
			'\Magento\Sales\Model\ResourceModel\Order\Collection'
			);
			$orderIds = $collection->getAllIds();
			//$this->_logger->addDebug('orderIds='.implode(",",$orderIds)); // log location: var/log/system.log
			return $orderIds;
		}
	}																										