<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\Adminhtml\Quote;

/**
 * Class Hold
 *
 * @package Cart2Quote\Quotation\Controller\Adminhtml\Quote
 */
class ChangeAddress extends \Cart2Quote\Quotation\Controller\Adminhtml\Quote
{
    /**
     * @var \Magento\Quote\Model\Quote\Address
     */
    private $quoteAddress;

    /**
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    private $addressService;

    /**
     * ChangeAddress constructor
     *
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
     * @param \Magento\Quote\Model\Quote\Address $quoteAddress
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressService
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Backend\Model\Session\Quote $backendQuoteSession
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Magento\GiftMessage\Model\Save $giftMessageSave
     * @param \Magento\Framework\Json\Helper\Data $jsonDataHelper
     */
    public function __construct(
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
        \Magento\Quote\Model\Quote\Address $quoteAddress,
        \Magento\Customer\Api\AddressRepositoryInterface $addressService,
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

        $this->quoteAddress = $quoteAddress;
        $this->addressService = $addressService;
    }

    /**
     * View quote detail
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        /** @var \Cart2Quote\Quotation\Model\Quote $quote */
        $quote = $this->_initQuote();

        if ($quote) {
            $addressType = $this->getRequest()->getParam('address_type');

            if ($addressType == 'billing') {
                $defaultBillingAddressId = $quote->getCustomer()->getDefaultBilling();
                if (isset($defaultBillingAddressId) && !empty($defaultBillingAddressId)) {
                    $this->quoteAddress->importCustomerAddressData(
                        $this->addressService->getById($defaultBillingAddressId)
                    );
                    $quote->setBillingAddress($this->quoteAddress);
                    $quote->setRecollect(true);
                    $quote->saveQuote();
                }
            }

            if ($addressType == 'shipping') {
                $defaultShippingAddressId = $quote->getCustomer()->getDefaultShipping();
                if (isset($defaultShippingAddressId) && !empty($defaultShippingAddressId)) {
                    $this->quoteAddress->importCustomerAddressData(
                        $this->addressService->getById($defaultShippingAddressId)
                    );
                    $quote->setShippingAddress($this->quoteAddress);
                    $quote->setRecollect(true);
                    $quote->saveQuote();
                }
            }

            return $this->resultRedirectFactory->create()->setPath(
                'quotation/quote/view',
                ['quote_id' => $quote->getId()]
            );
        }

        return $this->resultRedirectFactory->create()->setPath('quotation/quote/index');
    }
}
