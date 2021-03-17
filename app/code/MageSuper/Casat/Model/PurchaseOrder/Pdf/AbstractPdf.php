<?php
/**
 * Cart2Quote
 */
namespace  MageSuper\Casat\Model\PurchaseOrder\Pdf;

/**
 * Quotation PDF abstract model
 */
abstract class AbstractPdf extends \Magento\Sales\Model\Order\Pdf\AbstractPdf
{
    protected $om;
    /**
     * Predefined constants
     */
    const XML_PATH_SALES_PDF_INVOICE_PACKINGSLIP_ADDRESS = 'sales/identity/address';

    /**
     * @var \Magestore\PurchaseOrderSuccess\Block\Adminhtml\Email\EmailToSupplier\Header
     */
    public $headerblock;

    /**
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Sales\Model\Order\Pdf\Config $pdfConfig
     * @param \Magento\Sales\Model\Order\Pdf\Total\Factory $pdfTotalFactory
     * @param \Magento\Sales\Model\Order\Pdf\ItemsFactory $pdfItemsFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Cart2Quote\Quotation\Model\Quote\Address\Renderer $addressRenderer
     * @param Items\QuoteItem $renderer
     * @param array $data
     */
    public function __construct(
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Sales\Model\Order\Pdf\Config $pdfConfig,
        \Magento\Sales\Model\Order\Pdf\Total\Factory $pdfTotalFactory,
        \Magento\Sales\Model\Order\Pdf\ItemsFactory $pdfItemsFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Cart2Quote\Quotation\Model\Quote\Address\Renderer $addressRenderer,
        \MageSuper\Casat\Model\PurchaseOrder\Pdf\Items\QuoteItem $renderer,
        \Magento\Framework\ObjectManagerInterface $om,
        array $data = []
    ) {
        $this->addressRenderer = $addressRenderer;
        $this->_paymentData = $paymentData;
        $this->_localeDate = $localeDate;
        $this->string = $string;
        $this->_scopeConfig = $scopeConfig;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->_rootDirectory = $filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::ROOT);
        $this->_pdfConfig = $pdfConfig;
        $this->_pdfTotalFactory = $pdfTotalFactory;
        $this->_pdfItemsFactory = $pdfItemsFactory;
        $this->inlineTranslation = $inlineTranslation;
        $this->_renderer = $renderer;
        $this->om = $om;
        parent::__construct(
            $paymentData,
            $string,
            $scopeConfig,
            $filesystem,
            $pdfConfig,
            $pdfTotalFactory,
            $pdfItemsFactory,
            $localeDate,
            $inlineTranslation,
            $addressRenderer,
            $data
        );
    }

    /**
     * get StringUtils Object
     * @return \Magento\Framework\Stdlib\StringUtils
     */
    public function getStringUtils()
    {
        return $this->string;
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
        $annotation = \Zend_Pdf_Annotation_Link :: create( 235, 45, 380, 55, $target );
        $page->attachAnnotation($annotation);
        $this->_setFontBold($page, 11);
        $page->drawText(
            'www.canadasatellite.ca',
            235,
            45,
            'UTF-8'
        );

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->_setFontRegular($page, 10);
        $target = \Zend_Pdf_Action_URI :: create( 'mailto:sales@canadasatellite.ca' );
        $annotation = \Zend_Pdf_Annotation_Link :: create( 235, 30, 380, 45, $target );
        $page->attachAnnotation($annotation);
        $page->drawText(
            'sales@canadasatellite.ca',
            239,
            33,
            'UTF-8'
        );


        $lineBlock['lines'][] = [
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
        $page = $this->drawLineBlocks($page, [$lineBlock]);
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

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
        foreach ($this->string->split($text, 100, true, true) as $_value) {
            $boxHeight += 10;
        }
        //$page->drawRectangle($boxMargin, $boxTop, $page->getWidth() - $boxMargin, $boxTop + $boxHeight);
        $page->setLineColor(new \Zend_Pdf_Color_Rgb(0.71,0,0));
        $page->setLineWidth(1);
        $page->drawLine(30, $top-20, 570, $top-20);

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
     * Insert quote comment to PDF
     *
     * @param \Zend_Pdf_Page $page
     * @param $quote
     * @return \Zend_Pdf_Page
     * @throws \Zend_Pdf_Exception
     */
    protected function insertComments(\Zend_Pdf_Page $page, $quote)
    {
        //Add title
        $comments = array_merge(["Comment with quote: "], explode("\n", $quote->getCustomerNote()));
        $lines = [];
        foreach ($comments as $value) {
            if (!empty($value)) {
                //Replace html breaks with empty strings
                $value = preg_replace('/<br[^>]*>/i', "", $value);
                //Split the string for specified length
                foreach ($this->string->split($value, 70, true, true) as $part) {
                    $lines[] = $part;
                }
            }
        }
        
        $fontSize = 10;
        $lineCount = count($lines);
        $lineHeight = $fontSize + 2;
        $margin = 10;
        $feed = 35;
        $top = $this->y - $margin;
        $left = $feed - $margin;
        $right = 370;

        $bottom = ($top - ($lineHeight * $lineCount)) - $margin;

        //Draw comment box
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0.90));
        $page->drawRectangle($left, $top, $right, $bottom);

        //Draw comments
        $this->_setFontBold($page, $fontSize);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $nextLine = $top;
        foreach ($lines as $line) {
            $page->drawText(trim(strip_tags($line)), $feed, $nextLine -= $lineHeight, 'UTF-8');
            $this->_setFontRegular($page, $fontSize);
        }

        $this->y -= 20;

        return $page;
    }

    /**
     * Insert Quote to pdf page
     *
     * @param \Zend_Pdf_Page &$page
     * @param \Magento\Sales\Model\Order $obj
     * @param bool $putQuoteId
     * @return void
     */
    protected function insertQuote(&$page, $obj, $putQuoteId = true)
    {
        if ($obj instanceof \Magestore\PurchaseOrderSuccess\Model\PurchaseOrder) {
            $shipment = null;
            $quote = $obj;
        }

        $block = $this->om->create('\Magestore\PurchaseOrderSuccess\Block\Adminhtml\Email\EmailToSupplier\Header');
        $this->headerblock = $block;


        $this->y = $this->y ? $this->y : 815;
        $top = $this->y;

        $top1 = 815;

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(1));
        //$page->drawRectangle(25, $top, 570, $top - 75);
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.71,0,0));
        $this->setDocHeaderCoordinates([25, $top, 570, $top - 75]);
        $font = $this->_setFontRegular($page, 13);

        $rightAlign = 130;

        if ($putQuoteId) {
            $txt = __('Purchase Order # ') . $quote->getPurchaseCode();
            $tst = $this->getAlignRight($txt, $rightAlign, 440, $font, 13);
            $page->drawText(
                trim(strip_tags($txt)),
                $tst,
                $top1 -= 15,
                'UTF-8'
            );
        }
        $font = $this->_setFontRegular($page, 10);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0.25));
        $txt = $this->headerblock->getPurchaseDate();
        $tst = $this->getAlignRight($txt, $rightAlign, 440, $font, 10);
        $page->drawText(
            trim(strip_tags($txt)),
            $tst,
            $top1 -= 15,
            'UTF-8'
        );

        $top -= 10;
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        //$page->drawRectangle(25, $top, 275, $top - 25);
        //$page->drawRectangle(275, $top, 570, $top - 25);

        /* Calculate blocks info */

        /* Billing Address */
        /** @var \Magestore\PurchaseOrderSuccess\Block\Adminhtml\Email\EmailToSupplier\Header $block */


        $billingAddress = array(
            $block->getDataHtml('supplier_name'),
            $block->getDataHtml('street'),
            $block->getCityRegionPostCode(),
            $block->getCountry(),
            $block->getSupplierData('telephone'),
            $block->getDataHtml('contact_email')
        );

        /* Payment */
        /*if ($quote->getPayment()->getMethod()) {
            $paymentBlock = $this->_paymentData->getInfoBlock($quote->getPayment());
            $paymentBlock->addChild('payment_instructions', 'Cart2Quote\Quotation\Block\Adminhtml\Payment\Info\Instructions', $paymentBlock->getData());
            $paymentInfo = $paymentBlock->setIsSecureMode(true)->toPdf();
            $paymentInfo = htmlspecialchars_decode($paymentInfo, ENT_QUOTES);
            $payment = explode('{{pdf_row_separator}}', $paymentInfo);
            foreach ($payment as $key => $value) {
                if (strip_tags(trim($value)) == '') {
                    unset($payment[$key]);
                }
            }
            reset($payment);
        } else {
            $payment = [];
        }*/


        /* Shipping Address and Method */
        if (!$quote->getIsVirtual()) {
            /* Shipping Address */

            $shippingAddress = $block->getPurchaseOrderData('shipping_address');
            $shippingAddress = explode("\n", $shippingAddress);
            $shippingMethod = $quote->getShippingMethod();
        }
        $page->setLineColor(new \Zend_Pdf_Color_Rgb(0.71,0,0));
        $page->setLineWidth(1);
        $page->drawLine(30, $top-20, 570, $top-20);

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->_setFontBold($page, 12);


        $page->drawText(__('Vendor:'), 35, $top - 15, 'UTF-8');

        $page->drawText(__('Ship To:'), 285, $top - 15, 'UTF-8');


        $addressesHeight = $this->_calcAddressHeight($billingAddress);
        if (isset($shippingAddress)) {
            $addressesHeight = max($addressesHeight, $this->_calcAddressHeight($shippingAddress));
        }

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
        //$page->drawRectangle(25, $top - 25, 570, $top - 33 - $addressesHeight);
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

        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineWidth(0.5);
        //$page->drawRectangle(25, $this->y, 275, $this->y - 25);
        //$page->drawRectangle(275, $this->y, 570, $this->y - 25);

        $this->y -= 15;
        $this->_setFontBold($page, 10);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        //$page->drawText(__('Payment Method'), 35, $this->y, 'UTF-8');
        $page->drawText(__('Shipping Method:'), 35, $this->y, 'UTF-8');

        $page->drawText(__('Payment Terms:'), 285, $this->y, 'UTF-8');

        //$this->y -= 10;
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));

        $this->_setFontRegular($page, 10);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));

        $paymentLeft = 35;
        $yPayments = $this->y - 15;

        /*foreach ($payment as $value) {
            if (trim($value) != '') {
                //Printing "Payment Method" lines
                $value = preg_replace('/<br[^>]*>/i', "\n", $value);
                foreach ($this->string->split($value, 45, true, true) as $_value) {
                    $page->drawText(strip_tags(trim($_value)), $paymentLeft, $yPayments, 'UTF-8');
                    $yPayments -= 15;
                }
            }
        }*/

        $topMargin = 0;
        $this->y -=15;
        $methodStartY = $this->y;

        /*foreach ($this->string->split($quote->getShippingAddress()->getShippingDescription(), 45, true, true) as $_value) {
            $page->drawText(strip_tags(trim($_value)), 35, $this->y, 'UTF-8');
            $this->y -= 15;
        }*/

        $yShipments = $this->y;
        $totalShippingChargesText = $quote->getShippingMethod();
        $page->drawText($totalShippingChargesText, 35, $yShipments - $topMargin, 'UTF-8');

        $totalShippingChargesText = $quote->getPaymentTerm();
        $page->drawText($totalShippingChargesText, 285, $yShipments - $topMargin, 'UTF-8');
        $yShipments -= $topMargin + 10;

        $tracks = [];
        /*if ($shipment) {
            $tracks = $shipment->getAllTracks();
        }
        if (count($tracks)) {
            $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
            $page->setLineWidth(0.5);
            $page->drawRectangle(35, $yShipments, 510, $yShipments - 10);
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
        }*/

        $currentY = min($yPayments, $yShipments);

        // replacement of Shipments-Payments rectangle block
        //$page->drawLine(25, $methodStartY, 25, $currentY);
        //left
        //$page->drawLine(25, $currentY, 570, $currentY);
        //bottom
        //$page->drawLine(570, $currentY, 570, $methodStartY);
        //right

        $this->y = $currentY;
        $this->y -= 15;
        $comment = $quote->getComment();
        if($comment){
            $this->_setFontBold($page, 10);
            $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
            $page->drawText(__('Comments:'), 35, $this->y, 'UTF-8');
            $this->y -= 15;

            $this->_setFontRegular($page, 10);
            $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
            $page->drawText($comment, 35, $this->y, 'UTF-8');

            $this->y -= 15;
        }
    }

    /**
     * Insert totals to pdf page
     *
     * @param \Zend_Pdf_Page $page
     * @param \Magestore\PurchaseOrderSuccess\Model\PurchaseOrder $quote
     * @return \Zend_Pdf_Page
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function insertTotals($page, $quote)
    {
        $block = $this->headerblock;

        $lineBlock = ['lines' => [], 'height' => 15];
        //$quote->collectTotals();

        $totalsDatas = array(
            array(
                'label' => 'Subtotal',
                'font_size' => 12,
                'amount' => $block->getPriceFormat($quote->getSubtotal()),
            ),
            array(
                'label' => 'Shipping Cost',
                'font_size' => 12,
                'amount' => $block->getPriceFormat($quote->getShippingCost()),
            ),
            array(
                'label' => 'Discount',
                'font_size' => 12,
                'amount' => $block->getPriceFormat($quote->getTotalDiscount()),
            ),
            array(
                'label' => 'Tax',
                'font_size' => 12,
                'amount' => $block->getPriceFormat($quote->getTotalTax()),
            ),
            array(
                'label' => 'Grand Total (excl. Tax)',
                'font_size' => 12,
                'amount' => $block->getPriceFormat($quote->getGrandTotalExclTax()),
                'font' => 'bold',
            ),
            array(
                'label' => 'Grand Total (incl. Tax)',
                'font_size' => 12,
                'amount' => $block->getPriceFormat($quote->getGrandTotalInclTax()),
                'font' => 'bold',
            )
        );
        foreach ($totalsDatas as $totalData) {
            $font = '';
            if(isset($totalData['font'])){
                $font = $totalData['font'];
            }
            $lineBlock['lines'][] = [
                [
                    'text' => $totalData['label'],
                    'feed' => 475,
                    'align' => 'right',
                    'font_size' => $totalData['font_size'],
                    'addToTop' => 2,
                    'font' => $font,
                ],
                [
                    'text' => $totalData['amount'],
                    'feed' => 565,
                    'align' => 'right',
                    'font_size' => $totalData['font_size'],
                    'addToTop' => 2,
                    'font' => $font,
                ],
            ];
        }

        $this->y -= 20;
        $page = $this->drawLineBlocks($page, [$lineBlock]);

        return $page;
    }

    /**
     * Before getPdf processing
     *
     * @return void
     */
    protected function _beforeGetPdf()
    {
        if ($this->inlineTranslation != null) {
            $this->inlineTranslation->suspend();
        }
    }

    /**
     * After getPdf processing
     *
     * @return void
     */
    protected function _afterGetPdf()
    {
        if ($this->inlineTranslation != null) {
            $this->inlineTranslation->resume();
        }
    }

    /**
     * Draw Quote Item process
     *
     * @param \Magento\Framework\DataObject $item
     * @param \Zend_Pdf_Page $page
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return \Zend_Pdf_Page
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _drawQuoteItem(
        \Magento\Framework\DataObject $item,
        \Zend_Pdf_Page $page,
        \Magestore\PurchaseOrderSuccess\Model\PurchaseOrder $quote
    ) {
        $type = $item->getProductType();
        $renderer = $this->_renderer;
        $renderer->setItem($item);
        $renderer->setPdf($this);
        $renderer->setQuote($quote);
        $renderer->setPage($page);
        $renderer->setRenderedModel($this);
        $renderer->draw();

        return $renderer->getPage();
    }

    /**
     * Draw lines
     *
     * Draw items array format:
     * lines        array;array of line blocks (required)
     * shift        int; full line height (optional)
     * height       int;line spacing (default 10)
     *
     * line block has line columns array
     *
     * column array format
     * text         string|array; draw text (required)
     * feed         int; x position (required)
     * font         string; font style, optional: bold, italic, regular
     * font_file    string; path to font file (optional for use your custom font)
     * font_size    int; font size (default 7)
     * align        string; text align (also see feed parametr), optional left, right
     * height       int;line spacing (default 10)
     *
     * @param  \Zend_Pdf_Page $page
     * @param  array $draw
     * @param  array $pageSettings
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Zend_Pdf_Page
     */
    public function drawLineBlocks(\Zend_Pdf_Page $page, array $draw, array $pageSettings = [])
    {
        foreach ($draw as $itemsProp) {
            if (!isset($itemsProp['lines']) || !is_array($itemsProp['lines'])) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('We don\'t recognize the draw line data. Please define the "lines" array.')
                );
            }
            $lines = $itemsProp['lines'];
            $height = isset($itemsProp['height']) ? $itemsProp['height'] : 10;

            if (empty($itemsProp['shift'])) {
                $shift = 0;
                foreach ($lines as $line) {
                    $maxHeight = 0;
                    foreach ($line as $column) {
                        $lineSpacing = !empty($column['height']) ? $column['height'] : $height;
                        if (!is_array($column['text'])) {
                            $column['text'] = [$column['text']];
                        }
                        $top = 0;
                        foreach ($column['text'] as $part) {
                            $top += $lineSpacing;
                        }

                        $maxHeight = $top > $maxHeight ? $top : $maxHeight;
                    }
                    $shift += $maxHeight;
                }
                $itemsProp['shift'] = $shift;
            }

            if ($this->y - $itemsProp['shift'] < 15) {
                $page = $this->newPage($pageSettings);
            }
            foreach ($lines as $line) {
                $maxHeight = 0;
                foreach ($line as $column) {
                    if (isset($column['imageUrl'])) {
                        continue;
                    }
                    $fontSize = empty($column['font_size']) ? 10 : $column['font_size'];
                    if (!empty($column['font_file'])) {
                        $font = \Zend_Pdf_Font::fontWithPath($column['font_file']);
                        $page->setFont($font, $fontSize);
                    } else {
                        $fontStyle = empty($column['font']) ? 'regular' : $column['font'];
                        switch ($fontStyle) {
                            case 'bold':
                                $font = $this->_setFontBold($page, $fontSize);
                                break;
                            case 'italic':
                                $font = $this->_setFontItalic($page, $fontSize);
                                break;
                            default:
                                $font = $this->_setFontRegular($page, $fontSize);
                                break;
                        }
                    }

                    if (!is_array($column['text'])) {
                        $column['text'] = [$column['text']];
                    }

                    $lineSpacing = !empty($column['height']) ? $column['height'] : $height;
                    if (is_array($column['text'])) {
                        $lineSpacing = 10;
                    }
                    $top = 0;
                    if (isset($column['isProductLine'])) {
                        $top += 10;
                    }

                    if (isset($column['addToTop'])) {
                        $top += $column['addToTop'];
                    }
                    foreach ($column['text'] as $part) {
                        if ($this->y - $lineSpacing < 15) {
                            $page = $this->newPage($pageSettings);
                        }
                        $feed = $column['feed'];
                        $textAlign = empty($column['align']) ? 'left' : $column['align'];
                        $width = empty($column['width']) ? 0 : $column['width'];
                        switch ($textAlign) {
                            case 'right':
                                if ($width) {
                                    $feed = $this->getAlignRight($part, $feed, $width, $font, $fontSize);
                                } else {
                                    $feed = $feed - $this->widthForStringUsingFontSize($part, $font, $fontSize);
                                }
                                break;
                            case 'center':
                                if ($width) {
                                    $feed = $this->getAlignCenter($part, $feed, $width, $font, $fontSize);
                                }
                                break;
                            default:
                                break;
                        }
                        $page->drawText($part, $feed, $this->y - $top, 'UTF-8');
                        $top += $lineSpacing;
                    }
                    $maxHeight = $top > $maxHeight ? $top : $maxHeight;
                }
                $this->y -= $maxHeight;
            }
        }

        return $page;
    }

    /**
     * Add name to the top.
     *
     * @param $page
     * @param $quote
     * @param $top
     * @return int
     */
    private function addName(&$page, $quote, $top)
    {
        $page->drawText(
            __('Name: ') . $quote->getCustomerName(),
            35,
            $top -= 15,
            'UTF-8'
        );

        return $top;
    }

    protected function _setFontRegular($object, $size = 7)
    {
        $font = \Zend_Pdf_Font::fontWithPath(
            $this->_rootDirectory->getAbsolutePath('lib/internal/ArimoFont/Arimo-Regular.ttf')
        );
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
        $font = \Zend_Pdf_Font::fontWithPath(
            $this->_rootDirectory->getAbsolutePath('lib/internal/ArimoFont/Arimo-Bold.ttf')
        );
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
        $font = \Zend_Pdf_Font::fontWithPath(
            $this->_rootDirectory->getAbsolutePath('lib/internal/ArimoFont/Arimo-Italic.ttf')
        );
        $object->setFont($font, $size);
        return $font;
    }
}
