<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteFactory;

/**
 * Class QuoteCartManagement
 */
class QuoteCartManagement implements \Cart2Quote\Quotation\Api\QuoteCartManagementInterface
{
    use \Cart2Quote\Features\Traits\Model\QuoteCartManagement {
        getQuoteCartForCustomer as private traitGetQuoteCartForCustomer;
        createEmptyQuoteCartForCustomer as private traitCreateEmptyQuoteCartForCustomer;
        createCustomerCart as private traitCreateCustomerCart;
        _prepareCustomerQuote as private _traitPrepareCustomerQuote;
        prepareShippingAddress as private traitPrepareShippingAddress;
        prepareBillingAddress as private traitPrepareBillingAddress;
        requestQuote as private traitRequestQuote;
    }

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Cart2Quote\Quotation\Api\QuoteRepositoryInterface
     */
    protected $quotationRepository;

    /**
     * @var QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    private $addressRepository;

    /**
     * @var array
     */
    private $addressesToSync = [];

    /**
     * QuoteCartManagement constructor.
     *
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Cart2Quote\Quotation\Api\QuoteRepositoryInterface $quotationRepository
     * @param \Magento\Quote\Model\QuoteFactory  $quoteFactory
     * @param \Cart2Quote\Quotation\Model\QuoteFactory $quotationFactory
     * @param \Cart2Quote\Quotation\Model\Session $quoteSession
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteRequestSender $sender
     * @param \Magento\Customer\Api\AddressRepositoryInterface|null $addressRepository
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Cart2Quote\Quotation\Api\QuoteRepositoryInterface $quotationRepository,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Cart2Quote\Quotation\Model\QuoteFactory $quotationFactory,
        \Cart2Quote\Quotation\Model\Session $quoteSession,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteRequestSender $sender,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository = null
    ) {
        $this->storeManager = $storeManager;
        $this->quoteRepository = $quoteRepository;
        $this->quotationRepository = $quotationRepository;
        $this->customerRepository = $customerRepository;
        $this->quoteFactory = $quoteFactory;
        $this->quotationFactory = $quotationFactory;
        $this->quoteSession = $quoteSession;
        $this->sender = $sender;
        $this->addressRepository = $addressRepository ?: ObjectManager::getInstance()
            ->get(\Magento\Customer\Api\AddressRepositoryInterface::class);
    }

    /**
     * Returns information for the quote cart for a specified customer.
     *
     * @param int $customerId
     * @return \Cart2Quote\Quotation\Api\Data\QuoteCartInterface|\Magento\Quote\Api\Data\CartInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getQuoteCartForCustomer($customerId)
    {
        return $this->traitGetQuoteCartForCustomer($customerId);
    }

    /**
     * Creates an empty quote cart for a specified customer.
     *
     * @param int $customerId
     * @param int $storeId
     * @return int
     * @throws CouldNotSaveException
     */
    public function createEmptyQuoteCartForCustomer($customerId, $storeId)
    {
        return $this->traitCreateEmptyQuoteCartForCustomer($customerId, $storeId);
    }

    /**
     * Creates a cart for the currently logged-in customer.
     *
     * @param int $customerId
     * @param int $storeId
     * @return \Magento\Quote\Model\Quote Cart object.
     * @throws CouldNotSaveException The cart could not be created.
     */
    protected function createCustomerCart($customerId, $storeId)
    {
        return $this->traitCreateCustomerCart($customerId, $storeId);
    }

    /**
     * Prepare address for customer quote.
     *
     * @param Quote $quote
     * @return void
     */
    protected function _prepareCustomerQuote($quote)
    {
        $this->_traitPrepareCustomerQuote($quote);
    }

    /**
     * Prepare shipping address for customer quote
     *
     * @param $quote
     * @param $customer
     * @param $shipping
     * @param $billing
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function prepareShippingAddress($quote, $customer, $shipping, $billing)
    {
        $this->traitPrepareShippingAddress($quote, $customer, $shipping, $billing);
    }

    /**
     * Prepare billing address for customer quote
     *
     * @param $quote
     * @param $customer
     * @param $billing
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function prepareBillingAddress($quote, $customer, $billing)
    {
        $this->traitPrepareBillingAddress($quote, $customer, $billing);
    }

    /**
     * Request Quote
     *
     * @param int $customerId
     * @return string response
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function requestQuote($customerId)
    {
        return $this->traitRequestQuote($customerId);
    }
}
