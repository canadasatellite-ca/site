<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Interactivated\Pdf\Plugin\Sales\Model\Order\Pdf\Items\Shipment;

/**
 * Sales Order Shipment Pdf default items renderer
 */
class DefaultShipment extends \Magento\Sales\Model\Order\Pdf\Items\Shipment\DefaultShipment
{
    /**
     * Draw item line
     *
     * @return void
     */
    public function draw()
    {
        $item = $this->getItem();
        $pdf = $this->getPdf();
        $page = $this->getPage();
        $lines = [];


        // draw Product name
        $nameField = $this->string->split($item->getName(), 50, true, true);

        $lines[0] = [['text' => $nameField, 'feed' => 80]];

        // draw QTY
        $lines[0][] = ['text' => $item->getQty() * 1, 'feed' => 35];

        // draw SKU
        $lines[0][] = [
            'text' => $this->string->split($this->getSku($item), 35),
            'feed' => 565,
            'align' => 'right',
        ];

        // Custom options
        $options = $this->getItemOptions();
        if ($options) {
            foreach ($options as $option) {
                // draw options label
                $lines[][] = [
                    'text' => $this->string->split($this->filterManager->stripTags($option['label']), 70, true, true),
                    'font' => 'italic',
                    'feed' => 110,
                ];

                // draw options value
                if ($option['value']) {
                    $printValue = isset(
                        $option['print_value']
                    ) ? $option['print_value'] : $this->filterManager->stripTags(
                        $option['value']
                    );
                    $values = explode(', ', $printValue);
                    foreach ($values as $value) {
                        $lines[][] = ['text' => $this->string->split($value, 50, true, true), 'feed' => 115];
                    }
                }
            }
        }
        $product = $item->getOrderItem()->getProduct();
        $shelf = [];
        if($product){
            $attr__shelf = $product->getAttributeText('shelf_id');
            if($attr__shelf){
                $shelf[]= $attr__shelf;
            }
            /*$attr__shelf__location = $product->getAttributeText('shelf_location');
            if($attr__shelf__location){
                $shelf[]= $attr__shelf__location;
            }*/
            /*$attr__shelf_bid__location = $product->getAttributeText('bin_location');
            if($attr__shelf_bid__location){
                $shelf[]= $attr__shelf_bid__location;
            }*/
            if(count($shelf)){
                $lines[][] = [
                    'text' => $this->string->split($this->filterManager->stripTags(implode('; ',$shelf)), 70, true, true),
                    'feed' => 100,
                ];
            }
        }

        $lineBlock = ['lines' => $lines, 'height' => 20];

        $page = $pdf->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);
        $this->setPage($page);
    }
}
