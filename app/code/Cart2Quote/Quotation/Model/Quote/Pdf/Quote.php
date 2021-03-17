<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Quote\Pdf;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;

/**
 * Quote PDF model
 */
class Quote extends \Cart2Quote\Quotation\Model\Quote\Pdf\AbstractPdf
{
    use \Cart2Quote\Features\Traits\Model\Quote\Pdf\Quote {
        setPdf as private traitSetPdf;
        createQuotePdf as private traitCreateQuotePdf;
        getPdf as private traitGetPdf;
        getQuotes as private traitGetQuotes;
        setQuotes as private traitSetQuotes;
        _drawSectionHeader as private _traitDrawSectionHeader;
        drawLineBlocks as private traitDrawLineBlocks;
        _drawHeader as private _traitDrawHeader;
        drawDisclaimer as private traitDrawDisclaimer;
        getIncrementId as private traitGetIncrementId;
        setPdfLocale as private traitSetPdfLocale;
        createPrintQuote as private traitCreatePrintQuote;
        createPrintQuotationQuote as private traitCreatePrintQuotationQuote;
        newPage as private traitNewPage;
    }

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Address\Renderer|\Magento\Sales\Model\Order\Address\Renderer
     */
    protected $_addressRenderer;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $localeResolver;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * @var array
     */
    protected $quotes;

    /**
     * @var \Magento\Framework\Translate
     */
    protected $translate;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Cart2Quote\Quotation\Model\QuoteFactory
     */
    protected $quotationFactory;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepositoryInterface;

    /**
     * System event manager
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $varDirectory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * Quote constructor
     *
     * @param \Magento\Framework\Translate $translate
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
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param \Cart2Quote\Quotation\Model\Quote\Pdf\Items\QuoteItem $renderer
     * @param \Cart2Quote\Quotation\Helper\ProductThumbnail $thumbnailHelper
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \Cart2Quote\Quotation\Model\QuoteFactory $quotationFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Psr\Log\LoggerInterface $logger
     * @param array $data
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        \Magento\Framework\Translate $translate,
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
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Cart2Quote\Quotation\Model\Quote\Pdf\Items\QuoteItem $renderer,
        \Cart2Quote\Quotation\Helper\ProductThumbnail $thumbnailHelper,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Cart2Quote\Quotation\Model\QuoteFactory $quotationFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Psr\Log\LoggerInterface $logger,
        array $data = []
    ) {
        $this->translate = $translate;
        $this->fileFactory = $fileFactory;
        $this->_storeManager = $storeManager;
        $this->localeResolver = $localeResolver;
        $this->_addressRenderer = $addressRenderer;
        $this->customerSession = $customerSession;
        $this->quoteFactory = $quoteFactory;
        $this->quotationFactory = $quotationFactory;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->eventManager = $eventManager;
        $this->varDirectory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->logger = $logger;

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
            $renderer,
            $thumbnailHelper,
            $data
        );
    }

    /**
     * Set Pdf model
     *
     * @param  \Cart2Quote\Quotation\Model\Quote\Pdf\AbstractPdf $pdf
     * @return $this
     */
    public function setPdf(\Cart2Quote\Quotation\Model\Quote\Pdf\AbstractPdf $pdf)
    {
        return $this->traitSetPdf($pdf);
    }

    /**
     * Creates the Quote PDF and return the filepath
     *
     * @param array $quotes
     * @return string|null
     * @throws \Exception
     */
    public function createQuotePdf(array $quotes)
    {
        return $this->traitCreateQuotePdf($quotes);
    }

    /**
     * Get PDF document
     *
     * @return \Zend_Pdf
     * @internal param array|\Cart2Quote\Quotation\Traits\Model\Quote\Pdf\Collection $quotes
     */
    public function getPdf()
    {
        return $this->traitGetPdf();
    }

    /**
     * Get array of quotes
     *
     * @return array
     */
    public function getQuotes()
    {
        return $this->traitGetQuotes();
    }

    /**
     * Set array of quotes
     *
     * @param array $quotes
     * @return $this
     * @throws \Exception
     */
    public function setQuotes(array $quotes)
    {
        return $this->traitSetQuotes($quotes);
    }

    /**
     * Draw section header
     *
     * @param \Zend_Pdf_Page $page
     * @param \Cart2Quote\Quotation\Api\Data\Quote\SectionInterface $section
     * @return \Zend_Pdf_Page
     */
    protected function _drawSectionHeader(
        \Zend_Pdf_Page $page,
        \Cart2Quote\Quotation\Api\Data\Quote\SectionInterface $section
    ) {
        return $this->_traitDrawSectionHeader($page, $section);
    }

    /**
     * Draw line blocks
     *
     * @param \Zend_Pdf_Page $page
     * @param array $draw
     * @param array $pageSettings
     * @return \Zend_Pdf_Page
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function drawLineBlocks(\Zend_Pdf_Page $page, array $draw, array $pageSettings = [])
    {
        return $this->traitDrawLineBlocks($page, $draw, $pageSettings);
    }

    /**
     * Draw header for item table
     *
     * @param \Zend_Pdf_Page $page
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Pdf_Exception
     */
    protected function _drawHeader(\Zend_Pdf_Page $page)
    {
        $this->_traitDrawHeader($page);
    }

    /**
     * Draw disclaimer
     *
     * @param \Zend_Pdf_Page $page
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @throws \Zend_Pdf_Exception
     */
    public function drawDisclaimer(\Zend_Pdf_Page $page, $quote)
    {
        $this->traitDrawDisclaimer($page, $quote);
    }

    /**
     * Get array of increments
     *
     * @param array $quotes
     * @return string
     */
    public function getIncrementId(array $quotes)
    {
        return $this->traitGetIncrementId($quotes);
    }

    /**
     * Set the correct store locale to the PDF
     *
     * @param int $storeId
     */
    public function setPdfLocale($storeId)
    {
        $this->traitSetPdfLocale($storeId);
    }

    /**
     * @return \Magento\Quote\Model\Quote
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function createPrintQuote()
    {
        return $this->traitCreatePrintQuote();
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    public function createPrintQuotationQuote($quote)
    {
        return $this->traitCreatePrintQuotationQuote($quote);
    }

    /**
     * Create new page and assign to PDF object
     *
     * @param  array $settings
     * @return \Zend_Pdf_Page
     */
    public function newPage(array $settings = [])
    {
        return $this->traitNewPage($settings);
    }
}
