<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MageSuper\Casat\Model\CatalogInventory\QuantityValidator;

use Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\Initializer\StockItem as originalClass;


class StockItem extends originalClass
{
    /**
     * Initialize stock item
     *
     * @param \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @param int $qty
     *
     * @return \Magento\Framework\DataObject
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    function initialize(
        \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem,
        \Magento\Quote\Model\Quote\Item $quoteItem,
        $qty
    ) {
        $product = $quoteItem->getProduct();
        /**
         * When we work with subitem
         */
        if ($quoteItem->getParentItem()) {
            $rowQty = $quoteItem->getParentItem()->getQty() * $qty;
            /**
             * we are using 0 because original qty was processed
             */
            $qtyForCheck = $this->quoteItemQtyList
                ->getQty($product->getId(), $quoteItem->getId(), $quoteItem->getQuoteId(), 0);
        } else {
            $increaseQty = $quoteItem->getQtyToAdd() ? $quoteItem->getQtyToAdd() : $qty;
            $rowQty = $qty;
            $qtyForCheck = $this->quoteItemQtyList->getQty(
                $product->getId(),
                $quoteItem->getId(),
                $quoteItem->getQuoteId(),
                $increaseQty
            );
        }

        $productTypeCustomOption = $product->getCustomOption('product_type');
        if ($productTypeCustomOption !== null) {
            // Check if product related to current item is a part of product that represents product set
            if ($this->typeConfig->isProductSet($productTypeCustomOption->getValue())) {
                $stockItem->setIsChildItem(true);
            }
        }

        $stockItem->setProductName($product->getName());

        $result = $this->stockState->checkQuoteItemQty(
            $product->getId(),
            $rowQty,
            $qtyForCheck,
            $qty,
            $product->getStore()->getWebsiteId()
        );

        if ($stockItem->hasIsChildItem()) {
            $stockItem->unsIsChildItem();
        }

        if ($result->getItemIsQtyDecimal() !== null) {
            $quoteItem->setIsQtyDecimal($result->getItemIsQtyDecimal());
            if ($quoteItem->getParentItem()) {
                $quoteItem->getParentItem()->setIsQtyDecimal($result->getItemIsQtyDecimal());
            }
        }

        /**
         * Just base (parent) item qty can be changed
         * qty of child products are declared just during add process
         * exception for updating also managed by product type
         */
        if ($result->getHasQtyOptionUpdate() && (!$quoteItem->getParentItem() ||
                $quoteItem->getParentItem()->getProduct()->getTypeInstance()->getForceChildItemQtyChanges(
                    $quoteItem->getParentItem()->getProduct()
                )
            )
        ) {
            $quoteItem->setData('qty', $result->getOrigQty());
        }

        if ($result->getItemUseOldQty() !== null) {
            $quoteItem->setUseOldQty($result->getItemUseOldQty());
        }

        if ($result->getMessage() !== null) {
            $quoteItem->setMessage($result->getMessage());
        }

        if ($result->getItemBackorders() !== null) {
            $quoteItem->setBackorders($result->getItemBackorders());
        }

        $quoteItem->setStockStateResult($result);

        //return $result;

        $quoteItemOptions = $quoteItem->getProduct()->getTypeInstance(true)->getOrderOptions($quoteItem->getProduct());


        if (!$result->getHasError()) {
            $requiredOptions = [];
            $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $customOptions = $_objectManager->get('Magento\Catalog\Model\Product\Option')->getProductOptionCollection($quoteItem->getProduct());
            foreach ($customOptions as $customOption) {
                if ($customOption->getIsRequire()) {
                    $requiredOptions[] = $customOption->getId();
                }
            }
            $optionIsset = true;
            foreach ($requiredOptions as $optionId) {
                $optionIsset = false;
                if (isset ($quoteItemOptions['options'])) {
                    foreach ($quoteItemOptions['options'] as $option) {
                        if ($option['option_id'] == $optionId) {
                            $optionIsset = true;
                        }
                    }
                }
            }
            if (!$optionIsset) {
                $result->setHasError(true)
                    ->setMessage(__('Required was not selected.'))
                    ->setErrorCode('required_option_missed')
                    ->setQuoteMessage(__('Required was not selected.'))
                    ->setQuoteMessageIndex('required_option_missed');
                return $result;
            }
        }
        return $result;
    }
}
