<?php

namespace CanadaSatellite\DynamicsIntegration\Observer;

use Magento\Framework\Event\ObserverInterface;

class ProductDeleteAfterObserver implements ObserverInterface {
	private $publisher;
	private $config;
	private $eventFactory;
	private $logger;

	public function __construct(
		\CanadaSatellite\SimpleAmqp\Publisher $publisher,
		\CanadaSatellite\DynamicsIntegration\Config\Config $config,
		\CanadaSatellite\DynamicsIntegration\Event\EventFactory $eventFactory,
		\CanadaSatellite\DynamicsIntegration\Logger\Logger $logger
	) {
		$this->publisher = $publisher;
		$this->config = $config;
		$this->eventFactory = $eventFactory;
		$this->logger = $logger;
	}

	public function execute(\Magento\Framework\Event\Observer $observer) {
		try {
			$this->logger->info("[ProductDeleteAfterObserver] -> start");

			$product = $observer->getProduct();

			$productId = $product->getId();
			$sku = $product->getSku();
			
			$this->logger->info("Product $productId ($sku) deleted.");

			$this->publisher->publish($this->config->getIntegrationQueue(), $this->eventFactory->createProductDeletedEvent($productId, $sku));

			$this->logger->info("[ProductDeleteAfterObserver] -> end");
		}
		catch (\Exception $e) {
			$this->logger->info("Failed at ProductDeleteAfterObserver: " . $e->getMessage());
		}
	}
}