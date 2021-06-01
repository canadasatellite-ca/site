<?php

namespace CanadaSatellite\DynamicsIntegration\Observer;

use Magento\Framework\Event\ObserverInterface;

class StockItemSaveAfterObserver implements ObserverInterface {
	private $logger;

	function __construct(
		\CanadaSatellite\DynamicsIntegration\Logger\Logger $logger
	) {
		$this->logger = $logger;
	}

	function execute(\Magento\Framework\Event\Observer $observer)
	{
		$this->logger->info("Stock changed event.");
	}
}
