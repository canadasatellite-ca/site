<?php

namespace CanadaSatellite\DynamicsIntegration\Envelope;

class OrderItemEnvelopeFactory
{
	private $productRepository;
	private $productFactory;
	private $converterUtils;
	private $orderItemUtils;


	function __construct(
		\Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
		\CanadaSatellite\DynamicsIntegration\Envelope\ProductEnvelopeFactory $productFactory,
		\CanadaSatellite\DynamicsIntegration\Utils\ConverterUtils $converterUtils,
		\CanadaSatellite\DynamicsIntegration\Utils\OrderItemUtils $orderItemUtils,
		\CanadaSatellite\DynamicsIntegration\Logger\Logger $logger
	) {
		$this->productRepository = $productRepository;
		$this->productFactory = $productFactory;
		$this->converterUtils = $converterUtils;
		$this->orderItemUtils = $orderItemUtils;
		$this->logger = $logger;
	}

	function create($item, $cost)
	{
		$data = array();

		$sku = $item->getSku();
		$productId = $item->getProductId();

		$this->logger->info("Item sku: $sku");
		$this->logger->info("Item product id: $productId");

		// Get product by id, NOT SKU
		// There are bundle products with dynamic SKU - product in cart will have SKU made up of bundle items SKUs
		// Also there are product custom options with SKU that also change product SKU in cart
		$product = $this->productRepository->getById($productId);

		$data['sku'] = $sku;
		$data['product'] = $this->productFactory->create($product);

		$price = $item->getBasePrice();
		$this->logger->info("Item price: $price. Introspect: " . gettype($price));
		$price = $this->converterUtils->toFloat($price);
		$this->logger->info("Item price after converter: $price. Introspect: " . gettype($price));
		$data['price'] = $price;

		$qty = $item->getQtyOrdered();
		$this->logger->info("Item qty: $qty. Introspect: " . gettype($qty));
		$qty = $this->converterUtils->toFloat($qty);
		$this->logger->info("Item qty after converter: $qty. Introspect: " . gettype($qty));
		$data['qty'] = $qty;

		$tax = $item->getBaseTaxAmount();
		$this->logger->info("Item tax: $tax. Introspect: " . gettype($tax));
		$tax = $this->converterUtils->toFloat($tax);
		$this->logger->info("Item tax after converter: $tax. Introspect: " . gettype($tax));
		$data['tax'] = $tax;

		$discount = $item->getBaseDiscountAmount();
		$this->logger->info("Item discount: $discount. Introspect: " . gettype($discount));
		$discount = $this->converterUtils->toFloat($discount);
		$this->logger->info("Item discount after converter: $discount. Introspect: " . gettype($discount));
		$data['discount'] = $discount;

		$total = $item->getBaseRowTotal();
		$this->logger->info("Item total: $total. Introspect: " . gettype($total));
		$total = $this->converterUtils->toFloat($total);
		$this->logger->info("Item total after converter: $total. Introspect: " . gettype($total));
		$data['total'] = $total;

		$this->logger->info("Item options processing started");
		$options = $item->getProductOptions();

		try {
			$this->logger->info("Options got: " . json_encode($options));
		} catch (\Exception $e) {
			$this->logger->info("Failed to dump options: " . $e->getMessage() . "\r\nStack trace: " . $e->getTraceAsString());
		}

		$this->logger->info("Item cost in CAD: $cost");
		$data['cost'] = $cost;

		return $data;
	}
}
