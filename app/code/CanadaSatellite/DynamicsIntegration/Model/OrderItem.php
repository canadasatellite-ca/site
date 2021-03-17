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

	public function __construct(
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

	public function getSku()
	{
		return $this->sku;
	}

	public function getProduct()
	{
		return $this->product;
	}

	public function getPrice()
	{
		return $this->price;
	}

	public function getQty()
	{
		return $this->qty;
	}

	public function getTax()
	{
		return $this->tax;
	}

	public function getDiscount()
	{
		return $this->discount;
	}

	public function getTotal()
	{
		return $this->total;
	}

	public function getOptionsPrice()
	{
		return $this->optionsPrice;
	}

	public function getOptionsCost()
	{
		return $this->optionsCost;
	}

	public function getCost()
	{
		// In base currency, CAD.
		return $this->cost;
	}
}
