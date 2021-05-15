<?php
/**
 * Cart2Quote
 */
namespace Interactivated\Quotecheckout\Controller\Quote;


class Download extends \Magento\Framework\App\Action\Action
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
    protected $quoteFactory;
    protected $_currentQuote;
    protected $_coreRegistry;

    function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Cart2Quote\Quotation\Helper\Data $helperData,
        \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory,
        \Cart2Quote\Quotation\Model\ResourceModel\Status\Collection $statusCollection,
        \Cart2Quote\Quotation\Model\Quote\Pdf\Quote $pdfModel,
        \Magento\Downloadable\Helper\Download $downloadHelper,
        array $data = []
    ) {
        parent::__construct(
            $context
        );

        $this->_pdfModel = $pdfModel;
        $this->_downloadHelper = $downloadHelper;
        $this->quoteFactory = $quoteFactory;
        $this->_coreRegistry = $coreRegistry;
    }
    /**
     * Download PDF for the quotation quote item
     */
    function execute()
    {
        ini_set('zlib.output_compression', '0');
        $quote = $this->_initQuote();
        $filePath = $this->_pdfModel->createQuotePdf([$quote]);

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


    protected function _initQuote()
    {
        $id = $this->getRequest()->getParam('quote_id');

        $this->_currentQuote = $this->quoteFactory->create()->load($id);
        $session = $this->_getSession();
        $customerId = $session->getCustomerId();
        $quoteCustomerId = $this->_currentQuote->getCustomerId();

        if (!$this->_currentQuote->getId() || $quoteCustomerId!=$customerId) {
            $this->messageManager->addError(__('This quote no longer exists.'));
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);

            return false;
        }

        $this->_coreRegistry->unregister('current_quote');
        $this->_coreRegistry->register('current_quote', $this->_currentQuote);

        return $this->_currentQuote;
    }

    /**
     * Retrieve session object
     * @return \Magento\Customer\Model\Session
     */
    protected function _getSession()
    {
        return $this->_objectManager->get('Magento\Customer\Model\Session');
    }

}
