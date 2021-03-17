<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model;

/**
 * Class Session
 *
 * @package Cart2Quote\Quotation\Model
 */
class Session extends \Magento\Checkout\Model\Session
{

    use \Cart2Quote\Features\Traits\Model\Session {
        loadCustomerQuote as private traitLoadCustomerQuote;
        getQuote as private traitGetQuote;
        loadProductComments as private traitLoadProductComments;
        fullSessionClear as private traitFullSessionClear;
        clearQuote as private traitClearQuote;
        updateLastQuote as private traitUpdateLastQuote;
        addGuestFieldData as private traitAddGuestFieldData;
        addMergeData as private traitAddMergeData;
        addConfigData as private traitAddConfigData;
        addProductData as private traitAddProductData;
        addFieldData as private traitAddFieldData;
        setQuotationQuote as private traitSetQuotationQuote;
        getQuotationQuote as private traitGetQuotationQuote;
        setSkipLoadCustomer as private traitSetSkipLoadCustomer;
    }

    const QUOTATION_GUEST_FIELD_DATA = 'quotation_guest_field_data';
    const QUOTATION_FIELD_DATA = 'quotation_field_data';
    const QUOTATION_PRODUCT_DATA = 'quotation_product_data';
    const QUOTATION_STORE_CONFIG_DATA = 'quotation_store_config_data';

    protected $quoteResourceModel;

    /**
     * Skip load customer quote when coming from autologin
     *
     * @var bool
     */
    protected $skipLoadCustomerQuote = false;

    /**
     * Session constructor.
     *
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\Session\SidResolverInterface $sidResolver
     * @param \Magento\Framework\Session\Config\ConfigInterface $sessionConfig
     * @param \Magento\Framework\Session\SaveHandlerInterface $saveHandler
     * @param \Magento\Framework\Session\ValidatorInterface $validator
     * @param \Magento\Framework\Session\StorageInterface $storage
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \Magento\Quote\Model\ResourceModel\Quote $quoteResourceModel
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Session\SidResolverInterface $sidResolver,
        \Magento\Framework\Session\Config\ConfigInterface $sessionConfig,
        \Magento\Framework\Session\SaveHandlerInterface $saveHandler,
        \Magento\Framework\Session\ValidatorInterface $validator,
        \Magento\Framework\Session\StorageInterface $storage,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Framework\App\State $appState,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Quote\Model\ResourceModel\Quote $quoteResourceModel
    ) {
        $this->quoteResourceModel = $quoteResourceModel;
        parent::__construct(
            $request,
            $sidResolver,
            $sessionConfig,
            $saveHandler,
            $validator,
            $storage,
            $cookieManager,
            $cookieMetadataFactory,
            $appState,
            $orderFactory,
            $customerSession,
            $quoteRepository,
            $remoteAddress,
            $eventManager,
            $storeManager,
            $customerRepository,
            $quoteIdMaskFactory,
            $quoteFactory
        );
    }

    /**
     * Load data for customer quote and merge with current quote
     *
     * @return $this
     */
    public function loadCustomerQuote()
    {
        return $this->traitLoadCustomerQuote();
    }

    /**
     * Get quotation quote instance by current session
     *
     * @return Quote
     */
    public function getQuote()
    {
        return $this->traitGetQuote();
    }

    /**
     * Load item comments from the database and store it to the session
     */
    public function loadProductComments()
    {
        $this->traitLoadProductComments();
    }

    /**
     * Clear the Quotation Quote from the session.
     *
     * @return $this
     */
    public function fullSessionClear()
    {
        return $this->traitFullSessionClear();
    }

    /**
     * Destroy/end a session
     * -Unset all data associated with object
     *
     * @return $this
     */
    public function clearQuote()
    {
        return $this->traitClearQuote();
    }

    /**
     * Update the quote ID's and status on the session
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quotation
     * @return $this
     */
    public function updateLastQuote(\Cart2Quote\Quotation\Model\Quote $quotation)
    {
        return $this->traitUpdateLastQuote($quotation);
    }

    /**
     * Add config to the session
     * - Note: This data will be available on the RFQ page in as JSON data
     *
     * @param array $config
     * @return $this
     */
    public function addGuestFieldData(array $config)
    {
        return $this->traitAddGuestFieldData($config);
    }

    /**
     * Merge Data
     *
     * @param string $type
     * @param array $newConfig
     * @return $this
     */
    public function addMergeData($type, $newConfig)
    {
        return $this->traitAddMergeData($type, $newConfig);
    }

    /**
     * Add config to the session
     * - Note: This data will be available on the RFQ page in as JSON data
     *
     * @param array $config
     * @return $this
     */
    public function addConfigData(array $config)
    {
        return $this->traitAddConfigData($config);
    }

    /**
     * Add product data to the session
     * - Note: This data will be available on the RFQ page in as JSON data
     *
     * @param array $config
     * @return $this
     */
    public function addProductData(array $config)
    {
        return $this->traitAddProductData($config);
    }

    /**
     * Add field data to the session
     * - Note: This data will be available on the RFQ page in as JSON data
     *
     * @param array $config
     * @return $this
     */
    public function addFieldData(array $config)
    {
        return $this->traitAddFieldData($config);
    }

    /**
     * Set quotation quote to quotation session
     *
     * @param boolean $isQuotationQuote
     */
    public function setQuotationQuote($isQuotationQuote)
    {
        $this->traitSetQuotationQuote($isQuotationQuote);
    }

    /**
     * Get quotation quote from quotation session
     *
     * @return boolean|null
     */
    public function getQuotationQuote()
    {
        return $this->traitGetQuotationQuote();
    }

    /**
     * Set skip load customer
     *
     * @param boolean $skip
     */
    public function setSkipLoadCustomer($skip)
    {
        $this->traitSetSkipLoadCustomer($skip);
    }
}
