<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Observer\Quote;

/**
 * Class LoadTierItem
 *
 * @package Cart2Quote\Quotation\Observer\Quote
 */
class JoinTierItem implements \Magento\Framework\Event\ObserverInterface
{
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
     * Execute (observer entry point)
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $collection = $observer->getCollection();

        if ($collection instanceof \Magento\Quote\Model\ResourceModel\Quote\Item\Collection) {
            $tierItemTable = $collection->getTable('quotation_quote_tier_item');
            $collection->getSelect()->joinLeft(
                $tierItemTable,
                sprintf(
                    '%s.item_id = main_table.item_id AND %s.qty = main_table.qty',
                    $tierItemTable,
                    $tierItemTable
                ),
                sprintf('%s.entity_id AS current_tier_item_id', $tierItemTable)
            );
        }
    }
}
