<?php

namespace CanadaSatellite\DynamicsIntegration\DynamicsCrm;

use CanadaSatellite\DynamicsIntegration\Exception\DynamicsException;
use CanadaSatellite\DynamicsIntegration\Utils\ProductProfitCalculator;

class ProductModelComposer {
	private $mapper;
	private $restApi;
	private $currencyHelper;
	private $priceListHelper;
	private $vendorHelper;
	private $countryHelper;
	private $logger;

	public function __construct(
		\CanadaSatellite\DynamicsIntegration\DynamicsCrm\DynamicsMapper $mapper,
		\CanadaSatellite\DynamicsIntegration\Rest\RestApi $restApi,
		\CanadaSatellite\DynamicsIntegration\DynamicsCrm\CurrencyHelper $currencyHelper,
		\CanadaSatellite\DynamicsIntegration\DynamicsCrm\PriceListHelper $priceListHelper,
		\CanadaSatellite\DynamicsIntegration\DynamicsCrm\VendorHelper $vendorHelper,
		\CanadaSatellite\DynamicsIntegration\DynamicsCrm\CountryHelper $countryHelper,
		\CanadaSatellite\DynamicsIntegration\Logger\Logger $logger
	) {
		$this->mapper = $mapper;
		$this->restApi = $restApi;
		$this->currencyHelper = $currencyHelper;
		$this->priceListHelper = $priceListHelper;
		$this->vendorHelper = $vendorHelper;
		$this->countryHelper = $countryHelper;
		$this->logger = $logger;
	}

	/**
	 * @param Product model
	 */
	public function compose($product) {
		$vendorCurrency = $product->getVendorCurrency();
		$this->logger->info("Product currency: $vendorCurrency");

		if ($vendorCurrency === 'USD' && $product->hasPriceUsd()) {
			// Price in USD.
			$price = $product->getPriceUsd();
		} else {
			// Price in CAD.
			$vendorCurrency = 'CAD';
			$price = $product->getPrice();
		}

		if ($vendorCurrency === 'USD' && $product->hasSpecialPriceUsd()) {
			// Special price USD.
			$specialPrice = $product->getSpecialPriceUsd();
		} else {
			// Special price in CAD.
			$specialPrice = $product->getSpecialPrice();
			// If product has price USD, then it should have special price in USD, too.
			//if ($product->hasPriceUsd()) {
			//	$specialPrice = null;
			//}
		}

		$currencyId = $this->currencyHelper->getCurrencyIdByCode($vendorCurrency);
		$this->logger->info("Currency id: $currencyId");

		$priceLevelId = $this->priceListHelper->getDefaultPriceListId();

		$this->logger->info("Trying to get product quantity.");
		$qty = $product->getQty();
		$this->logger->info("Product quantity: $qty");

		$this->logger->info("Trying to get product url.");
		$url = $product->getUrl();
		$this->logger->info("Product URL: $url");

		$quoteDescription = $product->getQuoteDescription();
		$weight = $product->getWeight();

		$productDescription = $product->getDescription();

		$network = $product->getNetwork();
		$this->logger->info("Product network: $network");

		$category = $product->getCategory();
		$this->logger->info("Product category: $category");

		$service = $product->getService();
		$this->logger->info("Product service: $service");

		$brand = $product->getBrand();
		$this->logger->info("Product brand: $brand");

		$partNo = $product->getPartNo();
		$this->logger->info("Product part#: $partNo");

		$countryOfOrigin = $product->getCountryOfOrigin();
		$this->logger->info("Product country of origin: $countryOfOrigin");

		$priceListPage = $product->getPriceListPage();
		$this->logger->info("Product price list page: $priceListPage");

		$vendor = $product->getVendor();
		$this->logger->info("Product vendor: $vendor");

		$warranty = $product->getWarranty();
		$this->logger->info("Product warranty: $warranty");

		$upc = $product->getUpc();
		$this->logger->info("Product UPC: $upc");

		$cost = $product->getCost();
		$this->logger->info("Product cost: $cost");

		$this->logger->info("Product price: $price");
		$this->logger->info("Product special price: $specialPrice");

		$data = array(
			'name' => $product->getName(),
			'productnumber' => $product->getSku(),
			'price' => $price,
			// price_base is calculated from price, so does not set it.
			'transactioncurrencyid@odata.bind' => "/transactioncurrencies($currencyId)",

			// Defaults for Unit Group and Default Unit.
			'defaultuomscheduleid@odata.bind' => '/uomschedules(a974056b-8238-4cbd-93fd-bacda14d313d)',
			'defaultuomid@odata.bind' => '/uoms(16b94c87-47ff-4583-884b-fba2cea56106)',

			'pricelevelid@odata.bind' => "/pricelevels($priceLevelId)",
			
			// cs_volusionid will not be synced.
			// cs_issynced will not be synced.

			'producturl' => $product->getUrl(),
		);

		if ($qty !== null) {
			$data['quantityonhand'] = $qty;
		}

		
		if ($quoteDescription !== null) {
			$data['new_quotedescription'] = $quoteDescription;
		}
		if ($weight !== null) {
			$data['stockweight'] = $weight;
		}
		if ($productDescription !== null) {
			$data['new_description'] = $productDescription;
		}

		if ($network !== null) {
			$data['new_network'] = $this->mapper->mapNetwork($network);
		}
		if ($category !== null) {
			$data['new_productcategory'] = $this->mapper->mapProductCategory($category);
		}
		if ($service !== null) {
			$data['new_service'] = $this->mapper->mapServiceType($service);
		}

		if ($brand !== null) {
			$vendorId = $this->vendorHelper->getOrCreateVendorAccount($brand);
			$data['new_manufacturerlookup@odata.bind'] = "/accounts($vendorId)";
		}
		if ($partNo !== null) {
			$data['new_manufacturerpart'] = $partNo;
		}

		if ($countryOfOrigin !== null) {
			$this->logger->info("Finding country");
			$countryId = $this->countryHelper->findCountryByName($countryOfOrigin);
			$this->logger->info("Country found: $countryId");
			if ($countryId !== null) {
				$data['new_countryoforiginlookup@odata.bind'] = "/new_countries($countryId)";
			}
		}

		if ($priceListPage !== null) {
			$page = intval($priceListPage);
			if ($page !== 0) {
				$data['new_pricelistpage'] = $page;
			}
		}

		if ($vendor !== null) {
			$vendorId = $this->vendorHelper->getOrCreateVendorAccount($vendor);
			$data['cs_vendorid@odata.bind'] = "/accounts($vendorId)"; 
		}

		if ($warranty !== null) {
			$data['new_warranty'] = $this->mapper->mapWarranty($warranty);
		}

		if ($upc !== null) {
			$data['new_upc'] = $upc;
		}

		if ($cost !== null) {
			$data['new_usdcost'] = $cost;
		}

		$data['new_saleprice'] = $specialPrice;

		$data = array_merge_recursive($data, $this->buildVendorData($product));
		$data = array_merge_recursive($data, $this->buildMetadata($product));
		$data = array_merge_recursive($data, $this->buildShippingData($product));

		// TODO: Calculate profit/margin.
		$this->logger->info("Start calculating profit/margin for product");
		$calculator = new ProductProfitCalculator($this->logger, $product);

		$currencyExchange = $calculator->calculateCurrencyExchange();
		$this->logger->info("Currency exchange for product: $currencyExchange");

		$processingFees = $calculator->calculateProcessingFees();
		$this->logger->info("Processing fees for product: $processingFees");
		
		$standardCost = $calculator->calculateStandardCost();
		$this->logger->info("Standard cost for product: $standardCost");

		$profit = $calculator->calculateProfit();
		$this->logger->info("Profit for product: $profit");
		$margin = $calculator->calculateMargin();
		$this->logger->info("Margin for product: $margin");

		if ($currencyExchange !== null) {
			$data['new_currencyexchange'] = $currencyExchange;
		}
		if ($processingFees !== null) {
			$data['new_processingfees'] = $processingFees;
		}
		if ($standardCost !== null) {
			$data['standardcost'] = $standardCost;
		}
		if ($profit !== null) {
			$data['new_profit'] = $profit;
		}
		if ($margin !== null) {
			$data['new_margin'] = $margin;
		}	

		return $data;
	}

	public function composePriceListItem($product, $productId) {
		$this->logger->info("[composePriceListItem] -> Enter");

		$vendorCurrency = $product->getVendorCurrency();
		$this->logger->info("[composePriceListItem] Product currency: $vendorCurrency");

		// Always use CAD currency and default price list.
		$currencyId = $this->currencyHelper->getCurrencyIdByCode('CAD');
		$priceLevelId = $this->priceListHelper->getDefaultPriceListId();
		$price = $product->getPrice();
		$this->logger->info("[composePriceListItem] Set product price $price in CAD for default price list.");

		return array(
			'productid@odata.bind' => "/products($productId)",
			'amount' => $price,
			'transactioncurrencyid@odata.bind' => "/transactioncurrencies($currencyId)",
			"pricelevelid@odata.bind" => "/pricelevels($priceLevelId)",
			// Defaults for Unit Group and Default Unit.
			"uomid@odata.bind" => "/uoms(16b94c87-47ff-4583-884b-fba2cea56106)",
			"uomscheduleid@odata.bind" => "/uomschedules(a974056b-8238-4cbd-93fd-bacda14d313d)"
		);
	}

 	private function buildVendorData($product) {
 		$data = array();

 		$vendorDescription = $product->getVendorDescription();
 		$vendorName = $product->getVendorPart();

 		if ($vendorDescription !== null) {
			$data['description'] = $vendorDescription;
		}
		if ($vendorName !== null) {
			$data['vendorpartnumber'] = $vendorName;
		}

 		return $data;
 	}

 	private function buildMetadata($product) {
 		$data = array();

 		$metaTitle = $product->getMetaTitle();
 		$metaDescription = $product->getMetaDescription();
 		$metaKeywords = $product->getMetaKeyword();

 		if ($metaTitle !== null) {
 			$data['new_metatagtitle'] = $metaTitle;
 		}

 		if ($metaDescription !== null) {
 			$data['new_metatagdescription'] = $metaDescription;
 		}

 		if ($metaKeywords !== null) {
 			$data['new_metatagkeywords10k'] = $metaKeywords;
 		}

 		return $data;
 	}

 	private function buildShippingData($product) {
 		$data = array();

 		$length = $product->getShippingLength();
 		$width = $product->getShippingWidth();
 		$height = $product->getShippingHeight();

 		if ($length !== null) {
 			$data['new_lengthcm'] = $length;
 		}
 		if ($width !== null) {
 			$data['new_widthcm'] = $width;
 		}
 		if ($height !== null) {
 			$data['new_heightcm'] = $height;
 		}

 		return $data;
 	}
}