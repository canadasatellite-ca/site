<?php

namespace BroSolutions\PdfPrint\Model\Order\Pdf;

use Magento\Sales\Model\ResourceModel\Order\Invoice\Collection;

class Invoice extends \Magento\Sales\Model\Order\Pdf\Invoice
{

    /**
     * Predefined constants
     */
    const XML_PATH_SALES_PDF_INVOICE_PACKINGSLIP_ADDRESS = 'sales/identity/address';

    /**
     * Insert order to pdf page.
     *
     * @param \Zend_Pdf_Page $page
     * @param \Magento\Sales\Model\Order $obj
     * @param bool $putOrderId
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function insertOrder(&$page, $obj, $putOrderId = true)
    {
        if ($obj instanceof \Magento\Sales\Model\Order) {
            $shipment = null;
            $order = $obj;
        } elseif ($obj instanceof \Magento\Sales\Model\Order\Shipment) {
            $shipment = $obj;
            $order = $shipment->getOrder();
        }

        $this->y = $this->y ? $this->y : 815;
        $top = $this->y;

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(1));
        $page->drawRectangle(25, $top, 570, $top - 55);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->setDocHeaderCoordinates([25, $top, 570, $top - 55]);
        $this->_setFontBold($page, 14);

        if ($putOrderId) {
            $page->drawText(__('Order # ') . $order->getRealOrderId(), 400, $top += 30, 'UTF-8');
            $top +=15;
        }

        $top -=30;
        $page->drawText(
            /*__('Order Date: ') .*/
            $this->_localeDate->formatDate(
                $this->_localeDate->scopeDate(
                    $order->getStore(),
                    $order->getCreatedAt(),
                    true
                ),
                \IntlDateFormatter::MEDIUM,
                false
            ),
            400,
            $top,
            'UTF-8'
        );

        $top -= 10;
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.714, 0.012, 0.016));
        $page->setLineColor(new \Zend_Pdf_Color_Rgb(0.714, 0.012, 0.016));
        $page->setLineWidth(0.5);
        $page->drawRectangle(0, $top, 275, $top - 25);
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.5725, 0.5412, 0.5333));
        $page->setLineColor(new \Zend_Pdf_Color_Rgb(0.5725, 0.5412, 0.5333));
        $page->drawRectangle(275, $top, 595, $top - 25);

        $page->setLineColor(new \Zend_Pdf_Color_Rgb(1, 1, 1));

        /* Calculate blocks info */

        /* Billing Address */
        $billingAddress = $this->_formatAddress($this->addressRenderer->format($order->getBillingAddress(), 'pdf'));

        /* Payment */
        $paymentInfo = $this->_paymentData->getInfoBlock($order->getPayment())->setIsSecureMode(true)->toPdf();
        $paymentInfo = htmlspecialchars_decode($paymentInfo, ENT_QUOTES);
        $payment = explode('{{pdf_row_separator}}', $paymentInfo);
        foreach ($payment as $key => $value) {
            if (strip_tags(trim($value)) == '') {
                unset($payment[$key]);
            }
        }
        reset($payment);

        /* Shipping Address and Method */
        if (!$order->getIsVirtual()) {
            /* Shipping Address */
            $shippingAddress = $this->_formatAddress(
                $this->addressRenderer->format($order->getShippingAddress(), 'pdf')
            );
            $shippingMethod = $order->getShippingDescription();
        }

        $page->setFillColor(new \Zend_Pdf_Color_Rgb(1, 1, 1));
        $this->_setFontBold($page, 12);
        $page->drawText(__('Bill to'), 35, $top - 15, 'UTF-8');

        if (!$order->getIsVirtual()) {
            $page->drawText(__('Ship to'), 285, $top - 15, 'UTF-8');
        } else {
            $page->drawText(__('Payment Method:'), 285, $top - 15, 'UTF-8');
        }

        $addressesHeight = $this->_calcAddressHeight($billingAddress);
        if (isset($shippingAddress)) {
            $addressesHeight = max($addressesHeight, $this->_calcAddressHeight($shippingAddress));
        }

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
        $page->drawRectangle(25, $top - 25, 570, $top - 33 - $addressesHeight);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->_setFontRegular($page, 10);
        $this->y = $top - 40;
        $addressesStartY = $this->y;

        foreach ($billingAddress as $value) {
            if ($value !== '') {
                $text = [];
                foreach ($this->string->split($value, 45, true, true) as $_value) {
                    $text[] = $_value;
                }
                foreach ($text as $part) {
                    $page->drawText(strip_tags(ltrim($part)), 35, $this->y, 'UTF-8');
                    $this->y -= 15;
                }
            }
        }

        $addressesEndY = $this->y;

        if (!$order->getIsVirtual()) {
            $this->y = $addressesStartY;
            foreach ($shippingAddress as $value) {
                if ($value !== '') {
                    $text = [];
                    foreach ($this->string->split($value, 45, true, true) as $_value) {
                        $text[] = $_value;
                    }
                    foreach ($text as $part) {
                        $page->drawText(strip_tags(ltrim($part)), 285, $this->y, 'UTF-8');
                        $this->y -= 15;
                    }
                }
            }

            $addressesEndY = min($addressesEndY, $this->y);
            $this->y = $addressesEndY;

            $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.714, 0.012, 0.016));
            $page->setLineColor(new \Zend_Pdf_Color_Rgb(0.714, 0.012, 0.016));
            $page->setLineWidth(0.5);
            $page->drawRectangle(0, $this->y, 275, $this->y - 25);
            $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.5725, 0.5412, 0.5333));
            $page->setLineColor(new \Zend_Pdf_Color_Rgb(0.5725, 0.5412, 0.5333));
            $page->drawRectangle(275, $this->y, 595, $this->y - 25);
            $page->setLineColor(new \Zend_Pdf_Color_Rgb(1, 1, 1));
            $this->y -= 15;
            $this->_setFontBold($page, 12);
            $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
            $page->drawText(__('Payment Method:'), 35, $this->y, 'UTF-8');
            $page->drawText(__('Shipping Method:'), 285, $this->y, 'UTF-8');

            $this->y -= 10;
            $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));

            $this->_setFontRegular($page, 10);
            $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));

            $paymentLeft = 35;
            $yPayments = $this->y - 15;
        } else {
            $yPayments = $addressesStartY;
            $paymentLeft = 285;
        }

        foreach ($payment as $value) {
            if (trim($value) != '') {
                //Printing "Payment Method" lines
                $value = preg_replace('/<br[^>]*>/i', "\n", $value);
                foreach ($this->string->split($value, 45, true, true) as $_value) {
                    $page->drawText(strip_tags(trim($_value)), $paymentLeft, $yPayments, 'UTF-8');
                    $yPayments -= 15;
                }
            }
        }

        if ($order->getIsVirtual()) {
            // replacement of Shipments-Payments rectangle block
            $yPayments = min($addressesEndY, $yPayments);
            $page->drawLine(25, $top - 25, 25, $yPayments);
            $page->drawLine(570, $top - 25, 570, $yPayments);
            $page->drawLine(25, $yPayments, 570, $yPayments);

            $this->y = $yPayments - 15;
        } else {
            $topMargin = 15;
            $methodStartY = $this->y;
            $this->y -= 15;

            foreach ($this->string->split($shippingMethod, 45, true, true) as $_value) {
                $page->drawText(strip_tags(trim($_value)), 285, $this->y, 'UTF-8');
                $this->y -= 15;
            }

            $yShipments = $this->y;
            $totalShippingChargesText = "("
                . __('Total Shipping Charges')
                . " "
                . $order->formatPriceTxt($order->getShippingAmount())
                . ")";

            $page->drawText($totalShippingChargesText, 285, $yShipments - $topMargin, 'UTF-8');
            $yShipments -= $topMargin + 10;

            $tracks = [];
            if ($shipment) {
                $tracks = $shipment->getAllTracks();
            }
            if (count($tracks)) {
                $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
                $page->setLineWidth(0.5);
                $page->drawRectangle(285, $yShipments, 510, $yShipments - 10);
                $page->drawLine(400, $yShipments, 400, $yShipments - 10);
                //$page->drawLine(510, $yShipments, 510, $yShipments - 10);

                $this->_setFontRegular($page, 9);
                $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
                //$page->drawText(__('Carrier'), 290, $yShipments - 7 , 'UTF-8');
                $page->drawText(__('Title'), 290, $yShipments - 7, 'UTF-8');
                $page->drawText(__('Number'), 410, $yShipments - 7, 'UTF-8');

                $yShipments -= 20;
                $this->_setFontRegular($page, 8);
                foreach ($tracks as $track) {
                    $maxTitleLen = 45;
                    $endOfTitle = strlen($track->getTitle()) > $maxTitleLen ? '...' : '';
                    $truncatedTitle = substr($track->getTitle(), 0, $maxTitleLen) . $endOfTitle;
                    $page->drawText($truncatedTitle, 292, $yShipments, 'UTF-8');
                    $page->drawText($track->getNumber(), 410, $yShipments, 'UTF-8');
                    $yShipments -= $topMargin - 5;
                }
            } else {
                $yShipments -= $topMargin - 5;
            }

            $currentY = min($yPayments, $yShipments);

            // replacement of Shipments-Payments rectangle block
            $page->drawLine(25, $methodStartY, 25, $currentY);
            //left
            $page->drawLine(25, $currentY, 570, $currentY);
            //bottom
            $page->drawLine(570, $currentY, 570, $methodStartY);
            //right

            $this->y = $currentY;
            $this->y -= 15;
        }
    }


    /**
     * Draw header for item table
     *
     * @param \Zend_Pdf_Page $page
     * @return void
     */
    protected function _drawHeader(\Zend_Pdf_Page $page)
    {
        /* Add table head */
        $this->_setFontRegular($page, 10);
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(1, 1, 1));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $this->y, 570, $this->y - 15);
        $this->y -= 10;
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0, 0, 0));

        //columns headers
        $lines[0][] = ['text' => __('QTY'), 'feed' => 35, 'font' => 'bold'];
        $lines[0][] = ['text' => __('Item(s)'), 'feed' => 80, 'font' => 'bold'];

//        $lines[0][] = ['text' => __('SKU'), 'feed' => 290, 'align' => 'right'];

//        $lines[0][] = ['text' => __('Qty'), 'feed' => 435, 'align' => 'right'];

        $lines[0][] = ['text' => __('Each'), 'feed' => 450, 'align' => 'center', 'font' => 'bold'];

//        $lines[0][] = ['text' => __('Tax'), 'feed' => 495, 'align' => 'right'];

        $lines[0][] = ['text' => __('Subtotal'), 'feed' => 520, 'align' => 'center', 'font' => 'bold'];

        $lineBlock = ['lines' => $lines, 'height' => 5];

        $this->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->y -= 20;
    }

    /**
     * Return PDF document
     *
     * @param array|Collection $invoices
     * @return \Zend_Pdf
     */
    public function getPdf($invoices = [])
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('invoice');

        $pdf = new \Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new \Zend_Pdf_Style();
        $this->_setFontBold($style, 10);

        foreach ($invoices as $invoice) {
            if ($invoice->getStoreId()) {
                $this->_localeResolver->emulate($invoice->getStoreId());
                $this->_storeManager->setCurrentStore($invoice->getStoreId());
            }
            $page = $this->newPage();
            $order = $invoice->getOrder();
            /* Add image */
            $this->insertLogo($page, $invoice->getStore());
            /* Add address */
//            $this->insertAddress($page, $invoice->getStore());
            /* Add head */
            $this->insertOrder(
                $page,
                $order,
                $this->_scopeConfig->isSetFlag(
                    self::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $order->getStoreId()
                )
            );

            /* Add document text and number */
            $this->insertDocumentNumber($page, __('Invoice # ') . $invoice->getIncrementId());
            /* Add table */
            $headerY = $this->y;
            $this->_drawHeader($page);
            /* Add body */
            foreach ($invoice->getAllItems() as $item) {
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }
                /* Draw item */
                $this->_drawItem($item, $page, $order);
                $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
                $page->setLineWidth(0.5);
                $page->drawLine(25, $this->y+12.5, 570, $this->y+12.5);
                $page = end($pdf->pages);
            }
            $page->drawLine(25, $headerY, 25, $this->y+12.5);
            $page->drawLine(75, $headerY, 75, $this->y+12.5);
            $page->drawLine(435, $headerY, 435, $this->y+12.5);
            $page->drawLine(505, $headerY, 505, $this->y+12.5);
            $page->drawLine(570, $headerY, 570, $this->y+12.5);
            $this->_setFontBold($page, 10);
            $page->drawText(__('Registration No.: 820676757'), 25, $this->y - 20, 'UTF-8');
            /* Add totals */
            $beforeTotalsY = $this->y +12.5;
            $this->insertTotals($page, $invoice);
            $page->drawLine(435, $beforeTotalsY, 435, $this->y+12.5);
            $page->drawLine(505, $beforeTotalsY, 505, $this->y+12.5);
            $page->drawLine(570, $beforeTotalsY, 570, $this->y+12.5);
            $beforeTotalsY-= 20;
            while(($beforeTotalsY -= 15) > ($this->y+12.5)){
                $page->drawLine(435, $beforeTotalsY, 570, $beforeTotalsY);
            }
            $page->drawLine(435, $this->y+12.5, 570, $this->y+12.5);
            $page = end($pdf->pages);
            $this->insertFooter($page);

            if ($invoice->getStoreId()) {
                $this->_localeResolver->revert();
            }
        }
        $this->_afterGetPdf();
        return $pdf;
    }

    /**
     * Insert title and number for concrete document type
     *
     * @param  \Zend_Pdf_Page $page
     * @param  string $text
     * @return void
     */
    public function insertDocumentNumber(\Zend_Pdf_Page $page, $text)
    {
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->_setFontBold($page, 14);
        $docHeader = $this->getDocHeaderCoordinates();
        $page->drawText($text, 400, $docHeader[1] + 45, 'UTF-8');
    }

    /**
     * Insert address to pdf page
     *
     * @param \Zend_Pdf_Page &$page
     * @param null $store
     * @return void
     */
    protected function insertAddress(&$page, $store = null)
    {
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $font = $this->_setFontRegular($page, 10);
        $page->setLineWidth(0);
        $top = 45;

        $addr = $this->_scopeConfig->getValue(self::XML_PATH_SALES_PDF_INVOICE_PACKINGSLIP_ADDRESS) . "\n";
        $this->_setFontBold($page, 10);
        $page->drawText(
            'Canada Satellite',
            35,
            $top,
            'UTF-8'
        );

        $font = $this->_setFontRegular($page, 10);
        $top -= 10;

        foreach (explode("\n", $addr) as $value) {
            if ($value !== '') {
                $value = preg_replace('/<br[^>]*>/i', "", $value);
                foreach ($this->string->split($value, 45, true, true) as $_value) {
                    //$tst = $this->getAlignRight($_value, $rightAlign, 440, $font, 10);
                    $page->drawText(
                        trim(strip_tags($_value)),
                        35,
                        $top,
                        'UTF-8'
                    );
                    $top -= 10;
                }
            }
        }
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.71,0,0));
        $target = \Zend_Pdf_Action_URI :: create( 'https://www.canadasatellite.ca' );
//        $annotation = \Zend_Pdf_Annotation_Link :: create( 235, 45, 380, 55, $target );
//        $page->attachAnnotation($annotation);
        $this->_setFontBold($page, 11);
        $page->drawText(
            'www.canadasatellite.ca',
            400,
            45,
            'UTF-8'
        );

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->_setFontRegular($page, 10);
        $target = \Zend_Pdf_Action_URI :: create( 'mailto:sales@canadasatellite.ca' );
//        $annotation = \Zend_Pdf_Annotation_Link :: create( 235, 30, 380, 45, $target );
//        $page->attachAnnotation($annotation);
        $page->drawText(
            'sales@canadasatellite.ca',
            400,
            33,
            'UTF-8'
        );


        $lineBlock['lines'] = [
            [
                'text' => __('Toll Free: ') . '1 (855) 552-2623',
                'feed' => 555,
                'align' => 'right',
                'font_size' => 10,
                'font' => 'bold',
                'addToTop' => 0
            ],
            [
                'text' => __('Direct: ') . '1 (403) 918-6300',
                'feed' => 555,
                'align' => 'right',
                'font_size' => 10,
                'addToTop' => 10
            ],
            [
                'text' => __('Fax: ') . '1 (403) 910-0765',
                'feed' => 555,
                'align' => 'right',
                'font_size' => 10,
                'addToTop' => 20
            ],
        ];

        $this->y = 45;

foreach($lineBlock['lines'] as $item){
    $page->drawText(
        $item['text'],
        239,
        45 - $item['addToTop'],
        'UTF-8'
    );
    }

//        $page = $this->drawLineBlocks($page, [$lineBlock]);
    }

    /**
     * Insert General comment to PDF
     *
     * @param \Zend_Pdf_Page $page
     * @return void
     */
    protected function insertFooter(\Zend_Pdf_Page $page)
    {
        $text = $this->_scopeConfig->getValue(
            'cart2quote_pdf/quote/pdf_footer_text',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $text = '';
        $boxTop = 65;
        $top = $boxTop + 20;
        $boxHeight = 20;
        $boxMargin = 20;
        $font = $this->_setFontRegular($page, 10);

        if ($this->y - 20 < 15) {
            $page = $this->newPage([]);
        }

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
        foreach ($this->string->split($text, 100, true, true) as $_value) {
            $boxHeight += 10;
        }
        //$page->drawRectangle($boxMargin, $boxTop, $page->getWidth() - $boxMargin, $boxTop + $boxHeight);
        $page->setLineColor(new \Zend_Pdf_Color_Rgb(0.71,0,0));
        $page->setLineWidth(1);
        //$page->drawLine(30, $top-20, 570, $top-20);

        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.714, 0.012, 0.016));
        $page->setLineWidth(0.5);
        $page->drawRectangle(0, $top-20, 400, $top-10);
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.5725, 0.5412, 0.5333));
        $page->setLineColor(new \Zend_Pdf_Color_Rgb(0.5725, 0.5412, 0.5333));
        $page->drawRectangle(400, $top-20, 595, $top-10);


        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0.20));
        foreach ($this->string->split($text, 100, true, true) as $_value) {
            $feed = $this->getAlignCenter(
                trim(strip_tags($_value)),
                $boxMargin,
                $page->getWidth() - ($boxMargin * 2),
                $font,
                10
            );

            $page->drawText(
                trim(strip_tags($_value)),
                $feed,
                $top,
                'UTF-8'
            );

            $top -= 10;
        }


        $this->insertAddress($page);
    }


    /**
     * Set font as regular
     *
     * @param  \Zend_Pdf_Page $object
     * @param  int $size
     * @return \Zend_Pdf_Resource_Font
     */
    protected function _setFontRegular($object, $size = 7)
    {
        $font = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_HELVETICA);
        $object->setFont($font, $size);
        return $font;
    }

    /**
     * Set font as bold
     *
     * @param  \Zend_Pdf_Page $object
     * @param  int $size
     * @return \Zend_Pdf_Resource_Font
     */
    protected function _setFontBold($object, $size = 7)
    {
        $font = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_HELVETICA_BOLD);
        $object->setFont($font, $size);
        return $font;
    }

    /**
     * Set font as italic
     *
     * @param  \Zend_Pdf_Page $object
     * @param  int $size
     * @return \Zend_Pdf_Resource_Font
     */
    protected function _setFontItalic($object, $size = 7)
    {
        $font = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_HELVETICA_ITALIC);
        $object->setFont($font, $size);
        return $font;
    }

    /**
     * Insert totals to pdf page
     *
     * @param  \Zend_Pdf_Page $page
     * @param  \Magento\Sales\Model\AbstractModel $source
     * @return \Zend_Pdf_Page
     */
    protected function insertTotals($page, $source)
    {
        $order = $source->getOrder();
        $totals = $this->_getTotalsList();
        $lineBlock = ['lines' => [], 'height' => 15];
        foreach ($totals as $total) {
            $total->setOrder($order)->setSource($source);

            if ($total->canDisplay()) {
                $total->setFontSize(10);
                foreach ($total->getTotalsForDisplay() as $totalData) {
                    $lineBlock['lines'][] = [
                        [
                            'text' => $totalData['label'],
                            'feed' => 500,
                            'align' => 'right',
                            'font_size' => $totalData['font_size'],
                            'font' => 'bold',
                        ],
                        [
                            'text' => $totalData['amount'],
                            'feed' => 565,
                            'align' => 'right',
                            'font_size' => $totalData['font_size'],
                            'font' => 'bold'
                        ],
                    ];
                }
            }
        }

        $this->y -= 20;
        $page = $this->drawLineBlocks($page, [$lineBlock]);
        return $page;
    }


}


