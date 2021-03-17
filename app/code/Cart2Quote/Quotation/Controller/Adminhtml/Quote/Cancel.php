<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\Adminhtml\Quote;

/**
 * Class Cancel
 * @package Cart2Quote\Quotation\Controller\Adminhtml\Quote
 */
class Cancel extends \Cart2Quote\Quotation\Controller\Adminhtml\Quote
{
    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteCanceledSender
     */
    protected $quoteCanceledSender;

    /**
     * @var \Cart2Quote\Quotation\Api\QuoteRepositoryInterface
     */
    private $quoteRepository;

    /**
     * Cancel constructor
     *
     * @param \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteCanceledSender $quoteCanceledSender
     * @param \Cart2Quote\Quotation\Api\QuoteRepositoryInterface $quoteRepository
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
     * @param \Magento\Store\Model\Store $store
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Backend\App\Action\Context $context
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
     * @param \Cart2Quote\Quotation\Model\Admin\Quote\Create $quoteCreate
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Cart2Quote\Quotation\Helper\Cloning $cloningHelper
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Backend\Model\Session\Quote $backendQuoteSession
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Magento\GiftMessage\Model\Save $giftMessageSave
     * @param \Magento\Framework\Json\Helper\Data $jsonDataHelper
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteCanceledSender $quoteCanceledSender,
        \Cart2Quote\Quotation\Api\QuoteRepositoryInterface $quoteRepository,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Store\Model\Store $store,
        \Magento\Framework\Escaper $escaper,
        \Magento\Backend\App\Action\Context $context,
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
        \Cart2Quote\Quotation\Model\Admin\Quote\Create $quoteCreate,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Cart2Quote\Quotation\Helper\Cloning $cloningHelper,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Backend\Model\Session\Quote $backendQuoteSession,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\GiftMessage\Model\Save $giftMessageSave,
        \Magento\Framework\Json\Helper\Data $jsonDataHelper
    ) {
        parent::__construct(
            $customerRepositoryInterface,
            $store,
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
            $quoteFactory,
            $statusCollection,
            $quoteCreate,
            $scopeConfig,
            $cloningHelper,
            $logger,
            $backendQuoteSession,
            $productHelper,
            $giftMessageSave,
            $jsonDataHelper
        );

        $this->quoteCanceledSender = $quoteCanceledSender;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * Cancel quotation and reload page
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\Controller\Result\Redirect|null
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $originalQuote = $this->quoteRepository->get($this->getRequest()->getParam('quote_id'));
            $originalQuote->setData('state', \Cart2Quote\Quotation\Model\Quote\Status::STATE_CANCELED);
            $originalQuote->setData('status', \Cart2Quote\Quotation\Model\Quote\Status::STATUS_CANCELED);
            $originalQuote->save();
            if ($this->quoteCanceledSender->send($originalQuote)) {
                $this->messageManager->addSuccessMessage(__('The customer is notified'));
            }
            $resultRedirect->setPath('quotation/quote/view', ['quote_id' => $originalQuote->getId()]);

        } catch(\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Quote saving error: %1', $e->getMessage()));
            $resultRedirect->setPath('quotation/quote/view', ['quote_id' => $this->getCurrentQuote()->getId()]);
        }

        return $resultRedirect;
    }
}