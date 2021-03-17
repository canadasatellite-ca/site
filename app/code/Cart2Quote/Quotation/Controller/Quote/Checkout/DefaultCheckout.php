<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\Quote\Checkout;

/**
 * Class DefaultCheckout
 *
 * @package Cart2Quote\Quotation\Controller\Quote\Checkout
 */
abstract class DefaultCheckout extends \Cart2Quote\Quotation\Controller\Quote
{
    use \Cart2Quote\Features\Traits\Controller\Quote\Checkout\DefaultCheckout {
        isAutoLogin as private traitIsAutoLogin;
        isAutoConfirm as private traitIsAutoConfirm;
        proceedToCheckout as private traitProceedToCheckout;
        proceedToAcceptQuotation as private traitProceedToAcceptQuotation;
        initCheckoutQuote as private traitInitCheckoutQuote;
        getStoreId as private traitGetStoreId;
        prepareQuotationQuote as private traitPrepareQuotationQuote;
        deletePreviousAcceptedQuotes as private traitDeletePreviousAcceptedQuotes;
        beforeSetCheckoutQuote as private traitBeforeSetCheckoutQuote;
        saveCheckoutQuoteAsQuotationQuote as private traitSaveCheckoutQuoteAsQuotationQuote;
        useGuestCheckout as private traitUseGuestCheckout;
        processGuestCustomerData as private traitProcessGuestCustomerData;
        processShipping as private traitProcessShipping;
        deleteCurrentCheckoutSessionQuote as private traitDeleteCurrentCheckoutSessionQuote;
        placeCheckoutQuote as private traitPlaceCheckoutQuote;
        redirectToCheckout as private traitRedirectToCheckout;
        initQuote as private traitInitQuote;
        hasValidHash as private traitHasValidHash;
        autoLogin as private traitAutoLogin;
        defaultRedirect as private traitDefaultRedirect;
        alternativeCheckout as private traitAlternativeCheckout;
        isGuest as private traitIsGuest;
        isSameCustomer as private traitIsSameCustomer;
    }

    /**
     * Config Path to Alternative Checkout setting
     */
    const CONFIG_PATH_ENABLE_ALTERNATIVE_CHECKOUT = "cart2quote_advanced/checkout/enable_alternative_checkout";

    /**
     * Config Path to Alternative Checkout Url
     */
    const CONFIG_PATH_ALTERNATIVE_CHECKOUT_URL = "cart2quote_advanced/checkout/alternative_checkout_url";
    /**
     * Quote Repository
     *
     * @var \Magento\Quote\Model\QuoteRepository
     */
    protected $quoteRepository;
    /**
     * Checkout session
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;
    /**
     * Mage Quote Factory
     *
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $mageQuoteFactory;
    /**
     * Data helper
     *
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $helper;
    /**
     * Customer Session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
    /**
     * Quotation Quote
     *
     * @var \Cart2Quote\Quotation\Model\Quote
     */
    protected $quote;
    /**
     * Checkout Quote
     *
     * @var \Magento\Quote\Model\Quote
     */
    protected $checkoutQuote;
    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory
     */
    protected $quoteCollectionFactory;
    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteProposalAcceptedSender
     */
    protected $quoteProposalAcceptedSender;

    /**
     * DefaultCheckout constructor
     *
     * @param \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory $quoteCollectionFactory
     * @param \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteProposalAcceptedSender $quoteProposalAcceptedSender
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Cart2Quote\Quotation\Model\QuotationCart $cart
     * @param \Cart2Quote\Quotation\Model\Session $quotationSession
     * @param \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Quote\Model\QuoteFactory $mageQuoteFactory
     * @param \Cart2Quote\Quotation\Helper\Data $helper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory $quoteCollectionFactory,
        \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteProposalAcceptedSender $quoteProposalAcceptedSender,
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Cart2Quote\Quotation\Model\QuotationCart $cart,
        \Cart2Quote\Quotation\Model\Session $quotationSession,
        \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Quote\Model\QuoteFactory $mageQuoteFactory,
        \Cart2Quote\Quotation\Helper\Data $helper,
        \Magento\Customer\Model\Session $customerSession,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->customerSession = $customerSession;
        $this->helper = $helper;
        $this->quoteRepository = $quoteRepository;
        $this->checkoutSession = $checkoutSession;
        $this->mageQuoteFactory = $mageQuoteFactory;

        parent::__construct(
            $context,
            $scopeConfig,
            $storeManager,
            $formKeyValidator,
            $cart,
            $quotationSession,
            $quoteFactory,
            $resultPageFactory,
            $logger
        );

        $this->quoteCollectionFactory = $quoteCollectionFactory;
        $this->quoteProposalAcceptedSender = $quoteProposalAcceptedSender;
    }

    /**
     * Checks if auto login is allowed
     *
     * @return bool
     */
    public function isAutoLogin()
    {
        return $this->traitIsAutoLogin();
    }

    /**
     * Checks if auto confirm is allowed
     *
     * @return bool
     */
    public function isAutoConfirm()
    {
        return $this->traitIsAutoConfirm();
    }

    /**
     * Proceed to checkout
     *
     * @param bool $guest
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    protected function proceedToCheckout($guest = false)
    {
        return $this->traitProceedToCheckout($guest);
    }

    /**
     * Accept proposal
     *
     * @param bool $guest
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    protected function proceedToAcceptQuotation($guest = false)
    {
        return $this->traitProceedToAcceptQuotation($guest);
    }

    /**
     * Initialize the checkout quote
     *
     * @return $this
     */
    protected function initCheckoutQuote()
    {
        return $this->traitInitCheckoutQuote();
    }

    /**
     * Get store id
     *
     * @return int
     */
    protected function getStoreId()
    {
        return $this->traitGetStoreId();
    }

    /**
     * Prepare the quotation quote:
     *  Set state to complete
     *  Set status to accepted
     *  Set link to order
     *
     * @return $this
     */
    protected function prepareQuotationQuote()
    {
        return $this->traitPrepareQuotationQuote();
    }

    /**
     * Delete previously accepted quotes if they have same linked quotation id
     */
    protected function deletePreviousAcceptedQuotes()
    {
        $this->traitDeletePreviousAcceptedQuotes();
    }

    /**
     * Prepare the checkout quote
     * - Further configuration of a new Quote and making it as a copy of approved & accepted C2Q_Quote object
     *
     * @return $this
     */
    protected function beforeSetCheckoutQuote()
    {
        return $this->traitBeforeSetCheckoutQuote();
    }

    /**
     * Prepare the checkout quote and save it as quotation quote.
     * - Transform a new Quote object into a copy of approved & accepted C2Q_Quote object
     *
     * @return $this
     */
    protected function saveCheckoutQuoteAsQuotationQuote()
    {
        return $this->traitSaveCheckoutQuoteAsQuotationQuote();
    }

    /**
     * Use the checkout quote as guest checkout
     *
     * @return $this
     */
    protected function useGuestCheckout()
    {
        return $this->traitUseGuestCheckout();
    }

    /**
     * Process the customer data from the quotation quote to the checkout quote.
     * - Default copy functions do not copy this data.
     *
     * @return $this
     */
    protected function processGuestCustomerData()
    {
        return $this->traitProcessGuestCustomerData();
    }

    /**
     * Process shipping
     *
     * @return void
     */
    protected function processShipping()
    {
        $this->traitProcessShipping();
    }

    /**
     * Replace a current customer quote with it and remove the old one
     * - so customer will be able to place an Order with a new one
     *
     * @return $this
     */
    protected function deleteCurrentCheckoutSessionQuote()
    {
        return $this->traitDeleteCurrentCheckoutSessionQuote();
    }

    /**
     * Place the checkout quote in the checkout session
     *
     * @return $this
     */
    protected function placeCheckoutQuote()
    {
        return $this->traitPlaceCheckoutQuote();
    }

    /**
     * Redirect to checkout
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    protected function redirectToCheckout()
    {
        return $this->traitRedirectToCheckout();
    }

    /**
     * Initialize the quotation quote
     *
     * @return $this
     */
    protected function initQuote()
    {
        return $this->traitInitQuote();
    }

    /**
     * Check if the hash is valid
     *
     * @return bool
     */
    protected function hasValidHash()
    {
        return $this->traitHasValidHash();
    }

    /**
     * Login by customer id set on the quote
     *
     * @return $this
     */
    protected function autoLogin()
    {
        return $this->traitAutoLogin();
    }

    /**
     * Redirect to quote page if the quote exists
     * - Redirect to index page if the quote does not exists
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    protected function defaultRedirect()
    {
        return $this->traitDefaultRedirect();
    }

    /**
     * Get the alyernative checkout path and set it to the redirect
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    protected function alternativeCheckout()
    {
        return $this->traitAlternativeCheckout();
    }

    /**
     * Checks if a customer is a guest
     *
     * @return bool
     */
    protected function isGuest()
    {
        return $this->traitIsGuest();
    }

    /**
     * Checks if the customer is the same
     *
     * @return bool
     */
    protected function isSameCustomer()
    {
        return $this->traitIsSameCustomer();
    }
}
