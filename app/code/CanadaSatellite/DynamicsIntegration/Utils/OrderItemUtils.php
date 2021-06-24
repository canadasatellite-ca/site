<?php

namespace CanadaSatellite\DynamicsIntegration\Utils;

class OrderItemUtils
{
	private $customOptionsRepository;
	private $currencyUtils;
	private $productUtils;
	private $logger;

	function __construct(
		\Magento\Catalog\Api\ProductCustomOptionRepositoryInterface $customOptionsRepository,
		\CanadaSatellite\DynamicsIntegration\Utils\CurrencyUtils $currencyUtils,
		\CanadaSatellite\DynamicsIntegration\Utils\ProductUtils $productUtils,
		\CanadaSatellite\DynamicsIntegration\Logger\Logger $logger
	) {
		$this->customOptionsRepository = $customOptionsRepository;
		$this->currencyUtils = $currencyUtils;
		$this->productUtils = $productUtils;
		$this->logger = $logger;
	}

	/**
	 * @used-by \CanadaSatellite\DynamicsIntegration\Utils\OrderUtils::calculateVisibleItemsCosts()
	 * @param $item
	 * @return float|int|mixed
	 */
	function getItemBaseCost($item) {
		$this->logger->info("[OrderItemUtils::getItemBaseCost] Enter");
		$baseCost = $item->getBaseCost();
		$this->logger->info("[OrderItemUtils::getItemBaseCost] Item base cost: $baseCost");
		$product = $item->getProduct();
		$vendorCurrency = $this->productUtils->getVendorCurrency($product);
		$this->logger->info("[OrderItemUtils::getItemBaseCost] Vendor currency is: $vendorCurrency");
		if ($vendorCurrency !== 'CAD') {
			$this->logger->info("[OrderItemUtils::getItemBaseCost] Vendor currency is not CAD. Convert cost to CAD");
			$costCAD = $this->currencyUtils->convert($vendorCurrency, 'CAD', $baseCost);
			$this->logger->info("[OrderItemUtils::getItemBaseCost] Item cost in CAD: $costCAD");
			return $costCAD;
		}
		$this->logger->info("[OrderItemUtils::getItemBaseCost] Return item cost as is in CAD: $baseCost");
		return $baseCost;
	}

	function getItemOptionPrice($item, $optionId, $optionValueId)
	{
		$this->logger->info("[OrderItemUtils::getItemOptionPrice] Enter");

		$option = $this->getProductOption($item, $optionId);
		if ($option === null) {
			$this->logger->info("[OrderItemUtils::getItemOptionPrice] Option $optionId not found. Return zero");
			return 0.0;
		}

		$value = $this->getOptionValue($option, $optionValueId);
		if ($value === null) {
			$this->logger->info("[OrderItemUtils::getItemOptionPrice] Option $optionId value $optionValueId not found. Use option price");

			$price = $option->getPrice();
			if ($price === null || $price === false) {
				$this->logger->info("[OrderItemUtils::getItemOptionPrice] Option $optionId price not found. Return zero");
				return 0.0;
			}
			return $price;
		}

		$valueId = $value->getId();
		$valueTitle = $value->getTitle();
		// Call with flag=true to force value for percent price.
		$valuePrice = $value->getPrice(true);

		$this->logger->info("[OrderItemUtils::getItemOptionPrice] Product option $optionId value $valueId title $valueTitle price $valuePrice");
		return $valuePrice;
	}

	function getItemOptionCost($item, $optionId, $optionValueId)
	{
		$this->logger->info("[OrderItemUtils::getItemOptionCost] Enter");

		$option = $this->getProductOption($item, $optionId);
		if ($option === null) {
			$this->logger->info("[OrderItemUtils::getItemOptionCost] Option $optionId not found. Return zero");
			return 0.0;
		}

		$value = $this->getOptionValue($option, $optionValueId);
		if ($value === null) {
			$this->logger->info("[OrderItemUtils::getItemOptionCost] Option $optionId value $optionValueId not found. Use option cost");

			$cost = $option->getData('cost');
			if ($cost === null || $cost === false) {
				$this->logger->info("[OrderItemUtils::getItemOptionCost] Option $optionId cost not found. Return zero");
				return 0.0;
			}
			return $cost;
		}

		$valueId = $value->getId();
		$valueTitle = $value->getTitle();
		$valueCost = $value->getData('cost');

		$this->logger->info("[OrderItemUtils::getItemOptionPrice] Product option $optionId value $valueId title $valueTitle cost $valueCost");
		return $valueCost;
	}

	private function getProductOption($item, $optionId) 
	{
		$product = $item->getProduct();
		$option = $product->getOptionById($optionId);
		return $option;
	}

	private function getOptionValue($option, $optionValueId)
	{
		$value = $option->getValueById($optionValueId);
		return $value;
	}
}
