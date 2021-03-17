<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Quote;
/**
 * Factory class for @see \Cart2Quote\Quotation\Model\Quote\TierItem
 */
trait TierItemFactory
{
    /**
     * Create tier items for array of \Magento\Quote\Model\Quote\Item items
     *
     * @param \Magento\Quote\Model\Quote\Item[] $items
     * @return array
     */
    private function createFromItems($items)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$tierItems = [];
        foreach ($items as $item) {
            $tierItems[] = $this->createFromItem($item)->save();
        }
        return $tierItems;
		}
	}
    /**
     * Create a tier item from item
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param null|int $qty
     * @return tierItem|bool
     */
    private function createFromItem(\Magento\Quote\Model\Quote\Item $item, $qty = null)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if (!$this->bundleChildrenAreSaved($item)) {
            return false;
        }
        return $this->create()->setDataByItem($item, null, $qty)->save();
		}
	}
    /**
     * Check if the children of the bundle are saved before adding tiers
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return bool
     */
    private function bundleChildrenAreSaved(\Magento\Quote\Model\Quote\Item $item)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($item->getProductType() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
            foreach ($item->getChildren() as $child) {
                if (!$child->getId()) {
                    return false;
                }
            }
        }
        return true;
		}
	}
    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return \Cart2Quote\Quotation\Model\Quote\TierItem
     */
    private function create(array $data = [])
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->_objectManager->create($this->_instanceName, $data);
		}
	}
    /**
     * Process new tier items
     *
     * @param array $newTierItems
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return array
     */
    private function processNewTierItems(array $newTierItems, \Magento\Quote\Model\Quote\Item $item)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$tierItems = [];
        foreach ($newTierItems as $newTierItem) {
            $item->setCustomPrice($newTierItem['custom_price']);
            $tierItem = $this->createFromItem($item, $newTierItem['qty'])->save();
            $tierItem->setCustomPrice($newTierItem['custom_price']);
            $tierItem->setBaseCustomPrice($item->getQuote()->getBaseGrandTotal());
        }
        return $tierItems;
		}
	}
}
