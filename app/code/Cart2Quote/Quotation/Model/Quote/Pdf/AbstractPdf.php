<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Quote\Pdf;

use Magento\Store\Model\Store;

/**
 * Quotation PDF abstract model
 */
abstract class AbstractPdf extends \Magento\Sales\Model\Order\Pdf\AbstractPdf
{
    use \Cart2Quote\Features\Traits\Model\Quote\Pdf\AbstractPdf {
        getStringUtils as private traitGetStringUtils;
        insertAddress as private traitInsertAddress;
        insertFooter as private traitInsertFooter;
        insertComments as private traitInsertComments;
        insertQuote as private traitInsertQuote;
        insertTotals as private traitInsertTotals;
        drawLineBlocks as private traitDrawLineBlocks;
        _beforeGetPdf as private _traitBeforeGetPdf;
        _afterGetPdf as private _traitAfterGetPdf;
        _drawQuoteItem as private _traitDrawQuoteItem;
        drawShippingOptionsAndPricesReplacement as private traitDrawShippingOptionsAndPricesReplacement;
        addName as private traitAddName;
        setFont as private traitSetFont;
        _setFontRegular as private _traitSetFontRegular;
        _setFontBold as private _traitSetFontBold;
        _setFontItalic as private _traitSetFontItalic;
        saveSpaceFonts as private traitSaveSpaceFonts;
        drawLineBlockRow as private traitDrawLineBlockRow;
        drawShippingAddressAndMethod as private traitDrawShippingAddressAndMethod;
    }

    /**
     * @var bool
     */
    public $newPage = false;

    /**
     * Predefined constants
     */
    const XML_PATH_SALES_PDF_INVOICE_PACKINGSLIP_ADDRESS = 'sales/identity/address';

    /**
     * Path to pdf_save_space in system.xml
     */
    const XML_PATH_SAVE_SPACE_FONTS = 'cart2quote_pdf/quote/pdf_save_space';

    /**
     * Default value of 'y' when new page
     */
    const NEW_PAGE_Y_VALUE = '800';

    /**
     * @var \Cart2Quote\Quotation\Helper\ProductThumbnail
     */
    protected $thumbnailHelper;

    /**
     * AbstractPdf constructor
     *
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
     * @param \Cart2Quote\Quotation\Model\Quote\Pdf\Items\QuoteItem $renderer
     * @param \Cart2Quote\Quotation\Helper\ProductThumbnail $thumbnailHelper
     * @param array $data
     * @throws \Magento\Framework\Exception\FileSystemException
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
        \Cart2Quote\Quotation\Model\Quote\Pdf\Items\QuoteItem $renderer,
        \Cart2Quote\Quotation\Helper\ProductThumbnail $thumbnailHelper,
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
        $this->thumbnailHelper = $thumbnailHelper;
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
     * Get string standard library utilities
     *
     * @return \Magento\Framework\Stdlib\StringUtils
     */
    public function getStringUtils()
    {
        return $this->traitGetStringUtils();
    }

    /**
     * Insert address to pdf page
     *
     * @param \Zend_Pdf_Page $page
     * @param null|Store $store
     * @throws \Zend_Pdf_Exception
     */
    protected function insertAddress(&$page, $store = null)
    {
        $this->traitInsertAddress($page, $store);
    }

    /**
     * Insert General comment to PDF
     *
     * @param \Zend_Pdf_Page $page
     * @param string $text
     * @throws \Zend_Pdf_Exception
     */
    protected function insertFooter(\Zend_Pdf_Page $page, $text)
    {
        $this->traitInsertFooter($page, $text);
    }

    /**
     * Insert quote comment to PDF
     *
     * @param \Zend_Pdf_Page $page
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return \Zend_Pdf_Page
     * @throws \Zend_Pdf_Exception
     */
    protected function insertComments(\Zend_Pdf_Page $page, $quote)
    {
        return $this->traitInsertComments($page, $quote);
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
        return $this->traitInsertQuote($page, $obj, $putQuoteId);
    }

    /**
     * Insert totals to pdf page
     *
     * @param \Zend_Pdf_Page $page
     * @param \Magento\Sales\Model\AbstractModel $quote
     * @return \Zend_Pdf_Page
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function insertTotals($page, $quote)
    {
        return $this->traitInsertTotals($page, $quote);
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
        return $this->traitDrawLineBlocks($page, $draw, $pageSettings);
    }

    /**
     * Before getPdf processing
     *
     * @return void
     */
    protected function _beforeGetPdf()
    {
        $this->_traitBeforeGetPdf();
    }

    /**
     * After getPdf processing
     *
     * @return void
     */
    protected function _afterGetPdf()
    {
        $this->_traitAfterGetPdf();
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
        \Cart2Quote\Quotation\Model\Quote $quote
    ) {
        return $this->_traitDrawQuoteItem($item, $page, $quote);
    }

    /**
     * Function that draws the shipping options and prices
     *
     * @param \Zend_Pdf_Page $page
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @param \Magento\Sales\Model\Order\Shipment $shipment
     * @param int $yPayments
     * @throws \Zend_Pdf_Exception
     */
    protected function drawShippingOptionsAndPricesReplacement(&$page, $quote, $shipment, $yPayments)
    {
        $this->traitDrawShippingOptionsAndPricesReplacement($page, $quote, $shipment, $yPayments);
    }

    /**
     * Add name to the top.
     *
     * @param \Zend_Pdf_Page $page
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @param int $top
     * @return int
     */
    private function addName(&$page, $quote, $top)
    {
        return $this->traitAddName($page, $quote, $top);
    }

    /**
     * Set page font.
     *
     * - column array format
     * - font         string; font style, optional: bold, italic, regular
     * - font_file    string; path to font file (optional for use your custom font)
     * - font_size    int; font size (default 10)
     *
     * @param \Zend_Pdf_Page $page
     * @param array $column
     * @return \Zend_Pdf_Resource_Font
     * @throws \Zend_Pdf_Exception
     */
    private function setFont($page, &$column)
    {
        return $this->traitSetFont($page, $column);
    }

    /**
     * Set font as regular
     *
     * @param \Zend_Pdf_Page $object
     * @param int $size
     * @return \Zend_Pdf_Resource_Font
     * @throws \Zend_Pdf_Exception
     */
    protected function _setFontRegular($object, $size = 7)
    {
        return $this->_traitSetFontRegular($object, $size);
    }

    /**
     * Set font as regular bold
     *
     * @param \Zend_Pdf_Page $object
     * @param int $size
     * @return \Zend_Pdf_Resource_Font
     * @throws \Zend_Pdf_Exception
     */
    protected function _setFontBold($object, $size = 7)
    {
        return $this->_traitSetFontBold($object, $size);
    }

    /**
     * Set font as italic
     *
     * @param \Zend_Pdf_Page $object
     * @param int $size
     * @return \Zend_Pdf_Resource_Font
     * @throws \Zend_Pdf_Exception
     */
    protected function _setFontItalic($object, $size = 7)
    {
        return $this->_traitSetFontItalic($object, $size);
    }

    /**
     * Get the setting to save fonts
     *
     * @return boolean
     */
    protected function saveSpaceFonts()
    {
        return $this->traitSaveSpaceFonts();
    }

    /**
     * Draws row data in line block
     *
     * @param \Zend_Pdf_Page $page
     * @param array $pageSettings
     * @param array $line
     * @param int $height
     * @return \Zend_Pdf_Page $page
     * @throws \Zend_Pdf_Exception
     */
    private function drawLineBlockRow(\Zend_Pdf_Page $page, array $pageSettings, $line, $height)
    {
        return $this->traitDrawLineBlockRow($page, $pageSettings, $line, $height);
    }

    /**
     * Draw shipping address and method
     *
     * @param \Zend_Pdf_Page $page
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @param int $top
     * @param array $billingAddress
     * @param array $payment
     * @param \Magento\Sales\Model\Order\Shipment $shipment
     * @return \Zend_Pdf_Page $page
     * @throws \Zend_Pdf_Exception
     */
    private function drawShippingAddressAndMethod(
        &$page,
        $quote,
        $top,
        array $billingAddress,
        array $payment,
        $shipment
    ) {
        return $this->traitDrawShippingAddressAndMethod($page, $quote, $top, $billingAddress, $payment, $shipment);
    }
}
