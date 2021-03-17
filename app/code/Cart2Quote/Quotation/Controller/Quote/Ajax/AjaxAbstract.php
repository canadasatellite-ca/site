<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\Quote\Ajax;

use Cart2Quote\Quotation\Model\QuotationCart as CustomerCart;
use Cart2Quote\Quotation\Model\Quote;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\Error;
use Magento\Framework\Validator\Exception as ValidatorException;

/**
 * Class AjaxAbstract
 *
 * @package Cart2Quote\Quotation\Controller\Quote\Ajax
 */
abstract class AjaxAbstract extends \Magento\Checkout\Controller\Onepage
{
    use \Cart2Quote\Features\Traits\Controller\Quote\Ajax\AjaxAbstract {
        execute as private traitExecute;
        _expireAjax as private _traitExpireAjax;
        getOnepage as private traitGetOnepage;
        getEventPrefix as private traitGetEventPrefix;
        processAction as private traitProcessAction;
        isCustomerLoggedIn as private traitIsCustomerLoggedIn;
        updateQuotationProductData as private traitUpdateQuotationProductData;
        _preDispatchValidateCustomer as private _traitPreDispatchValidateCustomer;
    }

    const EVENT_PREFIX = 'Default';

    /**
     * Sender
     *
     * @var \Cart2Quote\Quotation\Model\Quote\Email\AbstractSender
     */
    protected $sender;

    /**
     * Quote Proposal Accepted Sender
     *
     * @var Quote\Email\Sender\QuoteProposalAcceptedSender
     */
    protected $_quoteProposalAcceptedSender;

    /**
     * Quote Repository
     *
     * @var \Magento\Quote\Model\QuoteRepository
     */
    protected $_quoteRepository;

    /**
     * Quote Session
     *
     * @var \Cart2Quote\Quotation\Model\Session
     */
    protected $quoteSession;

    /**
     * Data Object Factory
     *
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * Quote Factory
     *
     * @var \Cart2Quote\Quotation\Model\QuoteFactory
     */
    protected $quoteFactory;

    /**
     * Customer Cart
     *
     * @var CustomerCart
     */
    protected $quotationCart;

    /**
     * Create Quote
     *
     * @var \Cart2Quote\Quotation\Model\Quote\CreateQuote
     */
    protected $createQuote;

    /**
     * Data Object
     *
     * @var \Magento\Framework\DataObject
     */
    protected $result;

    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Status\Collection
     */
    protected $statusCollection;

    /**
     * Data helper
     *
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Catalog\Helper\Product
     */
    protected $productHelper;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Cart2Quote\Quotation\Model\Quote\TierItem
     */
    private $tierItemModel;

    /**
     * @var \Cart2Quote\Quotation\Helper\Cloning
     */
    protected $cloningHelper;

    /**
     * AjaxAbstract constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     * @param AccountManagementInterface $accountManagement
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Translate\InlineInterface $translateInline
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\DataObjectFactory $dataObjectFactory
     * @param \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory
     * @param Quote\Email\Sender\QuoteRequestSender $sender
     * @param \Cart2Quote\Quotation\Model\Session $quoteSession
     * @param Quote\CreateQuote $createQuote
     * @param CustomerCart $quotationCart
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Status\Collection $statusCollection
     * @param \Cart2Quote\Quotation\Helper\Data $helper
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param Quote\TierItem $tierItemModel
     * @param \Cart2Quote\Quotation\Helper\Cloning $cloningHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $accountManagement,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory,
        \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteRequestSender $sender,
        \Cart2Quote\Quotation\Model\Session $quoteSession,
        \Cart2Quote\Quotation\Model\Quote\CreateQuote $createQuote,
        \Cart2Quote\Quotation\Model\QuotationCart $quotationCart,
        \Cart2Quote\Quotation\Model\ResourceModel\Status\Collection $statusCollection,
        \Cart2Quote\Quotation\Helper\Data $helper,
        \Magento\Catalog\Helper\Product $productHelper,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Cart2Quote\Quotation\Model\Quote\TierItem $tierItemModel,
        \Cart2Quote\Quotation\Helper\Cloning $cloningHelper
    ) {
        $this->quotationCart = $quotationCart;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->quoteFactory = $quoteFactory;
        $this->sender = $sender;
        $this->quoteSession = $quoteSession;
        $this->createQuote = $createQuote;
        $this->statusCollection = $statusCollection;
        $this->helper = $helper;
        $this->productHelper = $productHelper;
        $this->logger = $logger;
        $this->checkoutSession = $checkoutSession;
        $this->tierItemModel = $tierItemModel;
        $this->cloningHelper = $cloningHelper;

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
            $quoteRepository,
            $resultPageFactory,
            $resultLayoutFactory,
            $resultRawFactory,
            $resultJsonFactory
        );
    }

    /**
     * Update quote data action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        return $this->traitExecute();
    }

    /**
     * Validate ajax request and redirect on failure
     *
     * @return bool
     */
    protected function _expireAjax()
    {
        return $this->_traitExpireAjax();
    }

    /**
     * Get one page checkout model
     *
     * @return \Cart2Quote\Quotation\Model\Quote\CreateQuote
     */
    public function getOnepage()
    {
        return $this->traitGetOnepage();
    }

    /**
     * Get event prefix
     *
     * @return string
     */
    public function getEventPrefix()
    {
        return $this->traitGetEventPrefix();
    }

    /**
     * Overwrite this function to perform an ajax action on the RFQ page.
     *
     * @return bool
     */
    public function processAction()
    {
        return $this->traitProcessAction();
    }

    /**
     * Checking customer login status
     *
     * @return bool
     */
    public function isCustomerLoggedIn()
    {
        return $this->traitIsCustomerLoggedIn();
    }

    /**
     * Update the fields from the quotation data on the session.
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function updateQuotationProductData()
    {
        $this->traitUpdateQuotationProductData();
    }

    /**
     * Overwrite of \Magento\Checkout\Controller\Action::_preDispatchValidateCustomer
     * - We don't need customer validation in our ajax calls, we validate the customer at a later point.
     *
     * @param bool $redirect
     * @param bool $addErrors
     * @return bool|\Magento\Framework\Controller\Result\Redirect
     */
    protected function _preDispatchValidateCustomer($redirect = false, $addErrors = false)
    {
        return $this->_traitPreDispatchValidateCustomer($redirect, $addErrors);
    }
}
