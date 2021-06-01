<?php

namespace CanadaSatellite\DynamicsIntegration\Utils;

class OrderItemStandardCostCalculator
{
	private $item;
	private $logger;

	function __construct(
		\CanadaSatellite\DynamicsIntegration\Logger\Logger $logger,
		$item
	) {
		$this->item = $item;
		$this->logger = $logger;
	}

	function calculateStandardCost()
 	{
 		// All calculations for order/items are performed in CAD.
 		$baseCost = $this->getBaseCost();	
 		$this->logger->info("[calculateStandardCost] Base cost: $baseCost");
 		$shippingCost = $this->getShippingCost();
 		$this->logger->info("[calculateStandardCost] Shipping cost: $shippingCost");
 		$currencyExchange = $this->calculateCurrencyExchange();
 		$this->logger->info("[calculateStandardCost] Currency exchange $currencyExchange");
 		$processingFees = $this->calculateProcessingFees();
 		$this->logger->info("[calculateStandardCost] Processing fees $processingFees");

 		return $baseCost + $shippingCost + $currencyExchange + $processingFees;
 	}

 	private function calculateCurrencyExchange()
 	{
 		$item = $this->item;
 		$product = $item->getProduct();

 		if (!$product->hasPriceUsd()) {
 			return 0.0;
 		}

 		$baseCost = $this->getBaseCost();
 		$shippingCost = $this->getShippingCost();
 		return ($baseCost + $shippingCost) * 0.02;
 	}

 	private function calculateProcessingFees()
 	{
 		$price = $this->getPrice();

 		return $price * 0.03;
 	}

 	private function getBaseCost()
 	{
 		$item = $this->item;

 		$baseCost = $item->getCost();
 		if ($baseCost === null) {
 			return 0.0;
 		}

 		return $baseCost;
 	}

 	private function getShippingCost()
 	{
 		$item = $this->item;
 		$product = $item->getProduct();

 		$shippingCost = $product->getShippingCost();
 		if ($shippingCost === null) {
 			return 0.0;
 		}

 		return $shippingCost;
 	}

 	private function getPrice()
 	{
 		$item = $this->item;
 		$product = $item->getProduct();

 		$price = $item->getPrice();
 		if ($price === null) {
 			return 0.0;
 		}

 		if ($product->hasPriceUsd()) {
 			// For products with USD price, 3% of processing fees are already included in order item price. Re-calculate raw price.
 			return $price / 1.03;
 		}

 		return $price;
 	}
}
