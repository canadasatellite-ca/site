<?php

namespace CanadaSatellite\DynamicsIntegration\Utils;

class OrderItemProfitCalculator
{
	private $item;
	private $quantity;
	private $priceTotal;
	private $costPerUnit;
	private $logger;

	public function __construct(
		\CanadaSatellite\DynamicsIntegration\Logger\Logger $logger,
		$item
	) {
		$this->item = $item;

		$this->quantity = $item->getQty();
		$this->priceTotal = $item->getTotal();
		// In base currency, CAD.
		$this->costPerUnit = $item->getCost(); 

		$this->logger = $logger;
	}

	public function calculateMargin()
	{
		$profit = $this->calculateProfit();
		$this->logger->info("[OrderItemProfitCalculator::calculateMargin] Order item profit $profit");
		$priceTotal = $this->getPriceTotal();
		$this->logger->info("[OrderItemProfitCalculator::calculateMargin] Order item total price $priceTotal");

		if ($priceTotal == 0) {
			$this->logger->info("[OrderItemProfitCalculator::calculateMargin] Order item total price <= zero. Return zero");
			return 0.0;
		}

		if ($profit <= 0) {
			$this->logger->info("[OrderItemProfitCalculator::calculateMargin] Order item profit <= zero. Return zero");
			return 0.0;
		}

		$margin = ($profit / $priceTotal) * 100;
		$this->logger->info("[OrderItemProfitCalculator::calculateMargin] Order item margin $margin");

		return $margin;
	}

	public function calculateProfit()
	{
		$priceTotal = $this->getPriceTotal();
		$this->logger->info("[OrderItemProfitCalculator::calculateProfit] Order item total price $priceTotal");
		$costTotal = $this->getCostTotal();
		$this->logger->info("[OrderItemProfitCalculator::calculateProfit] Order item total cost $costTotal");

		$profit = $priceTotal - $costTotal;
		$this->logger->info("[OrderItemProfitCalculator::calculateProfit] Order item profit $profit");

		return $profit;
	}

	private function getPriceTotal()
	{
		$price = $this->priceTotal;
		if ($price === null) {
			return 0.0;
		}

		return $price;
	}

	private function getCostTotal()
	{
		$costPerUnit = $this->getCostPerUnit();
		$this->logger->info("[OrderItemProfitCalculator::getCostTotal] Order item cost per unit $costPerUnit");
		$quantity = $this->getQuantity();
		$this->logger->info("[OrderItemProfitCalculator::getCostTotal] Order item quantity $quantity");

		return $costPerUnit * $quantity;
	}

	private function getCostPerUnit()
	{
		$item = $this->item;
		$product = $item->getProduct();

		$currentCost = $product->getCurrentCost();
		if ($currentCost !== null && $currentCost > 0) {
			$this->logger->info("[OrderItemProfitCalculator::getCostPerUnit] Current cost detected");
			return $currentCost;
		}

		$this->logger->info("[OrderItemProfitCalculator::getCostPerUnit] Calculate item standard cost");
		$calculator = new OrderItemStandardCostCalculator($this->logger, $item);
		$cost = $calculator->calculateStandardCost();
		$this->logger->info("[OrderItemProfitCalculator::getCostPerUnit] Item standard cost $cost");

		return $cost;
		/*$cost = $this->costPerUnit;
		if ($cost === null) {
			return 0.0;
		}

		return $cost;*/
	}

	private function getQuantity()
	{
		$quantity = $this->quantity;
		if ($quantity === null) {
			return 0;
		}

		return $quantity;
	}
}
