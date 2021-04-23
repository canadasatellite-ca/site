<?php
namespace MageSuper\Casat\Controller\Adminhtml\PurchaseOrder;

use Magento\Backend\App\Action;

/**
 * Class Pdf
 * @package MageSuper\Casat\Controller\Adminhtml\PO
 */
class Pdf extends \MageSuper\Casat\Controller\Adminhtml\PurchaseOrder
{
    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Pdf\Quote
     */
    protected $_pdfModel;
    /**
     * Download helper
     *
     * @var \Magento\Downloadable\Helper\Download
     */
    protected $_downloadHelper;

    /**
     * Pdf constructor.
     * @param \Magento\Framework\Escaper $escaper
     * @param Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Translate\InlineInterface $translateInline
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Cart2Quote\Quotation\Helper\Data $helperData
     * @param \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Status\Collection $statusCollection
     * @param \Cart2Quote\Quotation\Model\Quote\Pdf\Quote $pdfModel
     * @param \Magento\Downloadable\Helper\Download $downloadHelper
     * @param array $data
     */
    function __construct(
        \Magento\Framework\Escaper $escaper,
        Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Cart2Quote\Quotation\Helper\Data $helperData,
        \Magestore\PurchaseOrderSuccess\Model\PurchaseOrderFactory $poFactory,
        \Magestore\SupplierSuccess\Api\SupplierRepositoryInterface $supplierRepository,
        \Cart2Quote\Quotation\Model\ResourceModel\Status\Collection $statusCollection,
        \MageSuper\Casat\Model\PurchaseOrder\Pdf\PurchaseOrder $pdfModel,
        \Magento\Downloadable\Helper\Download $downloadHelper,
        array $data = []
    ) {
        parent::__construct(
            $escaper,
            $context,
            $coreRegistry,
            $fileFactory,
            $translateInline,
            $resultPageFactory,
            $resultJsonFactory,
            $resultLayoutFactory,
            $resultRawFactory,
            $helperData,
            $poFactory,
            $supplierRepository,
            $statusCollection
        );

        $this->_pdfModel = $pdfModel;
        $this->_downloadHelper = $downloadHelper;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Cart2Quote_Quotation::actions_view');
    }

    /**
     * Download PDF for the quotation quote item
     */
    function execute()
    {
        if ($results = parent::execute()) {
            return $results;
        }

        ini_set('zlib.output_compression', '0');
        $po = $this->_initPurchaseOrder();
        $filePath = $this->_pdfModel->createQuotePdf([$po]);

        $this->_downloadHelper->setResource($filePath, \Magento\Downloadable\Helper\Download::LINK_TYPE_FILE);
        $fileName = $this->_downloadHelper->getFilename();
        $contentType = $this->_downloadHelper->getContentType();
        //$contentDisposition = $this->_downloadHelper->getContentDisposition()
        $contentDisposition = 'attachment';

        $this->getResponse()->setHttpResponseCode(
            200
        )->setHeader(
            'target',
            '_blank',
            true
        )->setHeader(
            'Pragma',
            'public',
            true
        )->setHeader(
            'Cache-Control',
            'private, max-age=0, must-revalidate, post-check=0, pre-check=0',
            true
        )->setHeader(
            'Content-type',
            $contentType,
            true
        );

        if ($fileSize = $this->_downloadHelper->getFileSize()) {
            $this->getResponse()->setHeader('Content-Length', $fileSize);
        }

        $this->getResponse()->setHeader('Content-Disposition', $contentDisposition . '; filename=' . $fileName);

        $this->getResponse()->clearBody();
        $this->getResponse()->sendHeaders();

        $this->_downloadHelper->output();
    }
}
