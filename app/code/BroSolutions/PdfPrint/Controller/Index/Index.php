<?php

namespace BroSolutions\PdfPrint\Controller\Index;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class Index
 * @package BroSolutions\PdfPrint\Controller\Index
 */
class Index extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Digit\OrderButtons\Model\Pdf\OrderFactory
     */
    protected $orderPdfFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Sales\Api\InvoiceRepositoryInterface
     */
    protected $invoiceRepository;

    /**
     * @var \Magento\Sales\Model\Order\Pdf\Invoice
     */
    protected $invoicePdf;

    /**
     * Index constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepository
     * @param \Digit\OrderButtons\Model\Pdf\OrderFactory $orderPdfFactory
     * @param \Magento\Sales\Model\Order\Pdf\Invoice $invoicePdf
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Customer\Model\Session $session
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepository,
        \Digit\OrderButtons\Model\Pdf\OrderFactory $orderPdfFactory,
        \Magento\Sales\Model\Order\Pdf\Invoice $invoicePdf,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Customer\Model\Session $session
    )
    {
        $this->_pageFactory = $pageFactory;
        $this->fileFactory = $fileFactory;
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
        $this->orderRepository = $orderRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->orderPdfFactory = $orderPdfFactory;
        $this->invoicePdf = $invoicePdf;
        $this->date = $date;
        $this->_customerSession = $session;
        return parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $customerId = $this->_customerSession->getCustomerId();

        try {
            $orderId = (int)$this->getRequest()->getParam('order_id');

            if ($orderId) {
                $order = $this->orderRepository->get($orderId);
                if ($order->getId()
                    && $order->getCustomerId()
                    && $order->getCustomerId() == $customerId) {
                    $pdf = $this->orderPdfFactory->create()->getPdf([$order]);
                    $date = $this->date->date('Y-m-d_H-i-s');
                    return $this->fileFactory->create(
                        'order' . $date . '.pdf',
                        $pdf->render(),
                        DirectoryList::VAR_DIR,
                        'application/pdf'
                    );
                }
            }
        } catch (\Exception $e) {

        }

        try {
            $invoiceId = (int)$this->getRequest()->getParam('invoice_id');

            if ($invoiceId) {
                $invoice = $this->invoiceRepository->get($invoiceId);
                if ($invoice && $invoice->getId()) {
                    $order = $invoice->getOrder();

                    if ($order->getId()
                        && $order->getCustomerId()
                        && $order->getCustomerId() == $customerId) {
                        $pdf = $this->invoicePdf->getPdf([$invoice]);
                        $date = $this->date->date('Y-m-d_H-i-s');
                        return $this->fileFactory->create(
                            'invoice' . $date . '.pdf',
                            $pdf->render(),
                            DirectoryList::VAR_DIR,
                            'application/pdf'
                        );
                    }
                }

            }
        } catch (\Exception $e) {

        }

        return $this->resultRedirectFactory->create()->setPath('sales/order/history');
    }
}
