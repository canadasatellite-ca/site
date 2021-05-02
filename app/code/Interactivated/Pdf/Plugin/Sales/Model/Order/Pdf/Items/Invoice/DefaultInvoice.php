<?php
namespace Interactivated\Pdf\Plugin\Sales\Model\Order\Pdf\Items\Invoice;
use Magento\Sales\Model\Order\Invoice\Item as II;
class DefaultInvoice extends \Magento\Sales\Model\Order\Pdf\Items\Invoice\DefaultInvoice {
    /**
     * Draw item line
     *
     * @return void
     */
    public function draw()
    {
        $order = $this->getOrder();
        $item = $this->getItem(); /** @var II $item */
        $pdf = $this->getPdf();
        $page = $this->getPage();
        $lines = [];
        $lines[0] = [];

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
        $lines[1][] = [
			# 2021-05-02 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
			# «Call to a member function getQuoteDescription() on null
			# in app/code/Interactivated/Pdf/Plugin/Sales/Model/Order/Pdf/Items/Invoice/DefaultInvoice.php:45»:
			# https://github.com/canadasatellite-ca/site/issues/100
            'text' => $this->string->split($item->getOrderItem()->getProduct()->getQuoteDescription(), 75),
            'feed' => 65,
            'align' => 'left',
            'width' => 70,
            'font_size' => 10,
            'height' => 12
        ];

        $lines[2][] = [
            'text' => '',
            'feed' => 65,
            'width' => 70,
            'height' => 10
        ];

        // draw SKU
        $lines[3][] = [
            'text' => $this->string->split(__("SKU: ") . $this->getSku($item), 75),
            'feed' => 65,
            'align' => 'left',
            'width' => 70
        ];


        // draw item Prices
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

        // draw Tax
        /*$lines[0][] = [
            'text' => $order->formatPriceTxt($item->getTaxAmount()),
            'feed' => 495,
            'font' => 'bold',
            'align' => 'right',
        ];*/

        $lineBlock = ['lines' => $lines, 'height' => 20];

        $page = $pdf->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);

        // custom options
        $options = $this->getItemOptions();
        if ($options) {
            $optionsText = '';
            $optionsLabels = [];
            foreach ($options as $ket => $option) {
                $optionsText .= $option['label'];
                $optionsLabels[] = $option['label'] . ':';
                if ($option['value']) {
                    if (isset($option['print_value'])) {
                        $optionsLabels[] = $option['print_value'];
                    } else {
                        $optionsLabels = $this->filterManager->stripTags($option['value']);
                    }
//                    $values = explode(', ', $printValue);
//                    foreach ($values as $value) {
//                        $optionsText .= " " . $value;
//                    }
                }
                $optionsText .= ";";
            }
            $optionsLines = [];
            foreach ($optionsLabels as $key => $optionsLabel) {
                if ($key % 2 == 0) {
                    $optionsLines[][] = [
                        'text' => $this->string->split(
                            $this->filterManager->stripTags($optionsLabel),
                            65,
                            true,
                            true
                        ),
                        'font' => 'bold',
                        'feed' => 65,
                        'font_size' => 10
                    ];
                    continue;
                }

                $optionsLines[][] = [
                    'text' => $this->string->split(
                        $this->filterManager->stripTags($optionsLabel),
                        65,
                        true,
                        true
                    ),
                    'feed' => 85,
                    'font_size' => 10
                ];
            }

            if (count($optionsLines) > 0) {
                $optionsLineBlock = ['lines' => $optionsLines, 'height' => 15];
                $page = $pdf->drawLineBlocks($page, [$optionsLineBlock], ['table_header' => true]);
            }
        }

        $this->setPage($page);
    }
}
