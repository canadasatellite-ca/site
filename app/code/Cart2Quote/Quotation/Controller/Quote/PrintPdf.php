<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\Quote;

/**
 * Class PrintPdf
 * @package Cart2Quote\Quotation\Controller\Quote
 */
class PrintPdf extends \Cart2Quote\Quotation\Controller\Quote\Add
{
    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Pdf\Quote
     */
    protected $pdfModel;

    /**
     * @var \Cart2Quote\Quotation\Model\QuoteFactory
     */
    protected $quotationFactory;

    /**
     * @var \Cart2Quote\Quotation\Helper\Pdf\Download
     */
    protected $downloadHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * PrintPdf constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Cart2Quote\Quotation\Model\QuotationCart $cart
     * @param \Cart2Quote\Quotation\Model\Session $quotationSession
     * @param \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Cart2Quote\Quotation\Helper\Data $quotationDataHelper
     * @param \Magento\Framework\Locale\ResolverInterface $resolverInterface
     * @param \Magento\Framework\Escaper $escaper
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Cart2Quote\Quotation\Model\Quote\Request\Strategy\Provider $strategyProvider
     * @param \Cart2Quote\Quotation\Model\Quote\Pdf\Quote $pdfModel
     * @param \Cart2Quote\Quotation\Model\QuoteFactory $quotationFactory
     * @param \Cart2Quote\Quotation\Helper\Pdf\Download $downloadHelper
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Cart2Quote\Quotation\Model\QuotationCart $cart,
        \Cart2Quote\Quotation\Model\Session $quotationSession,
        \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Helper\Product $productHelper,
        \Cart2Quote\Quotation\Helper\Data $quotationDataHelper,
        \Magento\Framework\Locale\ResolverInterface $resolverInterface,
        \Magento\Framework\Escaper $escaper,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Cart2Quote\Quotation\Model\Quote\Request\Strategy\Provider $strategyProvider,
        \Cart2Quote\Quotation\Model\Quote\Pdf\Quote $pdfModel,
        \Cart2Quote\Quotation\Model\QuoteFactory $quotationFactory,
        \Cart2Quote\Quotation\Helper\Pdf\Download $downloadHelper,
        \Magento\Customer\Model\Session $customerSession
    ) {
        parent::__construct(
            $context,
            $scopeConfig,
            $storeManager,
            $formKeyValidator,
            $cart,
            $quotationSession,
            $quoteFactory,
            $resultPageFactory,
            $productRepository,
            $productHelper,
            $quotationDataHelper,
            $resolverInterface,
            $escaper,
            $logger,
            $jsonHelper,
            $strategyProvider,
            $customerSession
        );

        $this->customerSession = $customerSession;
        $this->pdfModel = $pdfModel;
        $this->quotationFactory = $quotationFactory;
        $this->downloadHelper = $downloadHelper;
    }


    /**
     * Add product to quote cart action
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        try {
            if (isset($params['qty'])) {
                $filter = new \Zend_Filter_LocalizedToNormalized(
                    [
                        'locale' => $this->resolverInterface->getLocale()
                    ]
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $product = $this->_initProduct();
            $related = $this->getRequest()->getParam('related_product');

            /**
             * Check product availability
             */
            if (!$product) {
                return $this->goBack();
            }

            //check if product is quoteable
            $checkProduct = $product;

            //if product is configurable check if Dynamic AddButtons is Enabled
            if ($checkProduct->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                //if that is enabled, extract the target simple product
                if ($this->quotationDataHelper->isDynamicAddButtonsEnabled()) {
                    $checkProduct = $this->_getProduct($checkProduct);
                    $request = $this->_getProductRequest($params);
                    $cartCandidates = $checkProduct->getTypeInstance()
                        ->prepareForCartAdvanced(
                            $request,
                            $checkProduct,
                            \Magento\Catalog\Model\Product\Type\AbstractType::PROCESS_MODE_FULL
                        );

                    foreach ($cartCandidates as $cartCandidate) {
                        if ($cartCandidate->getTypeId() == \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE) {
                            //we found a simple product
                            $checkProduct = $cartCandidate;
                            break;
                        }
                    }
                }
            }

            //check the product for quotability
            $quotable = $this->quotationDataHelper->isQuotable(
                $checkProduct,
                $this->customerSession->getCustomerGroupId()
            );

            if (!$quotable) {
                $this->messageManager->addErrorMessage(
                    __('This product is not quotable.')
                );

                return $this->goBack();
            }

            //save current quote
            $currentQuote = $this->cart->getQuote();

            //prepare temp PDF quote
            $quote = $this->pdfModel->createPrintQuote();
            $this->cart->setQuote($quote);
            $this->cart->addProduct($product, $params);
            if (!empty($related)) {
                $this->cart->addProductsByIds(explode(',', $related));
            }
            $this->cart->save();
            $quotation = $this->pdfModel->createPrintQuotationQuote($quote);
            $filePath = $this->pdfModel->createQuotePdf([$quotation]);
            $this->downloadHelper->setResource($filePath, \Magento\Downloadable\Helper\Download::LINK_TYPE_FILE);
            $fileName = $this->downloadHelper->getFilename();
            $contentType = $this->downloadHelper->getContentType();
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

            $fileSize = $this->downloadHelper->getFileSize();
            if ($fileSize) {
                $this->getResponse()->setHeader('Content-Length', $fileSize);
            }

            $this->getResponse()->setHeader('Content-Disposition', $contentDisposition . '; filename=' . $fileName);
            $this->getResponse()->clearBody();
            $this->getResponse()->sendHeaders();

            //restore current quote
            $this->cart->setQuote($currentQuote);
            $this->cart->save();

            //output PDF
            $this->downloadHelper->output();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            if ($this->_quotationSession->getUseNotice(true)) {
                $this->messageManager->addNoticeMessage(
                    $this->escaper->escapeHtml($e->getMessage())
                );
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->messageManager->addErrorMessage(
                        $this->escaper->escapeHtml($message)
                    );
                }
            }
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e);
            $this->logger->critical($e);
        }

        return $this->goBack();
    }
}
