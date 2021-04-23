<?php
/**
 * Cart2Quote
 */
namespace MageSuper\Casat\Model\PurchaseOrder\Pdf;

/**
 * Quote PDF model
 */
class PurchaseOrder extends \MageSuper\Casat\Model\PurchaseOrder\Pdf\AbstractPdf
{
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
    protected $_localeResolver;
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;
    /**
     * @var array
     */
    protected $quotes;

    /**
     * Quote constructor.
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
     * @param Items\QuoteItem $renderer
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param array $data
     */
    function __construct(
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
        \MageSuper\Casat\Model\PurchaseOrder\Pdf\Items\QuoteItem $renderer,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\ObjectManagerInterface $om,
        array $data = []
    ) {
        $this->fileFactory = $fileFactory;
        $this->_storeManager = $storeManager;
        $this->_localeResolver = $localeResolver;
        $this->_addressRenderer = $addressRenderer;
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
            $om,
            $data
        );
    }

    /**
     * Set Pdf model
     *
     * @param  \Cart2Quote\Quotation\Model\Quote\Pdf\AbstractPdf $pdf
     * @return $this
     */
    function setPdf(\Cart2Quote\Quotation\Model\Quote\Pdf\AbstractPdf $pdf)
    {
        $this->_pdf = $pdf;

        return $this;
    }

    /**
     * Creates the Quote PDF and return the filepath
     *
     * @param array $quotes
     * @return string|null
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    function createQuotePdf(array $quotes)
    {
        $this->setQuotes($quotes);
        $pdf = $this->getPdf();
        if (isset($pdf)) {
            $fileName = sprintf('quotation/Canada-Satellite-Purchase-Order-%s.pdf', $this->getIncrementId($quotes));
            $pdfcontent = $pdf->render();
            $pdfcontent = str_replace('/Annot /Subtype /Link', '/Annot /Subtype /Link /Border[0 0 0]',$pdfcontent);
            $this->_mediaDirectory->writeFile(
                $fileName,
                $pdfcontent
            );

            return $fileName;
        }

        return null;
    }
    /*protected function _initRenderer($type)
    {
        $this->_renderers = array(
            'default' => ['model' => "Magento\Sales\Model\Order\Pdf\Items\Invoice\DefaultInvoice", 'renderer' => null],
            'bundle'  => ['model' => "Digit\OrderButtons\Model\Bundle\Sales\Order\Pdf\Items\Order", 'renderer' => null],
            'downloadable' => ['model' => "Magento\Downloadable\Model\Sales\Order\Pdf\Items\Invoice",'renderer' => null],
            'grouped' => ['model' => "Magento\GroupedProduct\Model\Order\Pdf\Items\Invoice\Grouped",'renderer' => null]
        );
    }*/
    /**
     * Get PDF document
     * @return \Zend_Pdf
     * @internal param array|\Cart2Quote\Quotation\Traits\Model\Quote\Pdf\Collection $quotes
     */
    function getPdf()
    {
        $this->_beforeGetPdf();

        $pdf = new \Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new \Zend_Pdf_Style();
        $this->_setFontBold($style, 10);
        foreach ($this->getQuotes() as $quote) {
            $store = $quote->getStore();
            $page = $this->newPage();

            /* Add image */
            $this->insertLogo($page, $store);

            /* Add address */
            //$this->insertAddress($page, $store);

            $block = $this->om->create('\Magestore\PurchaseOrderSuccess\Block\Adminhtml\Email\EmailToSupplier\Items',array('data'=>array()));
            $items = $block->getPurchaseOrderItems();
            $quote->setPoBlock($block);


            /* Add quote data */
            $this->insertQuote($page, $quote);

            /** @var \Magestore\PurchaseOrderSuccess\Block\Adminhtml\Email\EmailToSupplier\Items $block */


            /* Add table */
            $this->_drawHeader($page,$quote);

            /* Add body */
            foreach ($items as $item) {
                if ($item->getParentItem() && ($item->getParentItem()->getProductType() != 'bundle')) {
                    continue;
                }
                /* Draw item */
                $this->_drawQuoteItem($item, $page, $quote);
                $page = end($pdf->pages);
            }

            /* Add totals */
            $totalsY = $this->y;
            $this->insertTotals($page, $quote);

            /* Draw Comments */
            /*$this->y = $totalsY;
            if ($quote->getCustomerNoteNotify() && $quote->getCustomerNote() != '') {
                $this->insertComments($page, $quote);
            }*/

            $page = end($pdf->pages);
            $this->insertFooter($page);

            if ($quote->getStoreId()) {
                $this->_localeResolver->revert();
            }
        }
        $this->_afterGetPdf();

        return $pdf;
    }

    /**
     * Get array of quotes
     *
     * @return array
     */
    function getQuotes()
    {
        return $this->quotes;
    }

    /**
     * Set array of quotes
     *
     * @param array $quotes
     * @return $this
     * @throws \Exception
     */
    function setQuotes(array $quotes)
    {
        foreach ($quotes as $quote) {
            if (!$quote instanceof \Magestore\PurchaseOrderSuccess\Model\PurchaseOrder) {
                throw new \Exception(__('Invalid quote PurchaseOrder provided for the PDF. ' .
                    'Expected class \Magestore\PurchaseOrderSuccess\Model\PurchaseOrder'));
            }
        }

        $this->quotes = $quotes;

        return $this;
    }

    /**
     * Draw header for item table
     *
     * @param \Zend_Pdf_Page $page
     * @return void
     */
    protected function _drawHeader(\Zend_Pdf_Page $page, $quote)
    {
        /* Add table head */
        $this->_setFontRegular($page, 10);
        //$page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineColor(new \Zend_Pdf_Color_Rgb(0.71,0,0));
        $page->setLineWidth(1);
        //$page->drawRectangle(25, $this->y, 570, $this->y - 15);
        $page->drawLine(30, $this->y-25, 570, $this->y-25);
        $this->y -= 10;
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0, 0, 0));

        //columns headers
        $isShowQtyReceived = $quote->getPoBlock()->checkIsShowQtyReceived();

        $lines[0][] = ['text' => __('Product'), 'feed' => 35,'font'=>'bold'];
        $lines[0][] = ['text' => __('Supplier SKU'), 'feed' => 400, 'align' => 'right','font'=>'bold'];

        $lines[0][] = ['text' => __('Qty'), 'feed' => 450, 'align' => 'right','font'=>'bold'];
        /*if ($isShowQtyReceived){
            $lines[0][] = ['text' => __('Qty Received'), 'feed' => 500, 'align' => 'right','font'=>'bold'];
        }*/
        //$lines[0][] = ['text' => __('Tax'), 'feed' => 488, 'align' => 'right','font'=>'bold'];
        $lines[0][] = ['text' => __('Each'), 'feed' => 500, 'align' => 'right','font'=>'bold'];
        $lines[0][] = ['text' => __('Subtotal'), 'feed' => 570, 'align' => 'right','font'=>'bold'];

        $lineBlock = ['lines' => $lines, 'height' => 5];

        $this->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->y -= 20;
    }

    /**
     * Get array of increments
     *
     * @param array $quotes
     * @return string
     */
    function getIncrementId(array $quotes)
    {
        $incrementIds = [];
        foreach ($quotes as $quote) {
            $incrementIds[] = $quote->getData('purchase_code');
        }

        return implode("-", $incrementIds);
    }
}
