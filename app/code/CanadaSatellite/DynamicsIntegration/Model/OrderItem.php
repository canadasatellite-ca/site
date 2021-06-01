<?php

namespace CanadaSatellite\DynamicsIntegration\Model;

class OrderItem
{
	private $sku;
	private $product;
	private $price;
	private $qty;
	private $tax;
	private $discount;
	private $total;

	private $optionsPrice;
	private $optionsCost;

	private $cost;

	function __construct(
		$sku,
		$product,
		$price,
		$qty,
		$tax,
		$discount,
		$total,

		$optionsPrice,
		$optionsCost,

		$cost
	) {
		$this->sku = $sku;
		$this->product = $product;
		$this->price = $price;
		$this->qty = $qty;
		$this->tax = $tax;
		$this->discount = $discount;
		$this->total = $total;

		$this->optionsPrice = $optionsPrice;
		$this->optionsCost = $optionsCost;

		$this->cost = $cost;
	}

	function getSku()
	{
		return $this->sku;
	}

	function getProduct()
	{
		return $this->product;
	}

	function getPrice()
	{
		return $this->price;
	}

	function getQty()
	{
		return $this->qty;
	}

	function getTax()
	{
		return $this->tax;
	}

	function getDiscount()
	{
		return $this->discount;
	}

	function getTotal()
	{
		return $this->total;
	}

	function getOptionsPrice()
	{
		return $this->optionsPrice;
	}

	function getOptionsCost()
	{
		return $this->optionsCost;
	}

	function getCost()
	{
		// In base currency, CAD.
		return $this->cost;
	}
}
