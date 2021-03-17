<?php

namespace MW\Onestepcheckout\Observer;

use Magento\Framework\Event\ObserverInterface;

class SaveOrderBefore implements ObserverInterface
{
	/**
	 * @var \MW\Onestepcheckout\Helper\Data
	 */
	protected $_dataHelper;

	/**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $_sessionManager;

    /**
	 * @var \Magento\Framework\Logger\Monolog
	 */
	protected $_logger;

    /**
     * @param \MW\Onestepcheckout\Helper\Data $dataHelper
     * @param \Magento\Framework\Session\SessionManagerInterface $sessionManager
     * @param \Magento\Framework\Logger\Monolog $logger
     */
    public function __construct(
    	\MW\Onestepcheckout\Helper\Data $dataHelper,
    	\Magento\Framework\Session\SessionManagerInterface $sessionManager,
    	\Magento\Framework\Logger\Monolog $logger
    ) {
    	$this->_dataHelper = $dataHelper;
    	$this->_sessionManager = $sessionManager;
    	$this->_logger = $logger;
    }

	/**
	 * Set delivery date information before save order
	 *
	 * @param  \Magento\Framework\Event\Observer $observer
	 * @return $this
	 */
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		if (!$this->_dataHelper->enabledInFrontend()) {
			return $this;
		}

		$order = $observer->getEvent()->getOrder();
		try {
			if ($this->_sessionManager->getDeliveryInforOrder()) {
				$deliveryinfor = $this->_sessionManager->getDeliveryInforOrder();
				$customercomment = $deliveryinfor[0]; // comment
				$deliverystatus = $deliveryinfor[1]; // deliverydate
				$deliverydate = $deliveryinfor[2]; // checkoutdate
				$deliverytime = $deliveryinfor[3]; // checkouttime

				$order->setMwCustomercommentInfo($customercomment);
				if ($deliverystatus == "late") {
					$order->setMwDeliverydateDate($deliverydate);
					$order->setMwDeliverydateTime($deliverytime);
				} else if ($deliverystatus == "now") {
					$order->setMwDeliverydateDate(__('As soon as possible'));
					$order->setMwDeliverydateTime('');
				}
				$this->_sessionManager->unsDeliveryInforOrder();
			}
		} catch (\Exception $e) {
			$this->_logger->critical($e);
		}

		return $this;
	}
}
