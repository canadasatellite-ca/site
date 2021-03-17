<?php

namespace CanadaSatellite\DynamicsIntegration\Envelope;

class ProductEnvelopeFactory
{
	private $eavUtils;
	private $productUtils;
	private $converterUtils;

	public function __construct(
		\CanadaSatellite\DynamicsIntegration\Utils\EavUtils $eavUtils,
		\CanadaSatellite\DynamicsIntegration\Utils\ProductUtils $productUtils,
		\CanadaSatellite\DynamicsIntegration\Utils\ConverterUtils $converterUtils,
		\CanadaSatellite\DynamicsIntegration\Logger\Logger $logger
	) {
		$this->eavUtils = $eavUtils;
		$this->productUtils = $productUtils;
		$this->converterUtils = $converterUtils;
		$this->logger = $logger;
	}

	/**
	 * @param \Magento\Catalog\Api\Data\ProductInterface $product
	 */
	public function create($product)
	{
		$data = array();

		$data['id'] = $product->getId();
		$data['name'] = $product->getName();
		$data['sku'] = $product->getSku();
		$data['upc'] = $this->eavUtils->getTextAttributeValue($product, 'upc');

		$typeId = $product->getTypeId();
		$this->logger->info("Product type: $typeId");

		// Price in CAD
		$price = $this->productUtils->getPrice($product);
		$this->logger->info("Price: $price. Introspect: " . gettype($price));
		$price = $this->converterUtils->toFloat($price);
		$this->logger->info("Price after converter: $price. Introspect: " . gettype($price));
		$data['price'] = $price;

		// Price in USD
		$priceUsd = $this->productUtils->getPriceUsd($product);
		$this->logger->info("Price in USD: $priceUsd. Introspect: " . gettype($priceUsd));
		$priceUsd = $this->converterUtils->toFloat($priceUsd);
		$this->logger->info("Price in USD after converter: $priceUsd. Introspect: " . gettype($priceUsd));
		$data['priceUsd'] = $priceUsd;

		// Special price in CAD
		$specialPrice = $this->productUtils->getSpecialPrice($product);
		$this->logger->info("Special price: $specialPrice. Introspect: " . gettype($specialPrice));
		$specialPrice = $this->converterUtils->toFloat($specialPrice);
		$this->logger->info("Special price after converter: $specialPrice. Introspect: " . gettype($specialPrice));
		$data['specialPrice'] = $specialPrice;

		// Special price USD
		$specialPriceUsd = $this->productUtils->getSpecialPriceUsd($product);
		$this->logger->info("Special price USD: $specialPriceUsd. Introspect: " . gettype($specialPriceUsd));
		$specialPriceUsd = $this->converterUtils->toFloat($specialPriceUsd);
		$this->logger->info("Special price USD after converter: $specialPriceUsd. Introspect: " . gettype($specialPriceUsd));
		$data['specialPriceUsd'] = $specialPriceUsd;
	
		/*$cost = $this->eavUtils->getDecimalAttributeValue($product, 'cost');
		$this->logger->info("Cost: $cost. Introspect: " . gettype($cost));
		$cost = $this->converterUtils->toFloat($cost);
		$this->logger->info("Cost after converter: $cost. Introspect: " . gettype($cost));*/
		$cost = $this->productUtils->getCost($product);
		$data['cost'] = $cost;
		
		$weight = $product->getWeight();
		$this->logger->info("Weight: $weight. Introspect: " . gettype($weight));
		$weight = $this->converterUtils->toFloat($weight);
		$this->logger->info("Weight after converter: $weight. Introspect: " . gettype($weight));
		$data['weight'] = $weight;

		$qty = $this->productUtils->getQty($product);
		$this->logger->info("Qty: $qty. Introspect: " . gettype($qty));
		$qty = $this->converterUtils->toFloat($qty);
		$this->logger->info("Qty after converter: $qty. Introspect: " . gettype($qty));
		$data['qty'] = $qty;
		
		$data['url'] = $this->productUtils->getUrl($product);
		$data['description'] = $this->eavUtils->getTextAttributeValue($product, 'description');
		$data['quoteDescription'] = $this->eavUtils->getTextAttributeValue($product, 'quote_description');

		$data['network'] = $this->eavUtils->getMultiselectAttributeValue($product, 'network');
		$data['category'] = $this->eavUtils->getMultiselectAttributeValue($product, 'product_category');
		$data['service'] = $this->eavUtils->getMultiselectAttributeValue($product, 'service');

		$data['brand'] = $this->eavUtils->getDropdownAttributeValue($product, 'brand');
		$data['partNo'] = $this->eavUtils->getTextAttributeValue($product, 'part');

		$data['countryOfOrigin'] = $this->eavUtils->getDropdownAttributeValue($product, 'country_of_manufacture');
		$data['priceListPage'] = $this->eavUtils->getDropdownAttributeValue($product, 'price_list_page');
		$data['warranty'] = $this->eavUtils->getDropdownAttributeValue($product, 'warranty');


		$data['vendor'] = $this->eavUtils->getDropdownAttributeValue($product, 'vendor');
		$data['vendorCurrency'] = $this->eavUtils->getDropdownAttributeValue($product, 'vendor_currency');
		$data['vendorDescription'] = $this->eavUtils->getTextAttributeValue($product, 'vendor_description');
		$data['vendorPart'] = $this->eavUtils->getTextAttributeValue($product, 'attr_vendor_part');

		$data['metaTitle'] = $this->eavUtils->getTextAttributeValue($product, 'meta_title');
		$data['metaDescription'] = $this->eavUtils->getTextAttributeValue($product, 'meta_description');
		$data['metaKeyword'] = $this->eavUtils->getTextAttributeValue($product, 'meta_keyword');

		$data['shippingLength'] = $this->eavUtils->getTextAttributeValue($product, 'shipping_length');
		$data['shippingWidth'] = $this->eavUtils->getTextAttributeValue($product, 'shipping_width');
		$data['shippingHeight'] = $this->eavUtils->getTextAttributeValue($product, 'shipping_height');

		return $data;
	}
}
