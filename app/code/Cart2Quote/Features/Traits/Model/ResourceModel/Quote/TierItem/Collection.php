<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\ResourceModel\Quote\TierItem;
/**
 * Trait Collection
 *
 * @package Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem
 */
trait Collection
{
    /**
     * Set items function
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return $this
     */
    private function setItem(\Magento\Quote\Model\Quote\Item $item)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->_item = $item;
        $itemId = $item->getId();
        if ($itemId) {
            $this->addFieldToFilter('item_id', $item->getId());
        } else {
            $this->_totalRecords = 0;
            $this->_setIsLoaded(true);
        }
        return $this;
		}
	}
    /**
     * Checker if given qty for item id exit in tiers data
     *
     * @param int $itemId
     * @param int $qty
     * @return bool
     */
    private function tierExists($itemId, $qty)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->addFieldToFilter(
            \Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::ITEM_ID,
            $itemId
        )->addFieldToFilter(
            \Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::QTY,
            $qty
        )->getSize() > 0;
		}
	}
    /**
     * Checker if tiers exit for a given itemid
     *
     * @param int $itemId
     * @return bool
     */
    private function tierExistsForItem($itemId)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->addFieldToFilter(
            \Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::ITEM_ID,
            $itemId
        )
                ->getSize() > 0;
		}
	}
    /**
     * Get a tier for a given itemid and qty
     *
     * @param int $itemId
     * @param int $qty
     * @return \Cart2Quote\Quotation\Model\Quote\TierItem
     */
    private function getTier($itemId, $qty)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->addFieldToFilter(
            \Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::ITEM_ID,
            $itemId
        )->addFieldToFilter(
            \Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::QTY,
            $qty
        )->getFirstItem();
		}
	}
    /**
     * Get a tier by id
     *
     * @param int $tierItemId
     * @return \Cart2Quote\Quotation\Model\Quote\TierItem
     */
    private function getTierById($tierItemId)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->addFieldToFilter(
            \Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::ENTITY_ID,
            $tierItemId
        )->getFirstItem();
		}
	}
    /**
     * Get multiple tiers by a list of tier ids
     *
     * @param array $tierItemIds
     * @return \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\Collection
     */
    private function getTiersByIds($tierItemIds)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->addFieldToFilter(
            \Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::ENTITY_ID,
            ['in' => $tierItemIds]
        );
        return $this;
		}
	}
    /**
     * Get an array with the ID as key and record qty as value
     *
     * @param bool $format
     * @return array ['ID' => 'qty']
     */
    private function getQtys($format = true)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$qtys = [];
        /** @var \Cart2Quote\Quotation\Model\Quote\TierItem $item */
        foreach ($this->getItems() as $item) {
            $qty = $item->getQty();
            if (isset($qty)) {
                if ($format) {
                    $qty = $qty * 1; // remove the zeros in the decimal
                }
                $qtys[$item->getId()] = $qty;
            }
        }
        return $qtys;
		}
	}
    /**
     * Set tier items to a Quote item
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return \Magento\Quote\Model\Quote\Item
     */
    private function setItemTiers(\Magento\Quote\Model\Quote\Item $item)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->getTierItemsByItemId($item->getId());
        /** @var \Cart2Quote\Quotation\Model\Quote\TierItem $tier */
        foreach ($this as $tier) {
            if ($tier->getQty() == $item->getQty()) {
                $tier->setItem($item);
                $item->setCurrentTierItem($tier);
            }
        }
        foreach ($this as $id => $tier) {
            if ($tier->isDeleted()) {
                $this->removeItemByKey($id);
            }
        }
        return $item->setTierItems($this);
		}
	}
    /**
     * Get the tier items by id
     *
     * @param int $itemId
     * @param bool $orderByQty
     * @return $this
     */
    private function getTierItemsByItemId($itemId, $orderByQty = true)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->addFieldToFilter(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::ITEM_ID, $itemId);
        if ($orderByQty) {
            $this->addOrder('qty', self::SORT_ORDER_ASC);
        }
        return $this;
		}
	}
    /**
     * Model initialization
     *
     * @return void
     */
    private function _construct()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->_init(
            \Cart2Quote\Quotation\Model\Quote\TierItem::class,
            \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem::class
        );
		}
	}
    /**
     * After load trigger
     *
     * @return $this
     */
    private function _afterLoad()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			parent::_afterLoad();
        /**
         * @var \Cart2Quote\Quotation\Model\Quote\TierItem $item
         * */
        foreach ($this as $item) {
            if ($this->_item) {
                $item->setItem($this->_item);
            }
        }
        $this->resetItemsDataChanged();
        return $this;
		}
	}
}
