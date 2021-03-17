<?php

namespace CanadaSatellite\DynamicsIntegration\Observer;

use Magento\Framework\Event\ObserverInterface;

class ProductSaveAfterObserver implements ObserverInterface {
	private $productRepository;
	private $publisher;
	private $config;
	private $envelopeFactory;
	private $eventFactory;
	private $logger;

	public function __construct(
		\Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
		\CanadaSatellite\SimpleAmqp\Publisher $publisher,
		\CanadaSatellite\DynamicsIntegration\Config\Config $config,
		\CanadaSatellite\DynamicsIntegration\Envelope\ProductEnvelopeFactory $envelopeFactory,
		\CanadaSatellite\DynamicsIntegration\Event\EventFactory $eventFactory,
		\CanadaSatellite\DynamicsIntegration\Logger\Logger $logger
	) {
		$this->productRepository = $productRepository;
		$this->publisher = $publisher;
		$this->config = $config;
		$this->envelopeFactory = $envelopeFactory;
		$this->eventFactory = $eventFactory;
		$this->logger = $logger;
	}

	public function execute(\Magento\Framework\Event\Observer $observer) {
		try {
			$this->logger->info("[ProductSaveAfterObserver] -> start");

			$product = $observer->getProduct();

			$productId = $product->getId();
			$sku = $product->getSku();		
			$this->logger->info("Product $productId ($sku) saved.");

			$originalSku = $this->getSkuFromOriginalData($product);
			$this->publisher->publish(
				$this->config->getIntegrationQueue(), 
				$this->eventFactory->createProductSavedEvent(
					$productId, 
					$originalSku,
					// Force reload from repository
					$this->envelopeFactory->create($this->productRepository->getById($productId, false, null, true))
				)
			);

			$this->logger->info("[ProductSaveAfterObserver] -> end");
		}
		catch (\Exception $e) {
			$this->logger->info("Failed at ProductSaveAfterObserver: " . $e->getMessage());
		}
	}

	private function getSkuFromOriginalData($product) {
		$sku = $product->getSku();
		$old_sku = $product->getOrigData('sku');
		$this->logger->info("Original SKU: $old_sku");

		$search_sku = $sku;
		if (!empty($old_sku) && $old_sku != $sku) {
			$search_sku = $old_sku;
		}

		return $search_sku;
	}
}