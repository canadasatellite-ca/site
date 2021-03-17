<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Digit\OrderButtons\Model\Bundle\Sales\Order\Pdf\Items;

/**
 * Sales Order Invoice Pdf default items renderer
 */
class Order extends \Magento\Bundle\Model\Sales\Order\Pdf\Items\Invoice
{
    public function getChildren($item)
    {
        $itemsArray = [];

        $items = null;
        if ($item instanceof \Magento\Sales\Model\Order\Invoice\Item) {
            $items = $item->getInvoice()->getAllItems();
        } elseif ($item instanceof \Magento\Sales\Model\Order\Shipment\Item) {
            $items = $item->getShipment()->getAllItems();
        } elseif ($item instanceof \Magento\Sales\Model\Order\Creditmemo\Item) {
            $items = $item->getCreditmemo()->getAllItems();
        } elseif ($item instanceof \Magento\Sales\Model\Order\Item) {
            $itemsO = $item->getOrder()->getAllItems();

            $items = array();
            foreach($itemsO as $itemO){
                $itemO->setData('order_item',$itemO);
                $itemO->setData('order_item_id',$itemO->getItemId());
                $items[] = $itemO;
            }
        }

        if ($items) {
            foreach ($items as $value) {
                $parentItem = $value->getOrderItem()->getParentItem();
                if ($parentItem) {
                    $itemsArray[$parentItem->getId()][$value->getOrderItemId()] = $value;
                } else {
                    $itemsArray[$value->getOrderItem()->getId()][$value->getOrderItemId()] = $value;
                }
            }
        }

        if (isset($itemsArray[$item->getOrderItem()->getId()])) {
            return $itemsArray[$item->getOrderItem()->getId()];
        } else {
            return null;
        }
    }
}
