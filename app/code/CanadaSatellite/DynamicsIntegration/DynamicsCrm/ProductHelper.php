<?php

namespace CanadaSatellite\DynamicsIntegration\DynamicsCrm;

use CanadaSatellite\DynamicsIntegration\Utils\ProductProfitCalculator;

class ProductHelper
{
	private $productComposer;
	private $priceListHelper;
	private $restApi;
	private $logger;

	function __construct(
		\CanadaSatellite\DynamicsIntegration\DynamicsCrm\ProductModelComposer $productComposer,
		\CanadaSatellite\DynamicsIntegration\DynamicsCrm\PriceListHelper $priceListHelper,
		\CanadaSatellite\DynamicsIntegration\Rest\RestApi $restApi,
		\CanadaSatellite\DynamicsIntegration\Logger\Logger $logger
	) {
		$this->productComposer = $productComposer;
		$this->priceListHelper = $priceListHelper;
		$this->restApi = $restApi;
		$this->logger = $logger;
	}

	/**
	 * @param string $sku
	 * @param array() product.
	 */
	function createOrUpdate($product)
	{
		$this->logger->info("[createOrUpdateProduct] Enter");

		$sku = $product->getSku();
		$this->logger->info("[createOrUpdateProduct] Finding product by sku $sku ...");

		$productId = $this->restApi->findProductIdBySku($sku);
		if ($productId === false) {
			$this->logger->info("[createOrUpdateProduct] Product not found. Creating...");
			$crmProduct = $this->productComposer->compose($product);
			$productId = $this->restApi->createProduct($crmProduct);
			$this->logger->info("[createOrUpdateProduct] Product created with id $productId");

			$this->logger->info("[createOrUpdateProduct] Creating product price list item - getting default price list...");
			$crmPriceListItem = $this->productComposer->composePriceListItem($product, $productId);
			$priceListId = $this->priceListHelper->getDefaultPriceListId();

			$this->logger->info("[createOrUpdateProduct] Creating product price list item - processing...");
			$productPriceLevelId = $this->restApi->createProductPriceLevel($productId, $crmPriceListItem);	
			$this->logger->info("[createOrUpdateProduct] Product price list level created with id $productPriceLevelId");
		}
		else {
			$this->logger->info("[createOrUpdateProduct] Product found with id $productId. Updating product...");
			$crmProduct = $this->productComposer->compose($product);
			$this->restApi->updateProduct($productId, $crmProduct);
			$this->logger->info("[createOrUpdateProduct] Product updated");

			$this->logger->info("[createOrUpdateProduct] Updating product price list item - getting default price list...");
			$crmPriceListItem = $this->productComposer->composePriceListItem($product, $productId);
			$priceListId = $this->priceListHelper->getDefaultPriceListId();

			$this->logger->info("[createOrUpdateProduct] Updating product price list item - finding item in default price list for product...");
			$productPriceLevelId = $this->restApi->findProductPriceLevelIdByProductId($productId, $priceListId);
			if ($productPriceLevelId === false) {
				$this->logger->info("[createOrUpdateProduct] Product price list item not found. Creating product price level...");
				$productPriceLevelId = $this->restApi->createProductPriceLevel($productId, $crmPriceListItem);
				$this->logger->info("[createOrUpdateProduct] Product price list item created");
			}
			else {
				$this->logger->info("[createOrUpdateProduct] Product price level found with id $productPriceLevelId . Updating ...");
				$this->restApi->updateProductPriceLevel($productPriceLevelId, $crmPriceListItem);
				$this->logger->info("[createOrUpdateProduct] Product price list item updated");
			}
		}

		// TODO: Update profit/margins.
		$this->logger->info("[ProductHelper::createOrUpdateProduct] Start calculating profit/margin for product");
		$crmProduct = $this->restApi->getProductById($productId);

		//$this->logger->info("Product from CRM:" . var_export($crmProduct, true));
		$calculator = new ProductProfitCalculator($this->logger, $product, $crmProduct->new_shippingcost, $crmProduct->currentcost, $crmProduct->new_saleprice);

		$currencyExchange = $calculator->calculateCurrencyExchange();
		$this->logger->info("[ProductHelper::createOrUpdateProduct] Currency exchange for product: $currencyExchange");

		$processingFees = $calculator->calculateProcessingFees();
		$this->logger->info("[ProductHelper::createOrUpdateProduct] Processing fees for product: $processingFees");
		
		$standardCost = $calculator->calculateStandardCost();
		$this->logger->info("[ProductHelper::createOrUpdateProduct] Standard cost for product: $standardCost");

		$profit = $calculator->calculateProfit();
		$this->logger->info("[ProductHelper::createOrUpdateProduct] Profit for product: $profit");
		$margin = $calculator->calculateMargin();
		$this->logger->info("[ProductHelper::createOrUpdateProduct] Margin for product: $margin");

		$profitData = array(
			'new_currencyexchange' => $currencyExchange,
			'new_processingfees' => $processingFees,
			'standardcost' => $standardCost,
			'new_profit' => $profit,
			'new_margin' => $margin,
		);

		$this->logger->info("[ProductHelper::createOrUpdateProduct] Trying to update product profit/margin...");
		$this->restApi->updateProduct($productId, $profitData);
		$this->logger->info("[ProductHelper::createOrUpdateProduct] Product profit/margin updated");

		$this->logger->info("[createOrUpdateProduct] Exit");

		return $this->restApi->getProductById($productId);
	}
}