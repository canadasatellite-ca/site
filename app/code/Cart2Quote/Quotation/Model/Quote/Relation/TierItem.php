<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Quote\Relation;

use Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationInterface;

/**
 * Class TierItem
 *
 * @package Cart2Quote\Quotation\Model\Quote\Relation
 */
class TierItem implements RelationInterface
{
    use \Cart2Quote\Features\Traits\Model\Quote\Relation\TierItem {
        processRelation as private traitProcessRelation;
        isQuotation as private traitIsQuotation;
        hasCurrentTierItemId as private traitHasCurrentTierItemId;
        processExistingUpdatedQuoteItem as private traitProcessExistingUpdatedQuoteItem;
        processNewTierItems as private traitProcessNewTierItems;
    }

    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\CollectionFactory
     */
    protected $tierItemCollectionFactory;

    /**
     * @var \Cart2Quote\Quotation\Model\Quote\TierItemFactory
     */
    protected $tierItemFactory;

    /**
     * AddProduct constructor.
     *
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\CollectionFactory $tierItemCollectionFactory
     * @param \Cart2Quote\Quotation\Model\Quote\TierItemFactory $tierItemFactory
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\CollectionFactory $tierItemCollectionFactory,
        \Cart2Quote\Quotation\Model\Quote\TierItemFactory $tierItemFactory
    ) {
        $this->tierItemCollectionFactory = $tierItemCollectionFactory;
        $this->tierItemFactory = $tierItemFactory;
    }

    /**
     * Process object relations
     *
     * @param \Magento\Framework\Model\AbstractModel|\Magento\Quote\Model\Quote $object
     * @return void
     */
    public function processRelation(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->traitProcessRelation($object);
    }

    /**
     * Check if the quote is a quotation quote
     *
     * @param object|\Cart2Quote\Quotation\Model\Quote $object
     * @return bool
     */
    protected function isQuotation($object)
    {
        return $this->traitIsQuotation($object);
    }

    /**
     * Checks if the item has current tier item id
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return bool
     */
    protected function hasCurrentTierItemId($item)
    {
        return $this->traitHasCurrentTierItemId($item);
    }

    /**
     * Process existing quote item that has been updated (different configuration)
     *
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\Collection $existingTierItemCollection
     * @param \Magento\Quote\Model\Quote\Item $item
     */
    protected function processExistingUpdatedQuoteItem(&$existingTierItemCollection, &$item)
    {
        $this->traitProcessExistingUpdatedQuoteItem($existingTierItemCollection, $item);
    }

    /**
     * Process new tiers
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     */
    protected function processNewTierItems(&$item)
    {
        $this->traitProcessNewTierItems($item);
    }
}
