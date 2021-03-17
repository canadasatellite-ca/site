<?php

namespace CanadaSatellite\DynamicsIntegration\Model;

class ProductFactory
{
	public function fromEnvelope($envelope)
	{
		if ($envelope === null) {
			return null;
		}
		
		return new Product(
			$envelope->id,
			$envelope->name,
			$envelope->sku,
			$envelope->upc,
			$envelope->price,
			$envelope->priceUsd,
			$envelope->cost,
			$envelope->weight,

			$envelope->qty,
			$envelope->url,
			$envelope->description,
			$envelope->quoteDescription,
			$envelope->network,
			$envelope->category,
			$envelope->service,

			$envelope->brand,
			$envelope->partNo,
			$envelope->countryOfOrigin,
			$envelope->priceListPage,
			$envelope->warranty,
			$envelope->vendor,
			$envelope->vendorCurrency,
			$envelope->vendorDescription,
			$envelope->vendorPart,

			$envelope->metaTitle,
			$envelope->metaDescription,
			$envelope->metaKeyword,

			$envelope->shippingLength,
			$envelope->shippingWidth,
			$envelope->shippingHeight,

			property_exists($envelope, 'specialPrice') ? $envelope->specialPrice : null,
			property_exists($envelope, 'specialPriceUsd') ? $envelope->specialPriceUsd : null
		);
	}
}
