<?php
/**
 * /**
 *
 * CART2QUOTE CONFIDENTIAL
 * __________________
 *
 *  [2009] - [2017] Cart2Quote B.V.
 *  All Rights Reserved.
 *
 * NOTICE OF LICENSE
 *
 * All information contained herein is, and remains
 * the property of Cart2Quote B.V. and its suppliers,
 * if any.  The intellectual and technical concepts contained
 * herein are proprietary to Cart2Quote B.V.
 * and its suppliers and may be covered by European and Foreign Patents,
 * patents in process, and are protected by trade secret or copyright law.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from Cart2Quote B.V.
 *
 * @category    Cart2Quote
 * @package     Quotation
 * @copyright   Copyright (c) 2017 Cart2Quote B.V. (https://www.cart2quote.com)
 * @license     https://www.cart2quote.com/ordering-licenses(https://www.cart2quote.com)
 */

namespace Cart2Quote\Quotation\Observer\Quote;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class UpdateTierItem
 * @package Cart2Quote\Quotation\Observer\Quote
 */
class UpdateTierItem implements ObserverInterface
{
    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\CollectionFactory
     */
    protected $tierItemCollectionFactory;

    /**
     * AddProduct constructor.
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\CollectionFactory $tierItemCollectionFactory
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\CollectionFactory $tierItemCollectionFactory
    ) {
        $this->tierItemCollectionFactory = $tierItemCollectionFactory;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $item = $observer->getItem();

        /**
         * @var \Magento\Quote\Model\Quote\Item $item
         */
        if ($item->getQuote()->getQuoteId() && $item->getId() && !$item->isDeleted()) {
            if (!$this->tierItemCollectionFactory->create()->tierExistsForItem($item->getId())) {
                $item->getQuote()->addTier($item);
            } else {
                /**
                 * @var \Cart2Quote\Quotation\Model\Quote\TierItem $tierItem
                 */
                $tierItems = $this->tierItemCollectionFactory->create()->setItem($item);
                foreach ($tierItems->getTierItemsByItemId($item->getId()) as $tierItem) {
                    if ($this->tierItemCollectionFactory->create()->tierExistsForItem($item->getId()) && !$item->getCurrentTierItem()->getId()) {
                        $tierItem->setQty($item->getQty());
                        $tierItem->save();
                    }
                }
            }
        }
    }
}
