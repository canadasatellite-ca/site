<?php
namespace CanadaSatellite\DynamicsIntegration\Utils;
use Magento\Catalog\Model\Product as P;
class ProductUtils {
	private $frontUrlModel;
	private $productRepository;
	private $stockRegistry;
	private $eavUtils;
	private $currencyUtils;
	private $converterUtils;
	private $logger;

	function __construct(
		\Magento\Framework\Url $frontUrlModel,
		\Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
		\Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
		\CanadaSatellite\DynamicsIntegration\Utils\EavUtils $eavUtils,
		\CanadaSatellite\DynamicsIntegration\Utils\CurrencyUtils $currencyUtils,
		\CanadaSatellite\DynamicsIntegration\Utils\ConverterUtils $converterUtils,
		\CanadaSatellite\DynamicsIntegration\Logger\Logger $logger
	) {
		$this->frontUrlModel = $frontUrlModel;
		$this->productRepository = $productRepository;
		$this->stockRegistry = $stockRegistry;
		$this->eavUtils = $eavUtils;
		$this->currencyUtils = $currencyUtils;
		$this->converterUtils = $converterUtils;
		$this->logger = $logger;
	}

	/**
	 * @used-by \CanadaSatellite\DynamicsIntegration\Utils\OrderItemUtils::getItemBaseCost()
	 * @param P $p
	 * @return mixed|string|null
	 */
	function getVendorCurrency(P $p) {
		$this->logger->info("[getVendorCurrency] Enter");
		$r = $this->eavUtils->getDropdownAttributeValue($p, 'vendor_currency');
		$this->logger->info("[getVendorCurrency] Vendor currency is: $r. Introspect: " . gettype($r));
		if ($r === null) {
			$this->logger->info("[getPrice] Vendor currency is not set. Fallback to CAD");
			$r = 'CAD';
		}
		return $r;
	}

	function getQty($product)
	{
		$productId = $product->getId();
		$stock = $this->stockRegistry->getStockItem($productId);
		
		if ($stock === null) {
			return null;
		}

		return $stock->getQty();
	}

	function getUrl($product)
	{
		$routeParams = ['_nosid' => true];

		$baseUrl = $this->frontUrlModel->getUrl(null, $routeParams);

		return $baseUrl . $product->getUrlKey() . '.htm';
	}

	/**
	 * @param Magento\Catalog\Api\Data\ProductInterface $product
	 * @return float|null Price in CAD.
	 */
	function getPrice($product)
	{
		$this->logger->info("[getPrice] -> Enter");

		$price = $this->doGetPrice($product);
		$this->logger->info("[getPrice] Product price: " . $price);

		$vendorCurrency = $this->eavUtils->getDropdownAttributeValue($product, 'vendor_currency');
		$this->logger->info("[getPrice] Vendor currency is: $vendorCurrency. Introspect: " . gettype($vendorCurrency));

		if ($vendorCurrency === null) {
			$this->logger->info("[getPrice] Vendor currency is not set. Fallback to CAD");
			$vendorCurrency = 'CAD';
		}

		if ($vendorCurrency === 'CAD') {
			$this->logger->info("[getPrice] Vendor currency is CAD. Returning price as is.");
			return $price;
		}

		if ($vendorCurrency === 'EUR') {
			$this->logger->info("[getPrice] Vendor currency is EUR. Returning price as is.");
			return $price;
		}

		if ($vendorCurrency === 'USD') {
			$usdPriceIsBase = $this->eavUtils->getBooleanAttributeValue($product, 'usd_is_base_price');
			$this->logger->info("[getPrice] USD Price Is Base: $usdPriceIsBase. Introspect: " . gettype($usdPriceIsBase));
			if (!$usdPriceIsBase) {
				$this->logger->info("[getPrice] USD Price Is Base is not set. Returning price as is.");
				return $price;
			}

			$usdPrice = $this->eavUtils->getDecimalAttributeValue($product, 'price_usd');
			$this->logger->info("[getPrice] USD Price: $usdPrice. Introspect: " . gettype($usdPrice));
			if ($usdPrice === null) {
				$this->logger->info("[getPrice] USD Price is not set. Returning price as is.");
				return $price;
			}

			// Use USD price as base.
			// TODO: Convert to CAD.
			$this->logger->info("[getPrice] Use USD price and base. Convert it to CAD");
			$usdPrice = $this->converterUtils->toFloat($usdPrice);

			$basePrice = $this->currencyUtils->convert('USD', 'CAD', $usdPrice);
			$this->logger->info("[getPrice] Price in CAD: $basePrice");

			return $basePrice;
		}
		else {
			$this->logger->info("[getPrice] Unknown currency. Return as price as is.");
			return $price;
		}
	}

	function getPriceUsd($product)
	{
		$this->logger->info("[getPriceUsd] -> Enter");

		$vendorCurrency = $this->eavUtils->getDropdownAttributeValue($product, 'vendor_currency');
		$this->logger->info("[getPriceUsd] Vendor currency is: $vendorCurrency. Introspect: " . gettype($vendorCurrency));
		if ($vendorCurrency !== 'USD') {
			$this->logger->info("[getPriceUsd] Vendor currency is not USD");
			return null;
		}

		$usdPriceIsBase = $this->eavUtils->getBooleanAttributeValue($product, 'usd_is_base_price');
		$this->logger->info("[getPriceUsd] USD Price Is Base: $usdPriceIsBase. Introspect: " . gettype($usdPriceIsBase));

		$usdPrice = $this->eavUtils->getDecimalAttributeValue($product, 'price_usd');
		$this->logger->info("[getPriceUsd] USD Price: $usdPrice. Introspect: " . gettype($usdPrice));

		if ($usdPriceIsBase && $usdPrice !== null) {
			$this->logger->info("[getPriceUsd] Returning USD price");
			return $usdPrice;
		}

		$this->logger->info("[getPriceUsd] Price not in USD");
		return null;
	}

	function getSpecialPrice($product) {
		$this->logger->info("[getSpecialPrice] Enter");
		$specialPrice = $this->doGetSpecialPrice($product);
		$specialPriceUsd = $this->getSpecialPriceUsd($product);
		if ($specialPrice === null && $specialPriceUsd === null) {
			$this->logger->info("[getSpecialPrice] Neither special price nor special price usd set.");
			return null;
		}

		if ($specialPriceUsd !== null) {
			$this->logger->info("[getSpecialPrice] Special Price USD is set. Convert to CAD");

			$baseSpecialPrice = $this->currencyUtils->convert('USD', 'CAD', $specialPriceUsd);
			$this->logger->info("[getSpecialPrice] Price in CAD: $baseSpecialPrice");

			return $baseSpecialPrice;
		}

		if ($specialPrice === null) {
			$this->logger->info("[getSpecialPrice] Special price is not set.");

			return null;
		}

		$this->logger->info("[getSpecialPrice] Special price is set. Return as is.");
		return $specialPrice;
	}

	function getSpecialPriceUsd($product) {
		$vendorCurrency = $this->eavUtils->getDropdownAttributeValue($product, 'vendor_currency');
		$this->logger->info("[getSpecialPriceUsd] Vendor currency is: $vendorCurrency. Introspect: " . gettype($vendorCurrency));
		if ($vendorCurrency !== 'USD') {
			$this->logger->info("[getSpecialPriceUsd] Vendor currency is not USD");
			return null;
		}

		$usdPriceIsBase = $this->eavUtils->getBooleanAttributeValue($product, 'usd_is_base_price');
		$this->logger->info("[getSpecialPriceUsd] USD Price Is Base: $usdPriceIsBase. Introspect: " . gettype($usdPriceIsBase));

		$specialPriceUsd = $this->eavUtils->getDecimalAttributeValue($product, 'special_price_usd');
		$this->logger->info("[getSpecialPriceUsd] Special Price USD: $specialPriceUsd. Introspect: " . gettype($specialPriceUsd));

		if (!$usdPriceIsBase || $specialPriceUsd === null) {
			$this->logger->info("[getSpecialPriceUsd] Price not in USD");
			return null;
		}

		if (!$product->getPriceInfo()->getPrice('special_price')->isScopeDateInInterval()) {
			$this->logger->info("[getSpecialPriceUsd] Special price USD is inactive for date");
			return null;
		}

		$this->logger->info("[getSpecialPriceUsd] Returning special price USD");
		return $specialPriceUsd;
	}

	function getCost($product) {
		// As is without currency conversion.
		if ($product->getTypeId() === 'bundle') {
			$this->logger->info("[ProductUtils::getCost] Calculating bundle product cost");

			$cost = 0;

			$typeInstance = $product->getTypeInstance();
			$optionsCollection = $typeInstance->getOptionsCollection($product);

			foreach ($optionsCollection as $option) {
				$optionId = $option->getId();
				$optionTitle = $option->getTitle();
				$required = $option->getRequired();
				$this->logger->info("[ProductUtils::getCost] Bundle option $optionId title $optionTitle required $required");
				if (!$required) {
					$this->logger->info("[ProductUtils::getCost] Option is not required. Skipping...");
					continue;
				}

				$selectionsCollection = $typeInstance->getSelectionsCollection(array($optionId), $product);
				if ($selectionsCollection) {
					$this->logger->info("[ProductUtils::getCost] Got selections");
					$this->logger->info("[ProductUtils::getCost] Got selections count " . count($selectionsCollection));
					foreach ($selectionsCollection as $selection) {
						$selectionId = $selection->getId();
						$selectionName = $selection->getName();
						$sku = $selection->getSku();
						$qty = $selection->getSelectionQty();
						$price = $selection->getPrice();

						$default = $selection->getIsDefault();

						/**
						 $product->getPriceModel()->getSelectionFinalTotalPrice($product, $selection, 0, $qty);
						*/
						try {
							$selectionProduct = $this->productRepository->get($sku);
							$selectionCost = $this->eavUtils->getDecimalAttributeValue($selectionProduct, 'cost');
							$selectionCost = $this->converterUtils->toFloat($selectionCost);

						} catch (Exception $e) {
							$this->logger->info("[ProductUtils::getCost] Failed to get product $sku");
						}
						$this->logger->info("[ProductUtils::getCost] Option $optionId ($optionTitle) selection $selectionId name $selectionName sku $sku qty $qty price $price cost $selectionCost");

						if (!$default) {
							if (count($selectionsCollection) == 1) {
								$this->logger->info("[ProductUtils::getCost] Selection is not default. Count as default because it's single selection");
							} else {
								$this->logger->info("[ProductUtils::getCost] Selection is not default. Skipping...");
								continue;
							}
						}

						$cost += $selectionCost * $qty;
					}
				} else {
					$this->logger->info("[ProductUtils::getCost] No selections");
				}
			}

			$this->logger->info("[ProductUtils::getCost] Bundle total cost $cost");
			return $cost;
		} else {
			$cost = $this->eavUtils->getDecimalAttributeValue($product, 'cost');
			$cost = $this->converterUtils->toFloat($cost);
		}

		$this->logger->info("[ProductUtils::getCost] Simple product cost is $cost");
		return $cost;
	}

	private function doGetSpecialPrice($product) {
		// Same for simple / bundle products.
		// Gets special price absolute value - just number from field.
		$specialPrice = $product->getPriceInfo()->getPrice('special_price')->getSpecialPrice();
		if ($specialPrice === null || $specialPrice === false) {
			$this->logger->info("[doGetSpecialPrice] Special price is unavailable");
			return null;
		}

		$this->logger->info("[doGetSpecialPrice] Product special price is $specialPrice. Introspect: " . gettype($specialPrice));

		if (!$product->getPriceInfo()->getPrice('special_price')->isScopeDateInInterval()) {
			$this->logger->info("[doGetSpecialPrice] Special price is inactive for date");
			return null;
		}

		$this->logger->info("[doGetSpecialPrice] Special price is active");
		return $specialPrice;	 
	}

	private function doGetPrice($product)
	{
		if ($product->getTypeId() === 'bundle') {
			$this->logger->info("Product is bundle");

			// Use minimal price to have correct profit/margin calculation.
			return $product->getPriceInfo()->getPrice('regular_price')->getMinimalPrice()->getValue();
		}

		return $product->getPrice();
	}
}
