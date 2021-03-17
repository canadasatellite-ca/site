<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Quote;
use Magento\Sales\Model\AbstractModel;
use Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface;
/**
 * Trait TierItem
 *
 * @package Cart2Quote\Quotation\Model\Quote
 */
trait TierItem
{
    /**
     * Get item id
     *
     * @return int $itemId
     */
    private function getItemId()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::ITEM_ID);
		}
	}
    /**
     * Set entity id
     *
     * @param int $entityId
     * @return $this
     */
    private function setEntityId($entityId)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->setData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::ENTITY_ID, $entityId);
        return $this;
		}
	}
    /**
     * Set entity id
     *
     * @return int
     */
    private function getEntityId()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::ENTITY_ID);
		}
	}
    /**
     * Set base original price
     *
     * @return float
     */
    private function getBaseOriginalPrice()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::BASE_ORIGINAL_PRICE);
		}
	}
    /**
     * Get Base Custom Price
     *
     * @return float
     */
    private function getBaseCustomPrice()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$baseCustomPrice = $this->getData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::BASE_CUSTOM_PRICE);
        if (empty($baseCustomPrice) || $baseCustomPrice == 0) { //check against 0 as the value might be 0.0000
            $quote = $this->getQuote();
            if ($quote->getId()) {
                //only overwrite the base price if there is a quote available
                $baseCustomPrice = $this->getCustomPrice();
            }
            if ($quote->isCurrencyDifferent()) {
                try {
                    $baseCustomPrice = $this->getQuote()->convertPriceToQuoteBaseCurrency($baseCustomPrice);
                    $this->setBaseCustomPrice($baseCustomPrice);
                } catch (\Exception $e) {
                    $logMessage = sprintf("No conversion rate set: %s", $e);
                    $this->_logger->notice($logMessage);
                }
            }
        }
        return $baseCustomPrice;
		}
	}
    /**
     * Set base cost price
     *
     * @param float $baseCost
     * @return $this
     */
    private function setBaseCost($baseCost)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->setData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::BASE_COST, $baseCost);
        return $this;
		}
	}
    /**
     * Get cost price
     *
     * @return float
     */
    private function getRowTotal()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::ROW_TOTAL);
		}
	}
    /**
     * Set cost price
     *
     * @param float $costPrice
     * @return $this
     */
    private function setRowTotal($costPrice)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->setData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::ROW_TOTAL, $costPrice);
        return $this;
		}
	}
    /**
     * Get base cost price
     *
     * @return float
     */
    private function getBaseRowTotal()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::BASE_ROW_TOTAL);
		}
	}
    /**
     * Set base cost price
     *
     * @param float $baseCostPrice
     * @return $this
     */
    private function setBaseRowTotal($baseCostPrice)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->setData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::BASE_ROW_TOTAL, $baseCostPrice);
        return $this;
		}
	}
    /**
     * Get discount amount
     *
     * @return float
     */
    private function getDiscountAmount()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::DISCOUNT_AMOUNT);
		}
	}
    /**
     * Set discount amount
     *
     * @param float $discountAmount
     * @return $this
     */
    private function setDiscountAmount($discountAmount)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->setData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::DISCOUNT_AMOUNT, $discountAmount);
        return $this;
		}
	}
    /**
     * Get base discount amount
     *
     * @return float
     */
    private function getBaseDiscountAmount()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::BASE_DISCOUNT_AMOUNT);
		}
	}
    /**
     * Set base discount amount
     *
     * @param float $baseDiscountAmount
     * @return $this
     */
    private function setBaseDiscountAmount($baseDiscountAmount)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->setData(
            \Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::BASE_DISCOUNT_AMOUNT,
            $baseDiscountAmount
        );
        return $this;
		}
	}
    /**
     * Make optional
     *
     * @return boolean
     */
    private function getMakeOptional()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::MAKE_OPTIONAL);
		}
	}
    /**
     * Set make optional
     *
     * @param boolean $makeOptional
     * @return $this
     */
    private function setMakeOptional($makeOptional)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->setData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::MAKE_OPTIONAL, $makeOptional);
        return $this;
		}
	}
    /**
     * @param float $originalTaxAmount
     * @return $this|\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface
     */
    private function setOriginalTaxAmount($originalTaxAmount)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->setData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::ORIGINAL_TAX_AMOUNT, $originalTaxAmount);
        return $this;
		}
	}
    /**
     * @return float
     */
    private function getOriginalTaxAmount()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::ORIGINAL_TAX_AMOUNT);
		}
	}
    /**
     * @param float $originalBaseTaxAmount
     * @return $this|\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface
     */
    private function setOriginalBaseTaxAmount($originalBaseTaxAmount)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->setData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::ORIGINAL_BASE_TAX_AMOUNT, $originalBaseTaxAmount);
        return $this;
		}
	}
    /**
     * @return float
     */
    private function getOriginalBaseTaxAmount()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::ORIGINAL_BASE_TAX_AMOUNT);
		}
	}
    /**
     * Set selected
     *
     * @return \Magento\Quote\Model\Quote\Item $item
     */
    private function setSelected()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$quoteItem = $this->getItem();
        $quoteItem = $this->resetQuoteItem($quoteItem);
        $quoteItem->setCurrentTierItem($this);
        $quoteItem->setQty($this->getQty());
        $quoteItem->setCustomPrice($this->getCustomPrice());
        $quoteItem->setBaseCustomPrice($this->getBaseCustomPrice());
        $quoteItem->setCalculationPrice($this->getCustomPrice());
        $quoteItem->setBaseCalculationPrice($this->getBaseCustomPrice());
        $quoteItem->setOriginalPrice($this->getOriginalPrice());
        $quoteItem->setBaseOriginalPrice($this->getBaseOriginalPrice());
        $quoteItem->setBaseCost($this->getBaseCost());
        if ($quoteItem->getProductType() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
            $quoteItem->setNoDiscount(1);
            $this->setSelectedChild($quoteItem);
        }
        return $quoteItem;
		}
	}
    /**
     * Get item
     *
     * @return \Magento\Quote\Model\Quote\Item
     */
    private function getItem()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$item = $this->getData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::QUOTE_ITEM);
        if (!$item && $this->getItemId()) {
            $item = $this->quoteItemFactory->create()->load($this->getItemId());
            $this->setData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::QUOTE_ITEM, $item);
        }
        return $item;
		}
	}
    /**
     * Reset the quote item prices
     *
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @return \Magento\Quote\Model\Quote\Item
     */
    private function resetQuoteItem($quoteItem)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $quoteItem
            ->setPrice(0)
            ->setBasePrice(0)
            ->setCustomPrice(0)
            ->setBaseCustomPrice(0)
            ->setOriginalCustomPrice(0)
            ->setBaseCalculationPrice(0)
            ->setCalculationPrice(0)
            ->setDiscountAmount(0)
            ->setBaseDiscountAmount(0)
            ->setDiscountPercent(0)
            ->setBaseRowTotal(0)
            ->setBaseRowTotalInclTax(0)
            ->setBaseRowTotalWithDiscount(0)
            ->setRowTotal(0)
            ->setRowTotalInclTax(0)
            ->setRowTotalWithDiscount(0)
            ->setBaseCost(0)
            ->setPriceInclTax(0)
            ->setBasePriceInclTax(0)
            ->setCost(0)
            ->setRowTotalWithDiscount(0)
            ->setTaxAmount(0)
            ->setBaseTaxAmount(0)
            ->setTaxPercent(0)
            ->setBaseTaxCalculationPrice(0)
            ->setTaxCalculationPrice(0);
		}
	}
    /**
     * Get qty
     *
     * @return float $qty
     */
    private function getQty()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::QTY);
		}
	}
    /**
     * Get original price
     *
     * @return float
     */
    private function getOriginalPrice()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::ORIGINAL_PRICE);
		}
	}
    /**
     * Get base cost price
     *
     * @return float
     */
    private function getBaseCost()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::BASE_COST);
		}
	}
    /**
     * Set selected for child
     * - The child tier prices are calculated based on the parent tier item
     *
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @return $this
     */
    private function setSelectedChild($quoteItem)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$totalChildPrice = $this->calculateChildTotalPrice($quoteItem->getChildren());
        $totalCalculatedChildPrice = 0;
        /** @var \Magento\Quote\Model\Quote\Item $child */
        foreach ($quoteItem->getChildren() as &$child) {
            $childPrice = $this->calculateChildPrice($child, $totalChildPrice);
            $totalCalculatedChildPrice += ((float)$childPrice * (float)$child->getQty());
            /** @var TierItem $tier */
            if ($tier = $child->getCurrentTierItem()) {
                $tier->setItem($child);
                $tier->setCustomPrice($childPrice);
                $child = $tier->setSelected(); // recursive
                $child->setNoDiscount(1);
            }
        }
        $this->checkBundleRoundingIssue($totalCalculatedChildPrice);
        return $this;
		}
	}
    /**
     * Get total child price
     *
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem[] $children
     * @return int
     */
    private function calculateChildTotalPrice($children)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$totalPrice = 0;
        foreach ($children as $child) {
            $dataPriceObject = $child->getCurrentTierItem();
            if ($dataPriceObject == null) {
                $dataPriceObject = $child;
            }
            $rowTotal = $dataPriceObject->getRowTotal();
            $customPrice = $dataPriceObject->getCustomPrice();
            if (isset($rowTotal) && $rowTotal > 0) {
                $totalPrice += (float)$rowTotal;
            } elseif ($customPrice && $customPrice > 0) {
                $totalPrice += ((float)$customPrice * (float)$dataPriceObject->getQty());
            } else {
                $totalPrice += ((float)$dataPriceObject->getPrice() * (float)$dataPriceObject->getQty());
            }
        }
        return $totalPrice;
		}
	}
    /**
     * Calculate the child price
     *
     * @param \Magento\Quote\Model\Quote\Item $child
     * @param float $totalChildPrice
     * @return float
     */
    private function calculateChildPrice(\Magento\Quote\Model\Quote\Item $child, $totalChildPrice)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$dataPriceObject = $child->getCurrentTierItem();
        if ($dataPriceObject == null) {
            $dataPriceObject = $child;
        }
        $rowTotal = $dataPriceObject->getRowTotal();
        if ($rowTotal <= 0) {
            $customPrice = (double)$dataPriceObject->getCustomPrice();
            return (double)($customPrice / $dataPriceObject->getQty());
        }
        $percentage = (double)$this->calculatePercentage(
            $totalChildPrice,
            $dataPriceObject->getRowTotal()
        );
        $percentage = round($percentage, 4, PHP_ROUND_HALF_DOWN);
        $customPrice = (double)$this->calculatePrice($this->getCustomPrice(), $percentage);
        $childPrice = (double)($customPrice / $dataPriceObject->getQty());
        $childPrice = round($childPrice, 4, PHP_ROUND_HALF_DOWN);
        return $childPrice;
		}
	}
    /**
     * Calculate percentage
     *
     * @param float $total
     * @param float $subject
     * @return float
     */
    private function calculatePercentage($total, $subject)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($total <= 0) {
            return $total;
        }
        return $subject / (0.01 * $total);
		}
	}
    /**
     * Calculate price
     *
     * @param float $total
     * @param float $percentage
     * @return float
     */
    private function calculatePrice($total, $percentage)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return ($total * 0.01) * $percentage;
		}
	}
    /**
     * Set item
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return $this
     */
    private function setItem(\Magento\Quote\Model\Quote\Item $item)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->setItemId($item->getId());
        $this->loadPriceOnItem($item);
        $this->setData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::QUOTE_ITEM, $item);
        return $this;
		}
	}
    /**
     * Set item id
     *
     * @param int $itemId
     * @return $this
     */
    private function setItemId($itemId)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->setData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::ITEM_ID, $itemId);
        return $this;
		}
	}
    /**
     * Load the tier price on the quote item
     *
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @return $this
     */
    private function loadPriceOnItem(&$quoteItem)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$quoteItem->setCalculationPrice($this->getCustomPrice());
        $quoteItem->setCustomPrice($this->getCustomPrice());
        $quoteItem->setBaseCustomPrice($this->getBaseCustomPrice());
        $quoteItem->setOriginalCustomPrice($this->getCustomPrice());
        return $this;
		}
	}
    /**
     * Get custom price
     *
     * @return float
     */
    private function getCustomPrice()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::CUSTOM_PRICE);
		}
	}
    /**
     * Set custom price
     *
     * @param float $customPrice
     * @return $this
     */
    private function setCustomPrice($customPrice)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->setData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::CUSTOM_PRICE, $customPrice);
        return $this;
		}
	}
    /**
     * Adds the rounding differences to the tier item (won't be saved in the DB)
     * - You can use this to detect rounding issues for bundles
     *
     * @param float $totalCalculatedChildPrice
     */
    private function checkBundleRoundingIssue($totalCalculatedChildPrice)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($this->getCustomPrice() > $totalCalculatedChildPrice
            || $this->getCustomPrice() < $totalCalculatedChildPrice
        ) {
            $this->setRoundingOffset($this->getCustomPrice() - $totalCalculatedChildPrice);
        }
		}
	}
    /**
     * Set the tier item data base the quote item values
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param null|int $tierItemId
     * @param null|int $qty
     * @return $this
     */
    private function setDataByItem(\Magento\Quote\Model\Quote\Item $item, $tierItemId = null, $qty = null)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->setData($item->getData())
            ->setId($tierItemId)
            ->setQty($qty ? $qty : $item->getQty())
            ->setItemId($item->getId());
        if ($tierItemId == null) {
            $this->setNewPrice($item);
        }
        return $this;
		}
	}
    /**
     * Set qty
     *
     * @param float $qty
     * @return $this
     */
    private function setQty($qty)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->setData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::QTY, $qty);
        return $this;
		}
	}
    /**
     * Set prices to tier item
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return $this
     */
    private function setNewPrice(\Magento\Quote\Model\Quote\Item $item)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->setBaseCustomPrice($item->getPrice());
        $this->setBaseOriginalPrice($item->getPrice());
        $this->setCustomPrice($item->getConvertedPrice());
        $this->setOriginalPrice($item->getConvertedPrice());
        $this->setOriginalTaxAmount($item->getTaxAmount());
        $this->setOriginalBaseTaxAmount($item->getBaseTaxAmount());
        if ($item->getProductType() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            $this->setConfigurableBaseCost($item);
        }
        if ($this->quotationTaxHelper->priceIncludesTax($item->getStoreId())) {
            $this->setPriceInclTax($item);
        }
        return $this;
		}
	}
    /**
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return $this
     */
    private function setPriceInclTax(\Magento\Quote\Model\Quote\Item $item)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$product = $item->getProduct();
        $finalPrice = $product->getFinalPrice();
        $this->setBaseCustomPrice($finalPrice);
        $this->setCustomPrice($this->getCurrencyPrice($finalPrice));
        return $this;
		}
	}
    /**
     * Calculate quote currency to the price
     *
     * @param float $price
     * @return float
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getCurrencyPrice($price)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $price * $this->getBaseToQuoteRate();
		}
	}
    /**
     * Get base_to_quote_rate from quote
     *
     * @return float
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getBaseToQuoteRate()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$item = $this->getItem();
        if ($item && $item->getQuote()) {
            $quote = $item->getQuote();
        } else {
            $quote = $this->quoteRepository->get($this->getQuoteId());
        }
        return $quote->getBaseToQuoteRate();
		}
	}
    /**
     * Get original price incl tax
     *
     * @return float
     */
    private function getOriginalPriceInclTax()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->quotationTaxHelper->getOriginalPriceInclTax($this);
		}
	}
    /**
     * Get base original price incl tax
     *
     * @return float
     */
    private function getBaseOriginalPriceInclTax()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->quotationTaxHelper->getBaseOriginalPriceInclTax($this);
		}
	}
    /**
     * Set base original price
     *
     * @param float $baseOriginalPrice
     * @return $this
     */
    private function setBaseOriginalPrice($baseOriginalPrice)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->setData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::BASE_ORIGINAL_PRICE, $baseOriginalPrice);
        return $this;
		}
	}
    /**
     * Set Base Custom Price
     *
     * @param float $baseCustomPrice
     * @return $this
     */
    private function setBaseCustomPrice($baseCustomPrice)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->setData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::BASE_CUSTOM_PRICE, $baseCustomPrice);
        return $this;
		}
	}
    /**
     * Set original price
     *
     * @param float $originalPrice
     * @return $this
     */
    private function setOriginalPrice($originalPrice)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->setData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::ORIGINAL_PRICE, $originalPrice);
        return $this;
		}
	}
    /**
     * Load the tier price on the product
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return $this
     */
    private function loadPriceOnProduct(&$product)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$product->setData('final_price', $this->getCustomPrice());
        return $this;
		}
	}
    /**
     * Is tier selected
     *
     * @return bool
     */
    private function isSelected()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return ($this->getItem()->getQty() * 1) == ($this->getQty() * 1);
		}
	}
    /**
     * Init resource model
     *
     * @return void
     */
    private function _construct()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->_init(\Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem::class);
		}
	}
    /**
     * Get Quotation Quote
     *
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    private function getQuote()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if (!$this->quote instanceof \Cart2Quote\Quotation\Model\Quote) {
            $quoteId = (int)$this->getQuoteId();
            $this->quote = $this->quotationFactory->create();
            $this->quote->load($quoteId);
        }
        return $this->quote;
		}
	}
    /**
     * Function that finds the quote id for this tier item
     *
     * @return int|null
     */
    private function getQuoteId()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$item = $this->getItem();
        if (!$item) {
            return null;
        }
        $quote = $item->getQuote();
        if (!$quote) {
            $quoteId = $item->getQuoteId();
            $quote = $this->quotationFactory->create()->load($quoteId);
        }
        $quotationId = $quote->getId();
        if ($quote->getLinkedQuotationId()) {
            $quotationId = $quote->getLinkedQuotationId();
        }
        return $quotationId;
		}
	}
    /**
     * Process new tier items
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return \Cart2Quote\Quotation\Model\Quote\TierItem
     * @throws \Exception
     */
    private function addNewTierItem($item)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$tierItem = $this->tierItemFactory->createFromItem($item);
        $this->tierItemResourceCollection->setItemTiers($item);
        return $tierItem;
		}
	}
    /**
     * Edit existing tier item
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param array $postData
     * @throws \Exception
     */
    private function editExistingTierItem($item, $postData)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$tierId = $postData['tier_id'];
        $tierItem = $this->tierItemResourceCollection->getTierById($tierId);
        if ($tierItem) {
            $newData = array_merge($tierItem->getData(), $item->getData());
            $tierItem->setData($newData);
            $tierItem->save();
        }
		}
	}
    /**
     * Check if item quantity exist already in tier quantity
     *
     * @param int $itemId
     * @param int $qty
     * @return bool
     */
    private function checkQtyExistTiers($itemId, $qty)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$tierItemCollection = $this->tierItemResourceCollection->getTierItemsByItemId($itemId);
        foreach ($tierItemCollection as $tierItem) {
            if ($qty == $tierItem->getQty()) {
                return true;
            }
        }
        return false;
		}
	}
    /**
     * Delete tier item
     *
     * @param int $tierItemId
     * @throws \Exception
     */
    private function deleteTierItem($tierItemId)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$tierItem = $this->tierItemResourceCollection->getTierById($tierItemId);
        $tierItem->delete();
		}
	}
    /**
     * @param \Magento\Quote\Model\Quote\Item $item
     */
    private function setConfigurableBaseCost($item)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$children = $item->getChildren();
        if (is_array($children)) {
            $baseCost = 0;
            foreach ($children as $child) {
                $childCost = $child->getBaseCost();
                if (isset($childCost)) {
                    $baseCost += $childCost;
                }
            }
             $this->setBaseCost($baseCost);
        }
		}
	}
}
