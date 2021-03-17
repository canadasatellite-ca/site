<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem;

/**
 * Class Collection
 *
 * @package Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    use \Cart2Quote\Features\Traits\Model\ResourceModel\Quote\TierItem\Collection {
        setItem as private traitSetItem;
        tierExists as private traitTierExists;
        tierExistsForItem as private traitTierExistsForItem;
        getTier as private traitGetTier;
        getTierById as private traitGetTierById;
        getTiersByIds as private traitGetTiersByIds;
        getQtys as private traitGetQtys;
        setItemTiers as private traitSetItemTiers;
        getTierItemsByItemId as private traitGetTierItemsByItemId;
        _construct as private _traitConstruct;
        _afterLoad as private _traitAfterLoad;
    }

    /**
     * @var \Magento\Quote\Model\Quote\Item $_item
     */
    protected $_item;

    /**
     * Set items function
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return $this
     */
    public function setItem(\Magento\Quote\Model\Quote\Item $item)
    {
        return $this->traitSetItem($item);
    }

    /**
     * Checker if given qty for item id exit in tiers data
     *
     * @param int $itemId
     * @param int $qty
     * @return bool
     */
    public function tierExists($itemId, $qty)
    {
        return $this->traitTierExists($itemId, $qty);
    }

    /**
     * Checker if tiers exit for a given itemid
     *
     * @param int $itemId
     * @return bool
     */
    public function tierExistsForItem($itemId)
    {
        return $this->traitTierExistsForItem($itemId);
    }

    /**
     * Get a tier for a given itemid and qty
     *
     * @param int $itemId
     * @param int $qty
     * @return \Cart2Quote\Quotation\Model\Quote\TierItem
     */
    public function getTier($itemId, $qty)
    {
        return $this->traitGetTier($itemId, $qty);
    }

    /**
     * Get a tier by id
     *
     * @param int $tierItemId
     * @return \Cart2Quote\Quotation\Model\Quote\TierItem
     */
    public function getTierById($tierItemId)
    {
        return $this->traitGetTierById($tierItemId);
    }

    /**
     * Get multiple tiers by a list of tier ids
     *
     * @param array $tierItemIds
     * @return \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\Collection
     */
    public function getTiersByIds($tierItemIds)
    {
        return $this->traitGetTiersByIds($tierItemIds);
    }

    /**
     * Get an array with the ID as key and record qty as value
     *
     * @param bool $format
     * @return array ['ID' => 'qty']
     */
    public function getQtys($format = true)
    {
        return $this->traitGetQtys($format);
    }

    /**
     * Set tier items to a Quote item
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return \Magento\Quote\Model\Quote\Item
     */
    public function setItemTiers(\Magento\Quote\Model\Quote\Item $item)
    {
        return $this->traitSetItemTiers($item);
    }

    /**
     * Get the tier items by id
     *
     * @param int $itemId
     * @param bool $orderByQty
     * @return $this
     */
    public function getTierItemsByItemId($itemId, $orderByQty = true)
    {
        return $this->traitGetTierItemsByItemId($itemId, $orderByQty);
    }

    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_traitConstruct();
    }

    /**
     * After load trigger
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        return $this->_traitAfterLoad();
    }
}
