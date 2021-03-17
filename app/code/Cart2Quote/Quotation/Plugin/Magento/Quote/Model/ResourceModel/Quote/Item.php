<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Plugin\Magento\Quote\Model\ResourceModel\Quote;

use Magento\Catalog\Model\Product;

/**
 * Class Address
 *
 * @package Cart2Quote\Quotation\Plugin\Magento\Quote\Model
 */
class Item
{
    /**
     * Quotation Session
     *
     * @var \Cart2Quote\Quotation\Model\Session
     */
    protected $quoteSession;

    /**
     * Address constructor.
     *
     * @param \Cart2Quote\Quotation\Model\Session $quoteSession
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\Session $quoteSession
    ) {
        $this->quoteSession = $quoteSession;
    }

    /**
     * Apply tier product price
     *
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @param \Magento\Catalog\Model\Product $product
     */
    public function beforeLoad($quoteItem, $product)
    {
        if ($quoteItem instanceof \Magento\Quote\Model\Quote\Item) {
            $tierItem = $quoteItem->getCurrentTierItem();
            if ($tierItem instanceof \Cart2Quote\Quotation\Model\Quote\TierItem) {
                $this->setItemPrice($product, $quoteItem, $tierItem);
            }
        }
    }

    /**
     * Set item price
     *
     * @param Product $product
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @param \Cart2Quote\Quotation\Model\Quote\TierItem $tierItem
     */
    protected function setItemPrice(&$product, &$quoteItem, $tierItem)
    {
        $product->setData('final_price', $tierItem->getCustomPrice());
        $quoteItem->setData('calculation_price', $tierItem->getCustomPrice());
        $quoteItem->setCustomPrice($tierItem->getCustomPrice());
        $quoteItem->setOriginalCustomPrice($tierItem->getCustomPrice());
    }
}
