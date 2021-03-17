<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items;

/**
 * Class PriceRenderer
 *
 * @package Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items
 */
class PriceRenderer extends DefaultRenderer
{
    /**
     * Calculate total amount for the (tier) item
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return float
     */
    public function getBaseTotalAmount($item)
    {
        $calculateItem = $item;
        if ($item->getTierItem()) {
            $calculateItem = $item->getTierItem();
        }

        return $this->itemPriceRenderer->getBaseTotalAmount($calculateItem);
    }

    /**
     * Calculate total amount for the (tier) item
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return float
     */
    public function getTotalAmount($item)
    {
        $calculateItem = $item;
        if ($item->getTierItem()) {
            $calculateItem = $item->getTierItem();
        }

        return $this->itemPriceRenderer->getTotalAmount($calculateItem);
    }

    /**
     * Calculate total amount excl. tax for the (tier) item
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return float
     */
    public function getTotalAmountExclTax($item)
    {
        $calculateItem = $item;
        if ($item->getTierItem()) {
            $calculateItem = $item->getTierItem();
        }

        return $calculateItem->getRowTotal()
            - $calculateItem->getDiscountAmount()
            + $calculateItem->getDiscountTaxCompensationAmount();
    }

    /**
     * Calculate base total amount excl. tax for the (tier) item
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return float
     */
    public function getBaseTotalAmountExclTax($item)
    {
        $calculateItem = $item;
        if ($item->getTierItem()) {
            $calculateItem = $item->getTierItem();
        }

        return $calculateItem->getBaseRowTotal()
            - $calculateItem->getBaseDiscountAmount()
            + $calculateItem->getBaseDiscountTaxCompensationAmount();
    }
}
