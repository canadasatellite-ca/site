<?php

namespace Interactivated\Pdf\Plugin\Sales\Model\Order\Pdf;

use Magento\MediaStorage\Helper\File\Storage\Database;
use Magento\Sales\Model\Order\Pdf\Config;

class Order extends \Digit\OrderButtons\Model\Pdf\Order
{
    private $fileStorageDatabase;

    public function __construct(
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Filesystem $filesystem,
        Config $pdfConfig,
        \Magento\Sales\Model\Order\Pdf\Total\Factory $pdfTotalFactory,
        \Magento\Sales\Model\Order\Pdf\ItemsFactory $pdfItemsFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageDatabase = null,
        array $data = []
    ) {
        $this->fileStorageDatabase = $fileStorageDatabase ?:
            \Magento\Framework\App\ObjectManager::getInstance()->get(Database::class);
        parent::__construct($paymentData, $string, $scopeConfig, $filesystem, $pdfConfig, $pdfTotalFactory, $pdfItemsFactory, $localeDate, $inlineTranslation, $addressRenderer, $storeManager, $localeResolver, $data);
    }

    /**
     * Return PDF document
     *
     * @param  \Magento\Sales\Model\Order[] $orders
     *
     * @return \Zend_Pdf
     */
    public function getPdf($orders = [])
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('order');

        $pdf = new \Zend_Pdf();
        $this->_setPdf($pdf);

        foreach ($orders as $order) {
            if ($order->getStoreId()) {
                $this->_localeResolver->emulate($order->getStoreId());
                $this->_storeManager->setCurrentStore($order->getStoreId());
            }
            $page = $this->newPage();
            $this->_setFontBold($page, 10);
            $order->setOrder($order);
            /* Add image */
            $this->insertLogo($page, $order->getStore());
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
            $headerY = $this->y;
            /* Add table */
            $this->_drawHeader($page);
            /* Add body */
            foreach ($order->getAllVisibleItems() as $item) {
                if ($item->getParentItem()) {
                    continue;
                }

                /* Keep it compatible with the invoice */
                $item->setQty($item->getQtyOrdered());
                $item->setOrderItem($item);

                /* Draw item */
                $this->_drawItem($item, $page, $order);
                $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
                $page->setLineWidth(0.5);
                $page->drawLine(5, $this->y + 12.5, 590, $this->y + 12.5);
                $page = end($pdf->pages);
            }
            $page->drawLine(5, $headerY, 5, $this->y + 12.5);
            $page->drawLine(60, $headerY, 60, $this->y + 12.5);
            $page->drawLine(430, $headerY, 430, $this->y + 12.5);
            $page->drawLine(500, $headerY, 500, $this->y + 12.5);
            $page->drawLine(590, $headerY, 590, $this->y + 12.5);
            $this->_setFontBold($page, 10);
            $page->drawText(__('GST / HST Registration #775933914'), 5, $this->y - 5, 'UTF-8');
            /* Add totals */
            $beforeTotalsY = $this->y + 12.5;
            $this->insertTotals($page, $order);
            $page->drawLine(430, $beforeTotalsY, 430, $this->y + 12.5);
            $page->drawLine(500, $beforeTotalsY, 500, $this->y + 12.5);
            $page->drawLine(590, $beforeTotalsY, 590, $this->y + 12.5);
            $beforeTotalsY -= 10;
            while (($beforeTotalsY -= 20) > ($this->y + 12.5)) {
                $page->drawLine(430, $beforeTotalsY, 590, $beforeTotalsY);
            }
            $page->drawLine(430, $this->y + 12.5, 590, $this->y + 12.5);
            $page = end($pdf->pages);
            $this->insertFooter($page);
            if ($order->getStoreId()) {
                $this->_localeResolver->revert();
            }
        }
        $this->_afterGetPdf();
        return $pdf;
    }

    /**
     * Insert order to pdf page
     *
     * @param \Zend_Pdf_Page &$page
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
        $page->drawRectangle(25, $top, 590, $top - 55);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->setDocHeaderCoordinates([25, $top, 590, $top - 55]);
        $this->y += 35;
//        $top += 20;

        $headerLines[0][0] = [
            'text' => __('Order - '),
            'feed' => 490,
            'align' => 'right',
//            'font' => 'italic',
            'width' => 50,
            'font_size' => 16
        ];

        $headerLines[0][1] = [
            'text' => $order->getRealOrderId(),
            'feed' => 535,
            'align' => 'left',
            'font' => 'bold',
            'width' => 50,
            'font_size' => 16
        ];

        $headerLines[1][] = [
            'text' =>$this->_localeDate->formatDate(
                $this->_localeDate->scopeDate(
                    $order->getStore(),
                    $order->getCreatedAt(),
                    true
                ),
                \IntlDateFormatter::LONG,
                false
            ),
            'feed' => 543,
            'align' => 'right',
//            'font' => 'italic',
            'width' => 50,
            'font_size' => 14
        ];

        $lineBlock1 = [
            'lines' => $headerLines,
            'height' => 15
        ];

        $this->drawLineBlocks($page, [$lineBlock1], ['table_header' => true]);

//        $top -= 30;

        $history = $order->getAllStatusHistory();
        $historys = [];
        foreach ($history as $history_item) {
            if ($history_item->getData('is_show_in_pdf')) {
                $historys[] = $history_item->getComment();
            }
        }
        if (count($historys)) {
            $page->setLineColor(new \Zend_Pdf_Color_Rgb(0.5725, 0.5412, 0.5333));
            $page->setLineWidth(0.3);
            $top += 15;
            $page->drawLine(5, $top, 590, $top);

            $top -= 30;

            $top += 15;
            $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
            $this->_setFontBold($page, 14);
            $page->drawText(__('Order Comments'), 5, $top -10, 'UTF-8');
            $top -= 35;
            $this->_setFontRegular($page, 10);

            foreach ($historys as $history_item) {
                $text = [];
                foreach ($this->string->split($history_item, 110, true, true) as $_value) {
                    $text[] = $_value;
                }
                foreach ($text as $part) {
                    $page->drawText(strip_tags(ltrim($part)), 5, $top, 'UTF-8');
                    $top -= 10;
                }
                $top -= 10;
            }
        }

        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.714, 0.012, 0.016));
        $page->setLineColor(new \Zend_Pdf_Color_Rgb(0.714, 0.012, 0.016));
        $page->setLineWidth(0.5);
        $page->drawRectangle(0, $top, 275, $top - 23);
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.5725, 0.5412, 0.5333));
        $page->setLineColor(new \Zend_Pdf_Color_Rgb(0.5725, 0.5412, 0.5333));
        $page->drawRectangle(275, $top, 595, $top - 23);

        $page->setLineColor(new \Zend_Pdf_Color_Rgb(1, 1, 1));

        /* Billing Address */
        $billingAddress = $this->_formatAddress(
            $this->addressRenderer->format(
                $order->getBillingAddress(),
                'pdf'
            )
        );

        /* Payment */
        $paymentInfo = $this->_paymentData->getInfoBlock($order->getPayment())
            ->setIsSecureMode(true)
            ->toPdf();
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
                $this->addressRenderer->format(
                    $order->getShippingAddress(),
                    'pdf'
                )
            );
            $shippingMethod = $order->getShippingDescription();
        }

        $page->setFillColor(new \Zend_Pdf_Color_Rgb(1, 1, 1));
        $this->_setFontBold($page, 14);
        $page->drawText(
            __('Bill to'),
            10,
            $top - 15,
            'UTF-8'
        );

        if (!$order->getIsVirtual()) {
            $page->drawText(
                __('Ship to'),
                285,
                $top - 15,
                'UTF-8'
            );
        } else {
            $page->drawText(
                __('Payment Method'),
                310,
                $top - 15,
                'UTF-8'
            );
        }

        $addressesHeight = $this->_calcAddressHeight($billingAddress);
        if (isset($shippingAddress)) {
            $addressesHeight = max($addressesHeight, $this->_calcAddressHeight($shippingAddress));
        }

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
        $page->drawRectangle(25, $top - 25, 590, $top - 33 - $addressesHeight);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->_setFontRegular($page, 10);
        $this->y = $top - 50;
        $addressesStartY = $this->y;

        foreach ($billingAddress as $value) {
            if ($value !== '') {
                $text = [];
                foreach ($this->string->split($value, 45, true, true) as $_value) {
                    $text[] = $_value;
                }
                foreach ($text as $part) {
                    $page->drawText(strip_tags(ltrim(str_replace('T:', '', $part))), 5, $this->y, 'UTF-8');
                    $this->y -= 12;
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
                        $page->drawText(
                            strip_tags(ltrim(str_replace('T:', '', $part))),
                            280,
                            $this->y,
                            'UTF-8'
                        );
                        $this->y -= 12;
                    }
                }
            }

            $addressesEndY = min($addressesEndY, $this->y);
            $this->y = $addressesEndY - 10;

            $page->setLineColor(new \Zend_Pdf_Color_Rgb(0, 0, 0));
            $page->setLineWidth(0.2);
            $page->drawLine(5, $this->y, 135, $this->y);
            $page->drawLine(280, $this->y, 410, $this->y);
            $this->y -= 10;

            $page->setLineColor(new \Zend_Pdf_Color_Rgb(255, 255, 255));
            $this->y -= 10;

            $this->_setFontBold($page, 14);
            $page->drawText(
                __('Payment Method'),
                5,
                $this->y,
                'UTF-8'
            );
            $page->drawText(
                __('Shipping Method'),
                280,
                $this->y,
                'UTF-8'
            );
            $this->y -= 10;

            $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
            $this->_setFontRegular($page, 10);
            $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));

            $paymentLeft = 5;
            $yPayments = $this->y - 15;
        } else {
            $yPayments = $addressesStartY;
            $paymentLeft = 280;
        }

        foreach ($payment as $value) {
            if (trim($value) != '') {
                //Printing "Payment Method" lines
                $value = preg_replace('/<br[^>]*>/i', "\n", $value);
                foreach ($this->string->split($value, 45, true, true) as $_value) {
                    $page->drawText(strip_tags(trim($_value)), $paymentLeft, $yPayments, 'UTF-8');
                    $yPayments -= 12;
                }
            }
        }

        if ($order->getIsVirtual()) {
            // replacement of Shipments-Payments rectangle block
            $yPayments = min($addressesEndY, $yPayments);
            $page->drawLine(25, $top - 25, 25, $yPayments);
            $page->drawLine(590, $top - 25, 590, $yPayments);
            $page->drawLine(25, $yPayments, 590, $yPayments);

            $this->y = $yPayments - 12;
        } else {
            $topMargin = 15;
            $methodStartY = $this->y;
            $this->y -= 15;

            foreach ($this->string->split($shippingMethod, 45, true, true) as $_value) {
                $page->drawText(strip_tags(trim($_value)), 280, $this->y, 'UTF-8');
                $this->y -= 12;
            }

            $yShipments = $this->y + 15;
            $totalShippingChargesText = "(" . __(
                    'Total Shipping Charges'
                ) . " " . $order->formatPriceTxt(
                    $order->getShippingAmount()
                ) . ")";

            $page->drawText($totalShippingChargesText, 280, $yShipments - $topMargin, 'UTF-8');
            $yShipments -= $topMargin + 10;

            $tracks = [];
            if ($shipment) {
                $tracks = $shipment->getAllTracks();
            }
            if (count($tracks)) {
                $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
                $page->setLineWidth(0.5);
                $page->drawRectangle(280, $yShipments, 510, $yShipments - 10);
                $page->drawLine(400, $yShipments, 400, $yShipments - 10);
                //$page->drawLine(510, $yShipments, 510, $yShipments - 10);

                $this->_setFontRegular($page, 9);
                $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
                //$page->drawText(__('Carrier'), 290, $yShipments - 7 , 'UTF-8');
                $page->drawText(
                    __('Title'),
                    290,
                    $yShipments - 7,
                    'UTF-8'
                );
                $page->drawText(
                    __('Number'),
                    410,
                    $yShipments - 7,
                    'UTF-8'
                );

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
            $page->drawLine(25, $currentY, 590, $currentY);
            //bottom
            $page->drawLine(590, $currentY, 590, $methodStartY);
            //right

            $this->y = $currentY;
            $this->y -= 15;
        }
    }

    protected function _drawHeader(\Zend_Pdf_Page $page)
    {
        /* Add table head */
        $this->_setFontRegular($page, 12);
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(1, 1, 1));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(5, $this->y, 590, $this->y - 25);
        $this->y -= 15;
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0, 0, 0));

        //columns headers
        $lines[0][] = [
            'text' => __('Quantity'),
            'feed' => 5,
            'align' => 'center',
            'font' => 'bold',
            'width' => 55,
            'font_size' => 12
        ];

        $lines[0][] = [
            'text' => __('Product'),
            'feed' => 45,
            'align' => 'center',
            'font' => 'bold',
            'width' => 340,
            'font_size' => 12
        ];

        $lines[0][] = [
            'text' => __('Each'),
            'feed' => 430,
            'align' => 'center',
            'font' => 'bold',
            'width' => 70,
            'font_size' => 12
        ];

        $lines[0][] = [
            'text' => __('Extended Cost'),
            'feed' => 520,
            'align' => 'center',
            'font' => 'bold',
            'width' => 50,
            'font_size' => 12
        ];

        $lineBlock = [
            'lines' => $lines,
            'height' => 5
        ];

        $this->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->y -= 20;
    }

    protected function insertTotals($page, $source)
    {
        $this->y += 10;
        $order = $source->getOrder();
        $totals = $this->_getTotalsList();
        $lineBlock = ['lines' => [], 'height' => 20];
        foreach ($totals as $total) {
            $total->setOrder($order)->setSource($source);

            if ($total->canDisplay()) {
                $total->setFontSize(10);
                foreach ($total->getTotalsForDisplay() as $totalData) {
                    if ($totalData['label'] == 'Shipping & Handling:') {
                        $totalData['label'] = 'Shipping Cost';
                    }

                    $lineBlock['lines'][] = [
                        [
                            'text' => str_replace(':', '', $totalData['label']),
                            'feed' => 430,
                            'align' => 'center',
                            'font_size' => $totalData['font_size'],
                            'font' => 'bold',
                            'width' => 70
                        ],
                        [
                            'text' => $totalData['amount'],
                            'feed' => 520,
                            'align' => 'center',
                            'font_size' => $totalData['font_size'],
                            'font' => 'bold',
                            'width' => 50
                        ],
                    ];
                }
            }
        }

        $this->y -= 20;
        $page = $this->drawLineBlocks($page, [$lineBlock]);
        return $page;
    }

    protected function insertAddress(&$page, $store = null)
    {
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $page->setLineWidth(0);
        $top = 45;

        $addr = $this->_scopeConfig->getValue(self::XML_PATH_SALES_PDF_INVOICE_PACKINGSLIP_ADDRESS) . "\n";
        $addr = str_replace("\nCanada", ' Canada', $addr);
        $this->_setFontBold($page, 10);
        $page->drawText(
            'Canada Satellite',
            5,
            $top,
            'UTF-8'
        );

        $top -= 10;

        $this->_setFontRegular($page, 10);
        foreach (explode("\n", $addr) as $value) {
            if ($value !== '') {
                $value = preg_replace('/<br[^>]*>/i', "", $value);
                foreach ($this->string->split($value, 55, true, true) as $_value) {
                    $page->drawText(
                        trim(strip_tags($_value)),
                        5,
                        $top,
                        'UTF-8'
                    );
                    $top -= 10;
                }
            }
        }

        /**
         * draw right block
         */
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->_setFontBold($page, 10);
        $page->drawText(
            'www.canadasatellite.ca',
            450,
            35,
            'UTF-8'
        );

        $page->drawText(
            $this->_scopeConfig->getValue('trans_email/ident_sales/email'),
            450,
            25,
            'UTF-8'
        );

        /**
         * draw middle block
         */
        $this->_setFontBold($page, 10);

        $page->drawText(
            'Toll Free:',
            239,
            35,
            'UTF-8'
        );

        $page->drawText(
            'Direct:',
            239,
            25,
            'UTF-8'
        );


        $this->_setFontRegular($page, 10);
        $page->drawText(
            '1 (403) 918-6300',
            275,
            25,
            'UTF-8'
        );
        $page->drawText(
            '1 (855) 552-2623',
            290,
            35,
            'UTF-8'
        );

        $this->y = 45;
    }

    protected function insertLogo(&$page, $store = null)
    {
        $this->y = $this->y ? $this->y : 815;
        $image = $this->_scopeConfig->getValue(
            'sales/identity/logo',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        if ($image) {
            $imagePath = '/sales/store/logo/' . $image;
            if ($this->fileStorageDatabase->checkDbUsage() &&
                !$this->_mediaDirectory->isFile($imagePath)
            ) {
                $this->fileStorageDatabase->saveFileToFilesystem($imagePath);
            }
            if ($this->_mediaDirectory->isFile($imagePath)) {
                $image = \Zend_Pdf_Image::imageWithPath($this->_mediaDirectory->getAbsolutePath($imagePath));
                $top = 830;
                //top border of the page
                $widthLimit = 270;
                //half of the page width
                $heightLimit = 270;
                //assuming the image is not a "skyscraper"
                $width = 120;
                $height = 50;

                //preserving aspect ratio (proportions)
                $ratio = $width / $height;
                if ($ratio > 1 && $width > $widthLimit) {
                    $width = $widthLimit;
                    $height = $width / $ratio;
                } elseif ($ratio < 1 && $height > $heightLimit) {
                    $height = $heightLimit;
                    $width = $height * $ratio;
                } elseif ($ratio == 1 && $height > $heightLimit) {
                    $height = $heightLimit;
                    $width = $widthLimit;
                }

                $y1 = $top - $height;
                $y2 = $top;
                $x1 = 5;
                $x2 = $x1 + $width;

                //coordinates after transformation are rounded by Zend
                $page->drawImage($image, $x1, $y1, $x2, $y2);

                $this->y = $y1 - 20;
            }
        }
    }
}
