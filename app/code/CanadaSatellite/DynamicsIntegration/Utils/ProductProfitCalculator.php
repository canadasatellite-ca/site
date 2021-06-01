<?php

namespace CanadaSatellite\DynamicsIntegration\Utils;

class ProductProfitCalculator
{
	private $product;
	private $shippingCost;
	private $currentCost;
	private $salePrice;

	private $optionsPrice;
	private $optionsCost;

	private $logger;

	function __construct(
		\CanadaSatellite\DynamicsIntegration\Logger\Logger $logger,
		$product, 
		$shippingCost = null,
		$currentCost = null,
		$salePrice = null,
		$optionsPrice = null,
		$optionsCost = null
	) {
		$this->product = $product;
		$this->shippingCost = $shippingCost;
		$this->currentCost = $currentCost;
		$this->salePrice = $salePrice;

		$this->optionsPrice = $optionsPrice;
		$this->optionsCost = $optionsCost;

		$this->logger = $logger;
	}

	function calculateMargin()
 	{
 		$profit = $this->calculateProfit();
 		$price = $this->getPrice();

 		if ($price == 0) {
 			return 0.0;
		}
		 
		if ($profit <= 0) {
			return 0.0;
	    }

		return ($profit / $price) * 100;
 	}

	function calculateProfit()
	{
		$this->logger->info("[ProductProfitCalculator::calculateProfit] Product shipping cost " . $this->shippingCost);
		$this->logger->info("[ProductProfitCalculator::calculateProfit] Product current cost " . $this->currentCost);
		$this->logger->info("[ProductProfitCalculator::calculateProfit] Product sale price " . $this->salePrice);

		$product = $this->product;

		$currentCost = $this->getCurrentCost();
 		$price = $this->getPrice();

 		return $price - $currentCost;
 	}

 	function calculateStandardCost()
 	{
 		$baseCost = $this->getBaseCost();	
 		$shippingCost = $this->getShippingCost();
 		$currencyExchange = $this->calculateCurrencyExchange();
 		$processingFees = $this->calculateProcessingFees();

 		return $baseCost + $shippingCost + $currencyExchange + $processingFees;
 	}

 	function calculateCurrencyExchange()
 	{
 		$product = $this->product;

 		if (!$product->hasPriceUsd()) {
 			return 0.0;
 		}

 		$baseCost = $this->getBaseCost();
 		$shippingCost = $this->getShippingCost();
 		return ($baseCost + $shippingCost) * 0.02;
 	}

 	function calculateProcessingFees()
 	{
 		$price = $this->getPrice();

 		return $price * 0.03;
 	}

 	private function getCurrentCost()
 	{
 		$currentCost = $this->currentCost;

 		if ($currentCost === null || $currentCost == 0.0) {
 			$currentCost = $this->calculateStandardCost();
 		} else {
 			$this->logger->info("[ProductProfitCalculator::calculateProfit] Current cost detected.");
 		}

 		return $currentCost;
 	}

 	private function getBaseCost()
 	{
 		$product = $this->product;

 		// Assume that cost and options cost are in the same currency as product price.
 		$optionsCost = $this->getOptionsCost();

 		$baseCost = $product->getCost();
 		if ($baseCost === null) {
 			$baseCost = 0.0;
 		}

 		return $baseCost + $optionsCost;
 	}

 	private function getShippingCost()
 	{
 		$shippingCost = $this->shippingCost;
 		if ($shippingCost === null) {
 			return 0.0;
 		}

 		return $shippingCost;
 	}

 	private function getPrice()
 	{
 		$product = $this->product;
 		$salePrice = $this->salePrice;

 		// Assume that options price is in the same currency as product price.
 		$optionsPrice = $this->getOptionsPrice();

 		$price = 0.0;
 		if ($salePrice !== null) {
 			$price = $salePrice;
 		} else if ($product->hasPriceUsd()) {
 			$price = $product->getPriceUsd();
 		} else {
 			$price = $product->getPrice();
 		}

 		return $price + $optionsPrice;
 	}

 	private function getOptionsPrice() 
 	{
 		$optionsPrice = $this->optionsPrice;
 		if ($optionsPrice === null) {
 			return 0.0;
 		}

 		return $optionsPrice;
 	}

 	private function getOptionsCost()
 	{
 		$optionsCost = $this->optionsCost;
 		if ($optionsCost === null) {
 			return 0.0;
 		}

 		return $optionsCost;
 	}
}
