<?php

namespace CanadaSatellite\DynamicsIntegration\Updater;

class ProductUpdater {
	private $productRepository;
	private $crm;
	private $logger;

	function __construct(
		\Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
		\CanadaSatellite\DynamicsIntegration\DynamicsCrm\DynamicsCrm $crm,
		\CanadaSatellite\DynamicsIntegration\Logger\Logger $logger
	) {
		$this->productRepository = $productRepository;
		$this->crm = $crm;
		$this->logger = $logger;
	}

	function createOrUpdate($product, $sku) {
		$this->logger->info("Try to update products in CRM.");
		$crmId = $this->crm->createOrUpdateProduct($sku, $product);
		$this->logger->info("Product created/updated in CRM with id $crmId.");
	}

	function delete($sku) {		
		$this->logger->info("Try to delete product in CRM.");
		$this->crm->deleteProduct($sku);
		$this->logger->info("Product deleted in CRM.");
	}
}