<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace MageSuper\Casat\Plugin\Sales\Model\Order;

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
    function aroundGetColumnHtml(
        \Magento\Sales\Block\Adminhtml\Items\AbstractItems $items,
        \Closure $work,
        \Magento\Framework\DataObject $item,
        $column,
        $field = null
    ) {
        switch ($column) {
            case 'profit':
                $profit = (float)$item->getData('profit');
                return $items->displayPrices($profit,$profit);
            case 'margin':
                $margin = (float)$item->getData('margin');
                $margin = round($margin,2).'%';
                return $margin;
        };
        return $work($item, $column, $field);
    }

}
