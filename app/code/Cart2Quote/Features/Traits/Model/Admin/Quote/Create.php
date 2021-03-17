<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Admin\Quote;
use Magento\Quote\Model\Quote\Item;
/**
 * Trait Create
 *
 * @package Cart2Quote\Quotation\Model\Admin\Quote
 */
trait Create
{
    /**
     * Update quantity of quote items
     *
     * @param array $items
     * @return \Magento\Sales\Model\AdminOrder\Create|array
     * @throws \Exception|\Magento\Framework\Exception\LocalizedException
     */
    private function updateQuotationItems($items)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if (!is_array($items)) {
            return $this;
        }
        try {
            foreach ($items as $itemId => $info) {
                $this->setQtyInput($info);
                if (!empty($info['configured'])) {
                    $item = $this->getQuote()->updateItem($itemId, $this->objectFactory->create($info));
                    $this->setConfiguredQtyItem($items, $item);
                    $this->setItemMergedMessage($item);
                } else {
                    $item = $this->getQuote()->getItemById($itemId);
                    if (!$item) {
                        continue;
                    }
                    $info['qty'] = (double)$info['qty'];
                }
                $this->quoteItemUpdater->update($item, $info);
                if ($item && !empty($info['action'])) {
                    $this->moveQuoteItem($item, $info['action'], $item->getQty());
                }
            }
        } catch (\Magento\Framework\Exception\AlreadyExistsException $e) {
           $this->setMergedErrorMessage($itemId);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->recollectCart();
            throw $e;
            // phpcs:ignore Magento2.Exceptions.ThrowCatch
        } catch (\Exception $e) {
            $this->_logger->critical($e);
        }
        $this->recollectCart();
        return $items;
		}
	}
    /**
     * Set correct qty value to item
     *
     * @param array $info
     */
    private function setQtyInput(&$info)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$selectedTier = $info['selected_tier'];
        if (isset($selectedTier)) {
            $tierInfo = $info['tier_item'][$selectedTier];
            $info['qty'] = $tierInfo['qty'];
        }
		}
	}
    /**
     * Needed for merging equal configurable and bundle items
     *
     * @param array $items
     * @param \Magento\Quote\Model\Quote\Item $item
     */
    private function setConfiguredQtyItem(&$items, $item)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$itemId = $item->getItemId();
        if (isset($itemId)) {
            $existingItem = &$items[$itemId];
            $selectedTier = $existingItem['selected_tier'];
            if (isset($selectedTier)) {
                $tierInfo = &$existingItem['tier_item'][$selectedTier];
                $tierInfo['qty'] = (double)$item->getQty();
            }
        }
		}
	}
    /**
     * Generate success message for configured item when merged with existing item
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     */
    private function setItemMergedMessage($item)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$quote = $this->getQuote();
        $items = $quote->getItemsCollection();
        $productId = $item->getProductId();
        foreach ($items as $existingItem) {
            if ($existingItem->getProductId() == $productId) {
                if ($existingItem->isDeleted()) {
                    $this->messageManager->addSuccessMessage(
                        __(
                            'Newly configured product already exist in quote. Successfully merged %1 into %2',
                            $existingItem->getSku(),
                            $item->getSku()
                        )
                    );
                }
            }
        }
		}
	}
    /**
     * Generate error message for configured item with existing tier quantity
     *
     * @param int $itemId
     */
    private function setMergedErrorMessage($itemId)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$quote = $this->getQuote();
        $items = $quote->getAllVisibleItems();
        foreach ($items as $existingItem) {
            if ($existingItem->getItemId() == $itemId) {
                $this->messageManager->addErrorMessage(
                    __(
                        'Quantity already exists in configuration. Unable to configure %1',
                        $existingItem->getSku()
                    )
                );
            }
        }
		}
	}
    /**
     * Update tier items of quotation items
     *
     * @param array $items
     * @return $this
     * @throws \Exception|\Magento\Framework\Exception\LocalizedException
     */
    private function updateTierItems($items)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if (!is_array($items)) {
            return $this;
        }
        try {
            foreach ($items as $itemId => $info) {
                if (!empty($info['tier_item']) && is_array($info['tier_item'])) {
                    $item = $this->getQuote()->getItemById($itemId);
                    if (!$item->isDeleted() &&
                        !(isset($info['action']) && $info['action'] == 'remove')) {
                        $selectedTierId = $info['selected_tier'];
                        $newTierItems = [];
                        unset($info['tier_item']['%template%']);
                        if (isset($info['tier_item']['new'])) {
                            if ($this->quotationDataHelper->isStockEnabledBackend()) {
                                $this->processItems($item, $info['tier_item']['new']);
                            }
                            $this->processCustomPrice($info['tier_item']['new']);
                            $newTierItems = $info['tier_item']['new'];
                            unset($info['tier_item']['new']);
                        }
                        $info['tier_item'] = $this->processOptionalValues($info['tier_item']);
                        $this->processCustomPrice($info['tier_item']);
                        $existingTierItems = $this->processExistingTierItems($item, $info);
                        $existingTierItems = $this->processNewTierItems($existingTierItems, $newTierItems, $item);
                        $existingTierItems = $this->calculateTierPrices($existingTierItems, $itemId, $selectedTierId);
                        if ($selectedTier = $existingTierItems->getItemById($selectedTierId)) {
                            $this->setCurrentTierItemData($selectedTier, $item);
                            $existingTierItems->setItemTiers($item);
                        }
                    } elseif ($item->isDeleted() && isset($info['configured']) && $info['configured']) {
                        if ($item->getCurrentTierItem()->getQty() != $info['qty']) {
                            foreach ($item->getTierItems() as $tierItem) {
                                if ($tierItem->getQty() == $info['qty']) {
                                    $item->setCurrentTierItem($tierItem);
                                    break;
                                }
                            }
                            $item->getCurrentTierItem()->setQty($info['qty']);
                        }
                    }
                }
            }
        } catch (\Magento\Framework\Exception\AlreadyExistsException $e) {
            $this->messageManager->addErrorMessage(
                __(
                    'Quantity already exists for requested item: %1.(%2.)',
                    $item->getName(),
                    $e->getMessage()
                )
            );
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->recollectCart();
            throw $e;
        } catch (\Exception $e) {
            $this->_logger->critical($e);
        }
        return $this;
		}
	}
    /**
     * Process optional values
     * - Set the "on" value to true for saving
     *
     * @param array $tierItems
     * @return array
     */
    private function processOptionalValues(array $tierItems)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			foreach ($tierItems as &$tierItem) {
            if (isset($tierItem['make_optional']) && $tierItem['make_optional'] == "on") {
                $tierItem['make_optional'] = true;
            }
        }
        return $tierItems;
		}
	}
    /**
     * Check for allowed custom price value
     *
     * @param array $tierItems
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function processCustomPrice(array $tierItems)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			foreach ($tierItems as &$tierItem) {
            if ($tierItem['custom_price'] < 0) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Please enter a number 0 or greater in this field.')
                );
            }
        }
		}
	}
    /**
     * Process item for quantity check
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param array $tierItems
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function processItems($item, array $tierItems)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$product = $item->getProduct();
        foreach ($tierItems as &$tierItem) {
            switch ($product->getTypeId()) {
                case \Magento\Bundle\Model\Product\Type::TYPE_CODE:
                    $this->processBundle($item, $tierItem['qty']);
                    break;
                case \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE:
                    $this->processConfigurable($item, $tierItem['qty']);
                    break;
                default:
                    $this->processQuantity($product->getId(), $tierItem['qty']);
                    break;
            }
        }
		}
	}
    /**
     * Process bundle product for quantity check
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param int $tierItemQty
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function processBundle($item, $tierItemQty)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$children = $item->getChildren();
        if (isset($children)) {
            foreach ($children as $bundleItem) {
                $qty = $bundleItem->getQty();
                $finalQty = $tierItemQty * $qty;
                $this->processQuantity($bundleItem->getProductId(), $finalQty);
            }
        }
		}
	}
    /**
     * Process configurable product for quantity check
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param int $tierItemQty
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function processConfigurable($item, $tierItemQty)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$children = $item->getChildren();
        if (isset($children[0])) {
            $productId = $children[0]->getProductId();
            if (isset($productId)) {
                $this->processQuantity($productId, $tierItemQty);
            }
        }
		}
	}
    /**
     * Check tier quantity for stock settings
     *
     * @param int $productId
     * @param int $qty
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function processQuantity($productId, $qty)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$stockItem = $this->stockRegistry->getStockItem($productId);
        $result = $this->stockStateProvider->checkQuoteItemQty(
            $stockItem,
            $qty,
            $qty,
            $qty
        );
        if ($result->getHasError()
            || $result->getMessage()
            && $stockItem->getBackorders() == \Magento\CatalogInventory\Model\Stock::BACKORDERS_NO
        ) {
            throw new \Magento\Framework\Exception\LocalizedException(__($result->getMessage()));
        }
		}
	}
    /**
     * Process existing tier items
     *
     * @param int $item
     * @param array $info
     * @return \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\Collection
     */
    private function processExistingTierItems($item, $info)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$tierItemCollection = $this->tierItemCollectionFactory->create();
        $tierItemCollection->getTierItemsByItemId($item->getId());
        /**
         * @var int $id
         * @var \Cart2Quote\Quotation\Model\Quote\TierItem $tierItem
         */
        foreach ($tierItemCollection as $id => $tierItem) {
            if (isset($info['tier_item'][$id])) {
                $tierItem = $tierItemCollection->getItemById($id);
                $info['tier_item'][$id] = $this->processCustomPriceTax($tierItem, $info);
                $info['tier_item'][$id] = $this->processPercentageDiscount($tierItem, $info);
                $info['tier_item'][$id] = $this->processCostPrice($tierItem, $info);
                $info['tier_item'][$id] = $this->processBaseCustomPrice($tierItem, $info);
                $tierItem->setData(array_replace($tierItem->getData(), $info['tier_item'][$id]));
            } else {
                $tierItem->delete();
            }
        }
        return $tierItemCollection;
		}
	}
    /**
     * Process percentage discount on single line item
     *
     * @param \Cart2Quote\Quotation\Model\Quote\TierItem $tierItem
     * @param array $info
     * @return array
     */
    private function processPercentageDiscount($tierItem, $info)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if (isset($info['tier_item'][$tierItem->getEntityId()])) {
            $tierItemInfo = $info['tier_item'][$tierItem->getEntityId()];
            $percentageDiscount = $tierItemInfo['percentage_discount'];
            if ($percentageDiscount != "") {
                $percentageDiscount = floatval($percentageDiscount);
            }
            if (!empty($percentageDiscount) || $percentageDiscount === 0.0) {
                //chose to calculate including or excluding tax
                $store = $this->getQuote()->getStore();
                if ($this->quotationTaxHelper->customPriceIncludesTax($store)) {
                    $originalPrice = $tierItem->getOriginalPriceInclTax();
                } else {
                    $originalPrice = $tierItem->getOriginalPrice();
                }
                $percentageDiscount = 100 - $percentageDiscount;
                $customPrice = $originalPrice * ($percentageDiscount / 100);
                $tierItemInfo['custom_price'] = $customPrice;
            }
            return $tierItemInfo;
        }
		}
	}
    /**
     * Process pcustom price tax
     *
     * @param \Cart2Quote\Quotation\Model\Quote\TierItem $tierItem
     * @param array $info
     * @return array
     */
    private function processCustomPriceTax($tierItem, $info)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if (isset($info['tier_item'][$tierItem->getEntityId()])) {
            $tierItemInfo = $info['tier_item'][$tierItem->getEntityId()];
            $customPrice = $tierItemInfo['custom_price'];
            if ($customPrice != "") {
                $customPrice = floatval($customPrice);
            }
            if (!empty($customPrice)) {
                $customPrice = $this->getCorrectedCustomPrice($tierItem->getItem(), $customPrice);
                $tierItemInfo['custom_price'] = $customPrice;
            }
            return $tierItemInfo;
        }
		}
	}
    /**
     * Function that generates the custom base price
     *
     * @param \Cart2Quote\Quotation\Model\Quote\TierItem $tierItem
     * @param array $info
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function processBaseCustomPrice($tierItem, $info)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if (isset($info['tier_item'][$tierItem->getEntityId()])) {
            $tierItemInfo = $info['tier_item'][$tierItem->getEntityId()];
            $customPrice = $tierItemInfo['custom_price'];
            if ($customPrice != "") {
                $customPrice = floatval($customPrice);
            }
            if (!empty($customPrice)) {
                $baseCustomPrice = $this->getQuote()->convertPriceToQuoteBaseCurrency(
                    $this->getCorrectedCustomPrice($tierItem->getItem(), $customPrice)
                );
                $tierItemInfo['base_custom_price'] = $baseCustomPrice;
            }
            return $tierItemInfo;
        }
		}
	}
    /**
     * Process base cost on single line item
     *
     * @param \Cart2Quote\Quotation\Model\Quote\TierItem $tierItem
     * @param array $info
     * @return array
     */
    private function processCostPrice($tierItem, $info)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if (isset($info['tier_item'][$tierItem->getEntityId()])) {
            $tierItemInfo = $info['tier_item'][$tierItem->getEntityId()];
            $baseCost = $tierItemInfo['base_cost'];
            if (!empty($baseCost)) {
                $quote = $this->getQuote();
                $quoteCurrency = $quote->getQuoteCurrency();
                $baseCurrency = $quote->getBaseCurrency();
                if ($quoteCurrency != $baseCurrency) {
                    try {
                        $baseCost = $this->getQuote()->convertPriceToQuoteBaseCurrency($baseCost);
                    } catch (\Exception $e) {
                        $logMessage = sprintf("No conversion rate set: %s", $e);
                        $this->_logger->notice($logMessage);
                    }
                }
                $tierItemInfo['base_cost'] = $baseCost;
            }
            return $tierItemInfo;
        }
		}
	}
    /**
     * Process new tier items
     *
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\Collection $existingTiers
     * @param array $newTierItems
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\Collection
     */
    private function processNewTierItems(
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\Collection $existingTiers,
        $newTierItems,
        $item
    ) {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$existingQtys = [];
        if (!empty($newTierItems)) {
            $existingQtys = $existingTiers->getQtys();
        }
        foreach ($newTierItems as $newTierItem) {
            if (in_array(($newTierItem['qty'] * 1), $existingQtys)) {
                continue;
            }
            $tierItem = $this->tierItemFactory->createFromItem($item, $newTierItem['qty']);
            $tierItem->setCustomPrice($this->getCorrectedCustomPrice($item, $newTierItem['custom_price']));
            $tierItem->setBaseCustomPrice(
                $this->getQuote()->convertPriceToQuoteBaseCurrency(
                    $this->getCorrectedCustomPrice($item, $newTierItem['custom_price'])
                )
            );
            $tierItem->setBaseCost(
                $this->getQuote()->convertPriceToQuoteBaseCurrency($newTierItem['base_cost'])
            );
            $existingTiers->addItem($tierItem);
        }
        return $existingTiers;
		}
	}
    /**
     * Calculate new custom_price when changing from currency to another in backend
     *
     * @param \Cart2Quote\Quotation\Model\Quote\TierItem $tierItem
     * @param \Magento\Quote\Model\Quote $quote
     */
    private function setCurrencyCustomPrice($tierItem, $quote)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$storeToQuoteRate = $quote->getStoreToQuoteRate();
        if (!isset($storeToQuoteRate) || $storeToQuoteRate == 0) {
            $storeToQuoteRate = 1;
        }
        $newPrice = $tierItem->getCustomPrice() * $storeToQuoteRate;
        $tierItem->setCustomPrice($newPrice);
		}
	}
    /**
     * Calculate tier price
     *
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\Collection $tierItemCollection
     * @param int $quoteItemId
     * @param int $selectedTierId
     * @return \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\Collection
     */
    private function calculateTierPrices(
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\Collection $tierItemCollection,
        $quoteItemId,
        $selectedTierId
    ) {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$quote = $this->getQuote();
        $quoteItem = $quote->getItemById($quoteItemId);
        $bundleFinalPrices = [];
        if ($quoteItem instanceof \Magento\Quote\Model\Quote\Item) {
            /** @var \Cart2Quote\Quotation\Model\Quote\TierItem $tierItem */
            foreach ($tierItemCollection as &$tierItem) {
                if ($this->_needCollect && !$tierItem->isDeleted()) {
                    $tierId = $tierItem->getId();
                    if ($this->quotationDataHelper->convertOnChange()) {
                        $this->setCurrencyCustomPrice($tierItem, $quote);
                    }
                    if ($tierId != $selectedTierId) {
                        //don't affect the original quoteItem when the selected tier id isn't this tier item
                        $quoteItem = clone $quoteItem;
                    }
                    $quoteItem = $tierItem->setItem($quoteItem)->setSelected();
                    $quote->setTotalsCollectedFlag(false)->collectTotals();
                    $product = $quoteItem->getProduct();
                    if ($quoteItem->getProductType() === \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
                        $found = false;
                        if (!$bundleFinalPrices) {
                            $finalPrice = $tierItem->getBaseOriginalPrice();
                            $bundleFinalPrices = [$tierItem->getItemId() => $finalPrice];
                        } else {
                            foreach ($bundleFinalPrices as $itemId => $bundleFinalPrice) {
                                if ($tierItem->getItemId() == $itemId) {
                                    $finalPrice = $bundleFinalPrice;
                                    $found = true;
                                }
                            }
                            if (!$found) {
                                $finalPrice = $tierItem->getBaseOriginalPrice();
                                $bundleFinalPrices = [$tierItem->getItemId() => $finalPrice];
                            }
                        }
                    } else {
                        $item = $tierItem->getItem();
                        $storeId = $item->getStoreId();
                        if ($this->quotationTaxHelper->priceIncludesTax($storeId)) {
                            $finalPrice = $this->quotationTaxHelper->getFinalPriceExclTax($item, $tierItem, $product);
                        } else {
                            $finalPrice = $product->getPriceModel()->getFinalPrice($tierItem->getQty(), $product);
                        }
                    }
                    $tierItem = $tierItem
                        ->setData(
                            array_replace(
                                $tierItem->getData(),
                                $quoteItem->getData(),
                                [
                                    'base_original_price' => $finalPrice,
                                    'original_price' => $this->getQuote()->convertPriceToQuoteCurrency($finalPrice)
                                ]
                            )
                        )
                        ->setId($tierId)
                        ->setItemId($quoteItemId)
                        ->save();
                    if ($quoteItem->getProductType() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE
                        && $tierId == $selectedTierId) {
                        foreach ($quoteItem->getChildren() as $child) {
                            if ($currentTierItem = $child->getCurrentTierItem()) {
                                $currentTierItem->save();
                            }
                        }
                    }
                }
            }
            $quote->setStoreToQuoteRate(1);
        }
        return $tierItemCollection;
		}
	}
    /**
     * Set the current tier item data to quote item
     *
     * @param \Cart2Quote\Quotation\Model\Quote\TierItem $tierItem
     * @param \Magento\Quote\Model\Quote\Item &$item
     */
    private function setCurrentTierItemData($tierItem, \Magento\Quote\Model\Quote\Item &$item)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$tierItem->setItem($item)->setSelected();
        $this->getQuote()->setTotalsCollectedFlag(false);
		}
	}
    /**
     * @param \Magento\Quote\Model\Quote\Item $tierItem
     * @param int|float $customPrice
     * @return int|float
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @see \Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\Columns\PriceQuoted::getPriceWithCorrectTax
     */
    private function getCorrectedCustomPrice(\Magento\Quote\Model\Quote\Item $item, $customPrice)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$magentoVersion = $this->quotationTaxHelper->getMagentoVersion();
        //check if custom price is used including tax
        $store = $this->getQuote()->getStore();
        if ($this->quotationTaxHelper->priceIncludesTax($store)
            && !$this->quotationTaxHelper->applyTaxOnCustomPrice($store)
        ) {
            //check this until MC-30483 is fixed: https://github.com/magento/magento2/issues/26394
            if (version_compare($magentoVersion, "2.3.1", ">")) {
                //make sure items has quote
                if(!$item->getQuote()) {
                    $item->setQuote($this->getQuote());
                }
                //for this version of magento we need to remove the tax on this input
                $rate = $this->quotationTaxHelper->getTaxCalculationRate($item, true);
                $customPrice = $customPrice / $rate;
            }
        }
        return $customPrice;
		}
	}
}
