<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Observer\Quote;

use Cart2Quote\Quotation\Model\Quote\TierItem;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class LoadTierItem
 *
 * @package Cart2Quote\Quotation\Observer\Quote
 */
class LoadTierItem implements ObserverInterface
{
    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\Collection
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
     * Execute (observer entypoint)
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $collection = $observer->getCollection();
        if ($collection instanceof \Magento\Quote\Model\ResourceModel\Quote\Item\Collection) {
            foreach ($collection as &$item) {
                $this->setItemTiers($item);
            }
        }

        $item = $observer->getItem();
        if ($item instanceof \Magento\Quote\Model\Quote\Item) {
            $this->setItemTiers($item);
        }
    }

    /**
     * Set tier items on the item
     *
     * @param TierItem $item
     */
    private function setItemTiers(&$item)
    {
        if ($currentTierItemId = $item->getCurrentTierItemId()) {
            $tierItemCollection = $this->tierItemCollectionFactory->create();
            $tierItemCollection->setItemTiers($item);
        }
    }
}
