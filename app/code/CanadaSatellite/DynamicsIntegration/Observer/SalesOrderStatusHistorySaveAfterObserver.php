<?php

namespace CanadaSatellite\DynamicsIntegration\Observer;

use Magento\Framework\Event\Observer;

class SalesOrderStatusHistorySaveAfterObserver implements \Magento\Framework\Event\ObserverInterface
{
	private $publisher;
	private $config;
	private $eventFactory;
	private $logger;

	function __construct(
		\CanadaSatellite\SimpleAmqp\Publisher $publisher,
		\CanadaSatellite\DynamicsIntegration\Config\Config $config,
		\CanadaSatellite\DynamicsIntegration\Event\EventFactory $eventFactory,
		\CanadaSatellite\DynamicsIntegration\Logger\Logger $logger
	)
	{
		$this->publisher = $publisher;
		$this->config = $config;
		$this->eventFactory = $eventFactory;
		$this->logger = $logger;
	}

	/**
	* Launches when "sales_order_status_history_save_after" event occures
	* @param Observer $observer
	*/
	function execute(Observer $observer)
	{
		try
		{
			$statusHistory = $observer->getEvent()->getStatusHistory();
			$comment = $statusHistory->getComment();
			# 2021-08-26 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
			# «Call to a member function getRealOrderId() on null
			# in app/code/CanadaSatellite/DynamicsIntegration/Observer/SalesOrderStatusHistorySaveAfterObserver.php:37»:
			# https://github.com/canadasatellite-ca/site/issues/203
			if (!$statusHistory->getOrder()) {
				df_log_l($this, [], df_class_l($this));
			}
			$orderId = $statusHistory->getOrder()->getRealOrderId();
			//$this->logger->info("[SalesOrderStatusHistorySaveAfterObserver]: Comment - " . json_encode($comment));
			//$this->logger->info("[SalesOrderStatusHistorySaveAfterObserver]: Entity - " . json_encode($statusHistory->getEntityId()) . "#" . $statusHistory->getEntityName());
			//$this->logger->info("[SalesOrderStatusHistorySaveAfterObserver]: OrderId - " . $orderId);

			$this->publisher->publish(
				$this->config->getIntegrationQueue(),
				$this->eventFactory->createOrderNoteCreatedEvent($orderId, $comment)
			);
		}
		catch (\Exception $e)
		{
			$this->logger->info("Failed at SalesOrderStatusHistorySaveAfterObserver: " . $e->getMessage());
		};
	}
}