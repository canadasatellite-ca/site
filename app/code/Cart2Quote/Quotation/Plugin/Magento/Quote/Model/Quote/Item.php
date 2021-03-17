<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Plugin\Magento\Quote\Model\Quote;

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
     * @return \Magento\Catalog\Model\Product
     */
    public function afterGetProduct($quoteItem, $product)
    {
        $tierItem = $quoteItem->getCurrentTierItem();
        if ($tierItem instanceof \Cart2Quote\Quotation\Model\Quote\TierItem) {
            if ($tierItem->getCustomPrice()) {
                $tierItem->loadPriceOnItem($quoteItem);
                $tierItem->loadPriceOnProduct($product);
            }
        }

        return $product;
    }
}
