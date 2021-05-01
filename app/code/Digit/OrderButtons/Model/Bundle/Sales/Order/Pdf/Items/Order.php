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

    public function draw()
    {
        $order = $this->getOrder();
        $item = $this->getItem();
        $pdf = $this->getPdf();
        $page = $this->getPage();

        $this->_setFontRegular();
        $items = $this->getChildren($item);

        $prevOptionId = '';
        $drawItems = [];

        /*
        foreach ($items as $childItem) {

            $line = [];

            $attributes = $this->getSelectionAttributes($childItem);
            if (is_array($attributes)) {
                $optionId = $attributes['option_id'];
            } else {
                $optionId = 0;
            }

            if (!isset($drawItems[$optionId])) {
                $drawItems[$optionId] = ['lines' => [], 'height' => 15];
            }

            if ($childItem->getOrderItem()->getParentItem()) {
                if ($prevOptionId != $attributes['option_id']) {
                    $line[0] = [
                        'font' => 'italic',
                        'text' => $this->string->split($attributes['option_label'], 45, true, true),
                        'feed' => 35,
                    ];

                    $drawItems[$optionId] = ['lines' => [$line], 'height' => 15];

                    $line = [];
                    $prevOptionId = $attributes['option_id'];
                }
            }
        */

            /* in case Product name is longer than 80 chars - it is written in a few lines */
        /*
            if ($childItem->getOrderItem()->getParentItem()) {
                $feed = 40;
                $name = $this->getValueHtml($childItem);
            } else {
                $feed = 35;
                $name = $childItem->getName();
            }
            $line[] = ['text' => $this->string->split($name, 35, true, true), 'feed' => $feed];

            // draw SKUs
            if (!$childItem->getOrderItem()->getParentItem()) {
                $text = [];
                foreach ($this->string->split($item->getSku(), 17) as $part) {
                    $text[] = $part;
                }
                $line[] = ['text' => $text, 'feed' => 255];
            }

            // draw prices
            if ($this->canShowPriceInfo($childItem)) {
                $price = $order->formatPriceTxt($childItem->getPrice());
                $line[] = ['text' => $price, 'feed' => 395, 'font' => 'bold', 'align' => 'right'];
                $line[] = ['text' => $childItem->getQty() * 1, 'feed' => 435, 'font' => 'bold'];

                $tax = $order->formatPriceTxt($childItem->getTaxAmount());
                $line[] = ['text' => $tax, 'feed' => 495, 'font' => 'bold', 'align' => 'right'];

                $row_total = $order->formatPriceTxt($childItem->getRowTotal());
                $line[] = ['text' => $row_total, 'feed' => 565, 'font' => 'bold', 'align' => 'right'];
            }

            $drawItems[$optionId]['lines'][] = $line;
        }
        */

        $lines = [];

        // draw QTY
        $lines[0][] = [
            'text' => $item->getQty() * 1,
            'feed' => 5,
            'align' => 'center',
            'font' => 'bold',
            'width' => 50
        ];

        // draw Product name
        $lines[0][] = [
            'text' => $this->string->split($item->getName(), 65, true, true),
            'align' => 'left',
            'feed' => 65,
            'font' => 'bold',
            'width' => 340
        ];

        // draw SKU
        $lines[1][] = [
            'text' => $this->string->split(__("SKU: ") . $this->getSku($item), 65),
            'feed' => 65,
            'align' => 'left',
            'width' => 70
        ];

        $i = 0;
        $prices = $this->getItemPricesForDisplay();
        $feedPrice = 430;
        $feedSubtotal = $feedPrice + 70;
        foreach ($prices as $priceData) {
            if (isset($priceData['label'])) {

                // draw Unit Price label
                $lines[$i][] = [
                    'text' => $priceData['label'],
                    'feed' => $feedPrice,
                    'align' => 'center',
                    'width' => 70
                ];

                // draw Extended Cost label
                $lines[$i][] = [
                    'text' => $priceData['label'],
                    'feed' => $feedSubtotal,
                    'align' => 'center',
                    'width' => 85
                ];
                $i++;
            }

            // draw Unit Price
            $lines[$i][] = [
                'text' => $priceData['price'],
                'feed' => $feedPrice,
                'font' => 'bold',
                'align' => 'center',
                'width' => 70
            ];

            // draw Extended Cost
            $lines[$i][] = [
                'text' => $priceData['subtotal'],
                'feed' => $feedSubtotal,
                'font' => 'bold',
                'align' => 'center',
                'width' => 85
            ];
            $i++;
        }

        $lineBlock = ['lines' => $lines, 'height' => 20];
        $page = $pdf->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);

        $this->setPage($page);
    }
}
