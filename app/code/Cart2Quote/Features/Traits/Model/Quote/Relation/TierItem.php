<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Quote\Relation;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationInterface;
/**
 * Trait TierItem
 *
 * @package Cart2Quote\Quotation\Model\Quote\Relation
 */
trait TierItem
{
    /**
     * Process object relations
     *
     * @param \Magento\Framework\Model\AbstractModel|\Magento\Quote\Model\Quote $object
     * @return void
     */
    private function processRelation(\Magento\Framework\Model\AbstractModel $object)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if (!$this->isQuotation($object)) {
            return;
        }
        /** @var \Magento\Quote\Model\Quote\Item $item */
        foreach ($object->getAllItems() as &$item) {
            if (!$this->hasCurrentTierItemId($item)) {
                $tierItemCollectionFactory = $this->tierItemCollectionFactory->create();
                if (!$tierItemCollectionFactory->tierExists($item->getId(), $item->getQty())) {
                    $existingTierItemCollection = $item->getTierItems();
                    if ($existingTierItemCollection
                        instanceof \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\Collection) {
                        $this->processExistingUpdatedQuoteItem($existingTierItemCollection, $item);
                    } else {
                        $this->processNewTierItems($item);
                    }
                }
            }
        }
		}
	}
    /**
     * Check if the quote is a quotation quote
     *
     * @param object|\Cart2Quote\Quotation\Model\Quote $object
     * @return bool
     */
    private function isQuotation($object)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $object->getId() && $object instanceof \Cart2Quote\Quotation\Model\Quote;
		}
	}
    /**
     * Checks if the item has current tier item id
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return bool
     */
    private function hasCurrentTierItemId($item)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $item->getId() && !$item->isDeleted() && $item->getCurrentTierItemId();
		}
	}
    /**
     * Process existing quote item that has been updated (different configuration)
     *
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\Collection $existingTierItemCollection
     * @param \Magento\Quote\Model\Quote\Item $item
     */
    private function processExistingUpdatedQuoteItem(&$existingTierItemCollection, &$item)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$existingTierItemCollection->walk("setItemId", [$item->getId()]);
        $existingTierItemCollection->walk("unsetData", ['id']);
        $existingTierItemCollection->walk("unsetData", ['entity_id']);
        $existingTierItemCollection->save();
        $item->unsetData('tier_items');
        $item->unsetData('current_tier_item');
		}
	}
    /**
     * Process new tiers
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     */
    private function processNewTierItems(&$item)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$tierItem = $this->tierItemFactory->createFromItem($item);
        if ($tierItem) {
            $tierItem->save();
        }
        $this->tierItemCollectionFactory->create()->setItemTiers($item);
		}
	}
}
