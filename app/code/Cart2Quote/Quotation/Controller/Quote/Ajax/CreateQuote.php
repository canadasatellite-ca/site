<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Quotation\Controller\Quote\Ajax;

use Cart2Quote\Quotation\Model\QuotationCart as CustomerCart;

/**
 * Class CreateQuote
 *
 * @package Cart2Quote\Quotation\Controller\Quote\Ajax
 */
class CreateQuote extends \Cart2Quote\Quotation\Controller\Quote\Ajax\AjaxAbstract
{
    use \Cart2Quote\Features\Traits\Controller\Quote\Ajax\CreateQuote {
        processAction as private traitProcessAction;
        saveCustomer as private traitSaveCustomer;
        validateCustomerEmail as private traitValidateCustomerEmail;
        setCustomerName as private traitSetCustomerName;
        autoLogin as private traitAutoLogin;
        addQuotationData as private traitAddQuotationData;
        updateCustomerNote as private traitUpdateCustomerNote;
        updateCustomerGender as private traitUpdateCustomerGender;
        updateCustomerDob as private traitUpdateCustomerDob;
        save as private traitSave;
        sendEmailToCustomer as private traitSendEmailToCustomer;
        removeForcedShipping as private traitRemoveForcedShipping;
        isPhoneRequest as private traitIsPhoneRequest;
    }

    const EVENT_PREFIX = 'create_quote';

    /**
     * @var \Cart2Quote\Quotation\Helper\Address
     */
    private $addressHelper;

    /**
     * @var \Cart2Quote\Quotation\Model\Quote\File
     */
    protected $fileModel;

    /**
     * CreateQuote constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Cart2Quote\Quotation\Api\AccountManagementInterface $accountManagement
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Translate\InlineInterface $translateInline
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRequestRepository
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\DataObjectFactory $dataObjectFactory
     * @param \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory
     * @param \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteRequestSender $sender
     * @param \Cart2Quote\Quotation\Model\Session $quoteSession
     * @param \Cart2Quote\Quotation\Model\Quote\CreateQuote $createQuote
     * @param CustomerCart $quotationCart
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Status\Collection $statusCollection
     * @param \Cart2Quote\Quotation\Helper\Data $helper
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Cart2Quote\Quotation\Helper\Address $addressHelper
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Cart2Quote\Quotation\Model\Quote\TierItem $tierItemModel
     * @param \Cart2Quote\Quotation\Helper\Cloning $cloningHelper
     * @param \Cart2Quote\Quotation\Model\Quote\File $fileModel
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Cart2Quote\Quotation\Api\AccountManagementInterface $accountManagement,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRequestRepository,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory,
        \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteRequestSender $sender,
        \Cart2Quote\Quotation\Model\Session $quoteSession,
        \Cart2Quote\Quotation\Model\Quote\CreateQuote $createQuote,
        CustomerCart $quotationCart,
        \Cart2Quote\Quotation\Model\ResourceModel\Status\Collection $statusCollection,
        \Cart2Quote\Quotation\Helper\Data $helper,
        \Magento\Catalog\Helper\Product $productHelper,
        \Cart2Quote\Quotation\Helper\Address $addressHelper,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Cart2Quote\Quotation\Model\Quote\TierItem $tierItemModel,
        \Cart2Quote\Quotation\Helper\Cloning $cloningHelper,
        \Cart2Quote\Quotation\Model\Quote\File $fileModel
    ) {
        $this->fileModel = $fileModel;
        $this->addressHelper = $addressHelper;
        parent::__construct(
            $context,
            $customerSession,
            $customerRepository,
            $accountManagement,
            $coreRegistry,
            $translateInline,
            $formKeyValidator,
            $scopeConfig,
            $layoutFactory,
            $quoteRequestRepository,
            $resultPageFactory,
            $resultLayoutFactory,
            $resultRawFactory,
            $resultJsonFactory,
            $dataObjectFactory,
            $quoteFactory,
            $sender,
            $quoteSession,
            $createQuote,
            $quotationCart,
            $statusCollection,
            $helper,
            $productHelper,
            $logger,
            $checkoutSession,
            $tierItemModel,
            $cloningHelper
        );
    }

    /**
     * Request customer's quote.
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function processAction()
    {
        return $this->traitProcessAction();
    }

    /**
     * Save the customer.
     *
     * @param \Magento\Quote\Model\Quote $quote
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    private function saveCustomer(\Magento\Quote\Model\Quote $quote)
    {
        $this->traitSaveCustomer($quote);
    }

    /**
     * Check if customer exists
     *
     * @param string $email
     * @return bool|\Magento\Customer\Api\Data\CustomerInterface
     */
    private function validateCustomerEmail($email)
    {
        return $this->traitValidateCustomerEmail($email);
    }

    /**
     * Set the first and last name
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @return \Magento\Quote\Model\Quote
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function setCustomerName(\Magento\Quote\Model\Quote $quote)
    {
        return $this->traitSetCustomerName($quote);
    }

    /**
     * Auto login the customer
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\State\InputMismatchException
     */
    private function autoLogin(\Magento\Quote\Model\Quote $quote)
    {
        $this->traitAutoLogin($quote);
    }

    /**
     * Update the fields from the quotation data on the session.
     *
     * @return void
     */
    private function addQuotationData()
    {
        $this->traitAddQuotationData();
    }

    /**
     * Update that customer note on the quote.
     *
     * @param array $quoteData
     *
     * @return void
     */
    private function updateCustomerNote($quoteData)
    {
        $this->traitUpdateCustomerNote($quoteData);
    }

    /**
     * Update that customer gender on the quote.
     *
     * @param array $quoteData
     *
     * @return void
     */
    private function updateCustomerGender($quoteData)
    {
        $this->traitUpdateCustomerGender($quoteData);
    }

    /**
     * Update that customer dob on the quote.
     *
     * @param array $quoteData
     *
     * @return void
     */
    private function updateCustomerDob($quoteData)
    {
        $this->traitUpdateCustomerDob($quoteData);
    }

    /**
     * Save the Quotation Quote.
     *
     * @param \Magento\Quote\Model\Quote $quote
     *
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    private function save(\Magento\Quote\Model\Quote $quote)
    {
        return $this->traitSave($quote);
    }

    /**
     * Send the quote email to the customer.
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quotation
     *
     * @return void
     */
    private function sendEmailToCustomer(\Cart2Quote\Quotation\Model\Quote $quotation)
    {
        $this->traitSendEmailToCustomer($quotation);
    }

    /**
     * Remove shipping from quote
     *
     * @param \Magento\Quote\Model\Quote $quote
     */
    private function removeForcedShipping($quote)
    {
        $this->traitRemoveForcedShipping($quote);
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @return bool
     */
    private function isPhoneRequest($quote)
    {
        return $this->traitIsPhoneRequest($quote);
    }
}
