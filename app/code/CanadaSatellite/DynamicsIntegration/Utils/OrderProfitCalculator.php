<?php

namespace CanadaSatellite\DynamicsIntegration\Utils;

class OrderProfitCalculator
{
	private $items;
	private $logger;

	function __construct(\CanadaSatellite\DynamicsIntegration\Logger\Logger $logger, $items)
	{
		$this->items = $items;
		$this->logger = $logger;
	}

	function calculateMargin()
	{
		$profit = $this->calculateProfit();
		$revenue = $this->calculateRevenue();

		if ($revenue <= 0) {
			$this->logger->info("[OrderProfitCalculator::calculateMargin] Order price <= zero. Return zero");
			return 0.0;
		}

		if ($profit <= 0) {
			$this->logger->info("[OrderProfitCalculator::calculateMargin] Order profit <= zero. Return zero");
			return 0.0;
		}

		return ($profit / $revenue) * 100;
	}

	function calculateProfit()
	{
		$this->logger->info("[OrderProfitCalculator::calculateProfit] Enter");

		$totalProfit = 0.0;

		foreach ($this->items as $item) {
			$itemProfitCalculator = new OrderItemProfitCalculator($this->logger, $item);
			$itemProfit = $itemProfitCalculator->calculateProfit();
			$this->logger->info("[OrderProfitCalculator::calculateProfit] Item profit: $itemProfit");

			$totalProfit += $itemProfit;
		}

		$this->logger->info("[OrderProfitCalculator::calculateProfit] Order profit: $totalProfit");
		return $totalProfit;
	}

	private function calculateRevenue() {
		$this->logger->info("[OrderProfitCalculator::calculateRevenue] Enter");

		$revenue = 0.0;

		foreach ($this->items as $item) {
			$itemTotal = $item->getTotal();
			$this->logger->info("[OrderProfitCalculator::calculateRevenue] Item total: $itemTotal");

			$revenue += $itemTotal;
		}

		$this->logger->info("[OrderProfitCalculator::calculateRevenue] Order revenue: $revenue");
		return $revenue;
	}
}