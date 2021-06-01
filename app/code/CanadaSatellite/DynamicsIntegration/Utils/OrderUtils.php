<?php

namespace CanadaSatellite\DynamicsIntegration\Utils;

class OrderUtils
{
	private $orderItemUtils;
	private $logger;

	function __construct(
		\CanadaSatellite\DynamicsIntegration\Utils\OrderItemUtils $orderItemUtils,
		\CanadaSatellite\DynamicsIntegration\Logger\Logger $logger
	) {
		$this->orderItemUtils = $orderItemUtils;
		$this->logger = $logger;
	}

	function getCreatedAt($order) {
		$createdAt = $order->getCreatedAt();
		if ($createdAt === null) {
			return null;
		}

		$date = \DateTime::createFromFormat('Y-m-d H:i:s', $createdAt);
		if ($date === false) {
			$errors = \DateTime::getLastErrors();
			$msg = json_encode($errors);
			$this->logger->info("Error while parsing '$createdAt' date: $msg");
			return null;
		}

		return $date->format('Y-m-d\TH:i:s\Z');
	}

	function getShipmentDate($order) {
		$shipmentDateUtc = null;
		foreach ($order->getShipmentsCollection() as $shipment) {
			// Take first.
			$shipmentDateUtc = $shipment->getCreatedAt();
			break;
		}

		if (empty($shipmentDateUtc)) {
			return null;
		}
		return (new \DateTime($shipmentDateUtc))->format("Y-m-d\TH:i:s\Z");
	}

	function calculateVisibleItemsCosts($order)
	{
		// Item quote id => item aggregate cost over all children. 
		// Cart may have multiple items with same SKU under unclear conditions, so SKU is not reliable enough as unique identifier.
		$costs = array();

		// Process child items first to be sure that all children processed when calculating parent's cost.
		foreach ($order->getAllItems() as $item) {
			$parent = $item->getParentItem();
			if (!$parent) {
				continue;
			}

			$parentQuoteId = $parent->getQuoteItemId();
			$cost = $this->orderItemUtils->getItemBaseCost($item);
			$qty = $item->getQtyOrdered();
				
			$this->logger->info("[OrderUtils::calculateVisibleItemsCosts] Child item cost $cost for $parentQuoteId qty $qty");

			if (!isset($costs[$parentQuoteId])) {
				$costs[$parentQuoteId] = 0;
			}

			// Bundle items may have configurable quantity, thus count it into cost.
			$costs[$parentQuoteId] += $cost * $qty;
		}

		// Calculate parent's costs.
		foreach ($order->getAllItems() as $item) {
			$parent = $item->getParentItem();
			if ($parent) {
				continue;
			}

			$quoteId = $item->getQuoteItemId();
			$cost = $this->orderItemUtils->getItemBaseCost($item);
			$qty = $item->getQtyOrdered();

			$this->logger->info("[OrderUtils::calculateVisibleItemsCosts] Parent cost $cost for $quoteId qty $qty");

			if (!isset($costs[$quoteId])) {
				// Item has no children. Calculate cost as is.

				$costs[$quoteId] = $cost;
				$this->logger->info("[OrderUtils::calculateVisibleItemsCosts] Simple product cost $cost for $quoteId qty $qty");		
			} else {
				// Item has children. Divide calculated cost by item qty to get bundle cost per unit.

				$costs[$quoteId] += $cost;
				$this->logger->info("[OrderUtils::calculateVisibleItemsCosts] Bundle product cost $cost for $quoteId qty $qty");

				$totalCost = $costs[$quoteId];
				$this->logger->info("[OrderUtils::calculateVisibleItemsCosts] Bundle product total cost $totalCost for $quoteId");

				$costPerUnit = $totalCost / $qty;
				$this->logger->info("[OrderUtils::calculateVisibleItemsCosts] Bundle product cost per unit $costPerUnit for $quoteId");

				$costs[$quoteId] = $costPerUnit;
			}
		}

		return $costs;
	}

	/**
	 $costs - pre-calculated costs array for visible items.
	 */
	function getVisibleItemBaseCost($item, $costs)
	{
		$quoteId = $item->getQuoteItemId();

		return $costs[$quoteId];
	}
}
