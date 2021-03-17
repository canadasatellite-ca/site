<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Interactivated\Pdf\Plugin\Sales\Model\Order\Pdf\Items\Invoice;

/**
 * Sales Order Invoice Pdf default items renderer
 */
class DefaultInvoice extends \Magento\Sales\Model\Order\Pdf\Items\Invoice\DefaultInvoice
{
    /**
     * Draw item line
     *
     * @return void
     */
    public function draw()
    {
        $order = $this->getOrder();
        $item = $this->getItem();
        $pdf = $this->getPdf();
        $page = $this->getPage();
        $lines = [];
        $lines[0] = [];
        // draw QTY
        $lines[0][] = ['text' => $item->getQty() * 1, 'feed' => 35];

        // draw Product name
        $lines[0][] = [
            'text' => $this->string->split($item->getName(), 75, true, true),
            'feed' => 80,
            'font' => 'bold'
        ];
        // draw SKU
        $lines[1][] = [
            'text' => $this->string->split(__("SKU: ").$this->getSku($item), 75),
            'feed' => 80,
            'align' => 'left',
//            'font' => 'bold'
        ];


        // draw item Prices
        $i = 0;
        $prices = $this->getItemPricesForDisplay();
        $feedPrice = 500;
        $feedSubtotal = $feedPrice + 60;
        foreach ($prices as $priceData) {
            /**/if (isset($priceData['label'])) {
                // draw Price label
                $lines[$i][] = ['text' => $priceData['label'], 'feed' => $feedPrice, 'align' => 'right'];
                // draw Subtotal label
                $lines[$i][] = ['text' => $priceData['label'], 'feed' => $feedSubtotal, 'align' => 'right'];
                $i++;
            }/**/
            // draw Price
            $lines[$i][] = [
                'text' => $priceData['price'],
                'feed' => $feedPrice,
                'font' => 'bold',
                'align' => 'right',
            ];
            // draw Subtotal
            $lines[$i][] = [
                'text' => $priceData['subtotal'],
                'feed' => $feedSubtotal,
                'font' => 'bold',
                'align' => 'right',
            ];
            $i++;
        }

        // draw Tax
        /*$lines[0][] = [
            'text' => $order->formatPriceTxt($item->getTaxAmount()),
            'feed' => 495,
            'font' => 'bold',
            'align' => 'right',
        ];*/

        // custom options
        $options = $this->getItemOptions();
        if ($options) {
            $optionsText = '';
            foreach ($options as $option) {
                $optionsText.= $option['label'];

                if ($option['value']) {
                    if (isset($option['print_value'])) {
                        $printValue = $option['print_value'];
                    } else {
                        $printValue = $this->filterManager->stripTags($option['value']);
                    }
                    $values = explode(', ', $printValue);
                    foreach ($values as $value) {
                        $optionsText.= " ".$value;
                    }
                }
                $optionsText.= ";";
            }
            $lines[][] = [
                'text' => $this->string->split($this->filterManager->stripTags($optionsText), 65, true, true),
                'font' => 'italic',
                'feed' => 82,
            ];

        }

        $lineBlock = ['lines' => $lines, 'height' => 20];

        $page = $pdf->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);
        $this->setPage($page);
    }
}
