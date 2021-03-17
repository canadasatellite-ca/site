<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\OrdersExportImport\Plugin\Sales\Model\Order;

/**
 * Class Item
 * @package Aitoc\OrdersExportImport\Plugin\Sales\Model\Order\
 */
class Item
{
    /**
     * Retrieve rendered column html content
     *
     * @param \Magento\Framework\DataObject $item
     * @param string $column the column key
     * @param string $field the custom item field
     * @return string
     */
    public function aroundGetColumnHtml(
        \Magento\Sales\Block\Adminhtml\Items\AbstractItems $items,
        \Closure $work,
        \Magento\Framework\DataObject $item,
        $column,
        $field = null
    ) {
        if (!$this->searchProduct($item->getProductId(), $item->getName())) {
            switch ($column) {
                case 'product':
                    return $item->getName();
                case 'name':
                    return $item->getName();
                case 'status':
                    return $item->getStatus();
                    break;
                case 'price-original':
                    return $items->displayPriceAttribute('original_price');
                    break;
                case 'price':
                    return $item->getPrice();
                    break;
                case 'qty':
                    return $item->getQtyOrdered();
                    break;
                case 'subtotal':
                    return $this->getSubTotal($item);
                    break;
                case 'tax-amount':
                    return $items->displayPriceAttribute('tax_amount');
                    break;
                case 'tax-percent':
                    return $items->displayTaxPercent($item);
                    break;
                case 'discont':
                    return $items->displayPriceAttribute('discount_amount');
                    break;
                case 'total':
                    return $item->getRowTotal();
                    break;
            };
        } else {
            return $work($item, $column, $field);
        }

        return '&nbsp;';
    }

    /**
     * @param $id
     * @param $name
     * @return int
     */
    public function searchProduct($id, $name)
    {
        $product = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Catalog\Model\Product')
            ->load($id);

        if ($product->getName() === $name) {
            return 1;
        } else {
            $product = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Catalog\Model\Product')
                ->getCollection()
                ->addFieldToFilter('name', $name);
            if (!$product->getSize()) {
                return 0;
            }

            return 1;
        }
    }

    public function getSubTotal($item)
    {
        $itemPriceRenderer = \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Magento\Weee\Block\Item\Price\Renderer');
        $itemPriceRenderer->setItem($item);
        $rowPriceExclTax = $itemPriceRenderer->getRowDisplayPriceExclTax();

        return \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Magento\Directory\Model\Currency')->formatPrecision($rowPriceExclTax, 2, [], true);
    }
}
