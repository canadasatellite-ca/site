<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model;

use Magento\Catalog\Model\Product;

/**
 * Quote model
 * Supported events:
 *  sales_quote_load_after
 *  sales_quote_save_before
 *  sales_quote_save_after
 *  sales_quote_delete_before
 *  sales_quote_delete_after
 * Class Quote
 * @package Cart2Quote\Quotation\Model
 */
class Quote extends \Magento\Quote\Model\Quote implements
    \Cart2Quote\Quotation\Model\EntityInterface,
    \Magento\Sales\Model\EntityInterface,
    \Cart2Quote\Quotation\Api\Data\QuoteInterface
{
    use \Cart2Quote\Features\Traits\Model\Quote {
        getQuotationCreatedBy as private traitGetQuotationCreatedBy;
        setQuotationCreatedBy as private traitSetQuotationCreatedBy;
        setRejectMessage as private traitSetRejectMessage;
        getShippingMethod as private traitGetShippingMethod;
        setSendRequestEmail as private traitSetSendRequestEmail;
        getSendRequestEmail as private traitGetSendRequestEmail;
        setRequestEmailSent as private traitSetRequestEmailSent;
        getRequestEmailSent as private traitGetRequestEmailSent;
        setSendQuoteCanceledEmail as private traitSetSendQuoteCanceledEmail;
        getSendQuoteCanceledEmail as private traitGetSendQuoteCanceledEmail;
        setQuoteCanceledEmailSent as private traitSetQuoteCanceledEmailSent;
        getQuoteCanceledEmailSent as private traitGetQuoteCanceledEmailSent;
        setSendQuoteEditedEmail as private traitSetSendQuoteEditedEmail;
        getSendQuoteEditedEmail as private traitGetSendQuoteEditedEmail;
        setQuoteEditedEmailSent as private traitSetQuoteEditedEmailSent;
        getQuoteEditedEmailSent as private traitGetQuoteEditedEmailSent;
        setSendProposalAcceptedEmail as private traitSetSendProposalAcceptedEmail;
        getSendProposalAcceptedEmail as private traitGetSendProposalAcceptedEmail;
        setProposalAcceptedEmailSent as private traitSetProposalAcceptedEmailSent;
        getProposalAcceptedEmailSent as private traitGetProposalAcceptedEmailSent;
        setSendProposalExpiredEmail as private traitSetSendProposalExpiredEmail;
        getSendProposalExpiredEmail as private traitGetSendProposalExpiredEmail;
        setProposalExpiredEmailSent as private traitSetProposalExpiredEmailSent;
        getProposalExpiredEmailSent as private traitGetProposalExpiredEmailSent;
        setSendProposalEmail as private traitSetSendProposalEmail;
        getSendProposalEmail as private traitGetSendProposalEmail;
        setProposalEmailSent as private traitSetProposalEmailSent;
        getProposalEmailSent as private traitGetProposalEmailSent;
        setSendReminderEmail as private traitSetSendReminderEmail;
        getSendReminderEmail as private traitGetSendReminderEmail;
        setReminderEmailSent as private traitSetReminderEmailSent;
        getReminderEmailSent as private traitGetReminderEmailSent;
        getProposalSent as private traitGetProposalSent;
        getFixedShippingPrice as private traitGetFixedShippingPrice;
        setFixedShippingPrice as private traitSetFixedShippingPrice;
        create as private traitCreate;
        setStatus as private traitSetStatus;
        setState as private traitSetState;
        getConfig as private traitGetConfig;
        setOriginalSubtotal as private traitSetOriginalSubtotal;
        setBaseOriginalSubtotal as private traitSetBaseOriginalSubtotal;
        setOriginalSubtotalInclTax as private traitSetOriginalSubtotalInclTax;
        setBaseOriginalSubtotalInclTax as private traitSetBaseOriginalSubtotalInclTax;
        getOriginalSubtotalInclTax as private traitGetOriginalSubtotalInclTax;
        getBaseOriginalSubtotalInclTax as private traitGetBaseOriginalSubtotalInclTax;
        getDefaultExpiryDate as private traitGetDefaultExpiryDate;
        getDefaultReminderDate as private traitGetDefaultReminderDate;
        save as private traitSave;
        setRecollect as private traitSetRecollect;
        recollectQuote as private traitRecollectQuote;
        recalculateOriginalSubtotal as private traitRecalculateOriginalSubtotal;
        getOriginalPriceInclTax as private traitGetOriginalPriceInclTax;
        getBaseOriginalPriceInclTax as private traitGetBaseOriginalPriceInclTax;
        getOriginalTaxAmount as private traitGetOriginalTaxAmount;
        getBaseOriginalTaxAmount as private traitGetBaseOriginalTaxAmount;
        formatSubtotal as private traitFormatSubtotal;
        isChildProduct as private traitIsChildProduct;
        convertPriceToQuoteCurrency as private traitConvertPriceToQuoteCurrency;
        isCurrencyDifferent as private traitIsCurrencyDifferent;
        convertRate as private traitConvertRate;
        recalculateCustomPriceTotal as private traitRecalculateCustomPriceTotal;
        priceIncludesTax as private traitPriceIncludesTax;
        subtotalIncludesTax as private traitSubtotalIncludesTax;
        setCustomPriceTotal as private traitSetCustomPriceTotal;
        setBaseCustomPriceTotal as private traitSetBaseCustomPriceTotal;
        recalculateQuoteAdjustmentTotal as private traitRecalculateQuoteAdjustmentTotal;
        getTaxAmount as private traitGetTaxAmount;
        getBaseTaxAmount as private traitGetBaseTaxAmount;
        getBaseOriginalSubtotal as private traitGetBaseOriginalSubtotal;
        getOriginalSubtotal as private traitGetOriginalSubtotal;
        setQuoteAdjustment as private traitSetQuoteAdjustment;
        setBaseQuoteAdjustment as private traitSetBaseQuoteAdjustment;
        removeTier as private traitRemoveTier;
        canEdit as private traitCanEdit;
        canCancel as private traitCanCancel;
        isCanceled as private traitIsCanceled;
        canHold as private traitCanHold;
        canUnhold as private traitCanUnhold;
        canComment as private traitCanComment;
        canChangeRequest as private traitCanChangeRequest;
        getAllStatusHistory as private traitGetAllStatusHistory;
        getStatusHistoryCollection as private traitGetStatusHistoryCollection;
        getVisibleStatusHistory as private traitGetVisibleStatusHistory;
        getStatusHistoryById as private traitGetStatusHistoryById;
        addStatusHistory as private traitAddStatusHistory;
        saveQuote as private traitSaveQuote;
        importPostData as private traitImportPostData;
        setShippingMethod as private traitSetShippingMethod;
        setPaymentMethod as private traitSetPaymentMethod;
        applyCoupon as private traitApplyCoupon;
        resetShippingMethod as private traitResetShippingMethod;
        collectShippingRates as private traitCollectShippingRates;
        collectRates as private traitCollectRates;
        setPaymentData as private traitSetPaymentData;
        initRuleData as private traitInitRuleData;
        setShippingAsBilling as private traitSetShippingAsBilling;
        addProducts as private traitAddProducts;
        checkProduct as private traitCheckProduct;
        addProduct as private traitAddProduct;
        getItemByProduct as private traitGetItemByProduct;
        getItemsByProduct as private traitGetItemsByProduct;
        updateBaseCustomPrice as private traitUpdateBaseCustomPrice;
        setSubtotalProposal as private traitSetSubtotalProposal;
        removeQuoteItem as private traitRemoveQuoteItem;
        removeQuotationItem as private traitRemoveQuotationItem;
        getItemsCollection as private traitGetItemsCollection;
        getStatusLabel as private traitGetStatusLabel;
        getStatus as private traitGetStatus;
        getStateLabel as private traitGetStateLabel;
        getState as private traitGetState;
        formatPrice as private traitFormatPrice;
        formatPricePrecision as private traitFormatPricePrecision;
        getQuoteCurrency as private traitGetQuoteCurrency;
        getQuoteCurrencyCode as private traitGetQuoteCurrencyCode;
        formatBasePrice as private traitFormatBasePrice;
        formatBasePricePrecision as private traitFormatBasePricePrecision;
        getBaseCurrency as private traitGetBaseCurrency;
        getAnyCurrency as private traitGetAnyCurrency;
        resetQuoteCurrency as private traitResetQuoteCurrency;
        formatPriceTxt as private traitFormatPriceTxt;
        getCustomerName as private traitGetCustomerName;
        getCreatedAtFormatted as private traitGetCreatedAtFormatted;
        getEmailCustomerNote as private traitGetEmailCustomerNote;
        getExpiryDateString as private traitGetExpiryDateString;
        getExpiryDateFormatted as private traitGetExpiryDateFormatted;
        setIncrementId as private traitSetIncrementId;
        setProposalSent as private traitSetProposalSent;
        canAccept as private traitCanAccept;
        showPrices as private traitShowPrices;
        getBaseCustomPriceTotal as private traitGetBaseCustomPriceTotal;
        getCustomPriceTotal as private traitGetCustomPriceTotal;
        getQuoteAdjustment as private traitGetQuoteAdjustment;
        getBaseQuoteAdjustment as private traitGetBaseQuoteAdjustment;
        getEntityType as private traitGetEntityType;
        getIncrementId as private traitGetIncrementId;
        getUrlHash as private traitGetUrlHash;
        getRandomHash as private traitGetRandomHash;
        convertPriceToQuoteBaseCurrency as private traitConvertPriceToQuoteBaseCurrency;
        convertShippingPrice as private traitConvertShippingPrice;
        getTotalItemQty as private traitGetTotalItemQty;
        getSections as private traitGetSections;
        getSectionItems as private traitGetSectionItems;
        _construct as private _traitConstruct;
        sortItemId as private traitSortItemId;
        sort as private traitSort;
        hasOptionalItems as private traitHasOptionalItems;
        getExtensionAttributes as private traitGetExtensionAttributes;
        assignToSection as private traitAssignToSection;
        setCustomer as private traitSetCustomer;
        getCustomerEmail as private traitGetCustomerEmail;
    }

    const ENTITY = 'quote';
    const EAV_ENTITY = 'quotation';
    const DEFAULT_EXPIRATION_TIME = 'cart2quote_quotation/global/default_expiration_time';
    const DEFAULT_REMINDER_TIME = 'cart2quote_quotation/global/default_reminder_time';
    const IS_QUOTE = 1;
    const IS_QUOTE_DELETED = 2;
    const ORIGINAL_QUOTE = 1;

    /**
     * Quotation was created by admin or user
     */
    const CREATED_BY = 'created_by';

    /**
     * Reason for rejection
     */
    const REJECT_MESSAGE = 'reject_message';

    /**
     * @var \Magento\Directory\Model\Currency
     */
    protected $anyCurrency;

    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Config
     */
    protected $_quoteConfig;

    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Status\HistoryFactory
     */
    protected $_quoteHistoryFactory;

    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\Status\History\CollectionFactory
     */
    protected $_historyCollectionFactory;

    /**
     * Re-collect quote flag
     * @var boolean
     */
    protected $_needCollect;

    /**
     * Quote session object
     *
     * @var \Magento\Backend\Model\Session\Quote
     */
    protected $_session;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Quote\Model\Quote\Item\Updater
     */
    protected $quoteItemUpdater;

    /**
     * @var \Magento\Quote\Model\QuoteRepository
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Directory\Model\Currency
     */
    protected $_quoteCurrency;

    /**
     * @var \Magento\Directory\Model\Currency
     */
    protected $_baseCurrency;

    /**
     * @var \Magento\Quote\Model\Cart\CurrencyFactory
     */
    protected $_currencyFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $datetime;

    /**
     * @var Quote\TierItemFactory
     */
    protected $_tierItemFactory;

    /**
     * @var ResourceModel\Quote\TierItem\CollectionFactory
     */
    protected $tierItemCollectionFactory;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $_quoteFactory;

    /**
     * @var \Magento\Sales\Model\Status
     */
    protected $_statusObject;

    /**
     * @var Quote\Item\Section\Provider
     */
    private $itemSectionProvider;

    /**
     * @var ResourceModel\Quote\Item\Section
     */
    private $itemSectionResourceModel;

    /**
     * @var ResourceModel\Quote\TierItem
     */
    private $tierItemResourceModel;

    /**
     * @var \Cart2Quote\Quotation\Helper\QuotationTaxHelper
     */
    protected $quotationTaxHelper;

    /**
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $quotationDataHelper;

    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\Section\CollectionFactory
     */
    protected $sectionCollectionFactory;

    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\Item\Section
     */
    private $sectionItemResourceModel;

    /**
     * @var \Cart2Quote\Quotation\Helper\StockCheck
     */
    private $stockCheck;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $appState;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;

    /**
     * Price currency
     *
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * Quote constructor
     *
     * @param \Cart2Quote\Quotation\Helper\StockCheck $stockCheck
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\Item\Section $sectionItemResourceModel
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\Section\CollectionFactory $sectionCollectionFactory
     * @param \Cart2Quote\Quotation\Helper\Data $quotationDataHelper
     * @param \Cart2Quote\Quotation\Helper\QuotationTaxHelper $quotationTaxHelper
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\Item\Section $itemSectionResourceModel
     * @param \Cart2Quote\Quotation\Model\Quote\Item\Section\Provider $itemSectionProvider
     * @param \Cart2Quote\Quotation\Model\Quote\TierItemFactory $tierItemFactory
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem $tierItemResourceModel
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\CollectionFactory $tierItemCollectionFactory
     * @param \Cart2Quote\Quotation\Model\Quote\Config $quoteConfig
     * @param \Cart2Quote\Quotation\Model\Quote\Status\HistoryFactory $quoteHistoryFactory
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\Status\History\CollectionFactory $historyCollectionFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Quote\Model\Quote\Item\Updater $quoteItemUpdater
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \Magento\Directory\Model\CurrencyFactory $directoryCurrencyFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Backend\Model\Session\Quote $quoteSession
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Magento\Quote\Model\QuoteValidator $quoteValidator
     * @param \Magento\Catalog\Helper\Product $catalogProduct
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Quote\Model\Quote\AddressFactory $quoteAddressFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Customer\Api\GroupRepositoryInterface $groupRepository
     * @param \Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory $quoteItemCollectionFactory
     * @param \Magento\Quote\Model\Quote\ItemFactory $quoteItemFactory
     * @param \Magento\Framework\Message\Factory $messageFactory
     * @param \Magento\Sales\Model\Status\ListFactory $statusListFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Quote\Model\Quote\PaymentFactory $quotePaymentFactory
     * @param \Magento\Quote\Model\ResourceModel\Quote\Payment\CollectionFactory $quotePaymentCollectionFactory
     * @param \Magento\Framework\DataObject\Copy $objectCopyService
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\Quote\Model\Quote\Item\Processor $itemProcessor
     * @param \Magento\Framework\DataObject\Factory $objectFactory
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $criteriaBuilder
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Magento\Customer\Api\Data\AddressInterfaceFactory $addressDataFactory
     * @param \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerDataFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param \Magento\Quote\Model\Cart\CurrencyFactory $currencyFactory
     * @param \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param \Magento\Quote\Model\Quote\TotalsCollector $totalsCollector
     * @param \Magento\Quote\Model\Quote\TotalsReader $totalsReader
     * @param \Magento\Quote\Model\ShippingFactory $shippingFactory
     * @param \Magento\Quote\Model\ShippingAssignmentFactory $shippingAssignmentFactory
     * @param \Cart2Quote\Quotation\Model\Quote\Status $statusObject
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Cart2Quote\Quotation\Helper\StockCheck $stockCheck,
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\Item\Section $sectionItemResourceModel,
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\Section\CollectionFactory $sectionCollectionFactory,
        \Cart2Quote\Quotation\Helper\Data $quotationDataHelper,
        \Cart2Quote\Quotation\Helper\QuotationTaxHelper $quotationTaxHelper,
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\Item\Section $itemSectionResourceModel,
        \Cart2Quote\Quotation\Model\Quote\Item\Section\Provider $itemSectionProvider,
        \Cart2Quote\Quotation\Model\Quote\TierItemFactory $tierItemFactory,
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem $tierItemResourceModel,
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\CollectionFactory $tierItemCollectionFactory,
        \Cart2Quote\Quotation\Model\Quote\Config $quoteConfig,
        \Cart2Quote\Quotation\Model\Quote\Status\HistoryFactory $quoteHistoryFactory,
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\Status\History\CollectionFactory $historyCollectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Quote\Model\Quote\Item\Updater $quoteItemUpdater,
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Directory\Model\CurrencyFactory $directoryCurrencyFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Backend\Model\Session\Quote $quoteSession,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Quote\Model\QuoteValidator $quoteValidator,
        \Magento\Catalog\Helper\Product $catalogProduct,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Quote\Model\Quote\AddressFactory $quoteAddressFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory $quoteItemCollectionFactory,
        \Magento\Quote\Model\Quote\ItemFactory $quoteItemFactory,
        \Magento\Framework\Message\Factory $messageFactory,
        \Magento\Sales\Model\Status\ListFactory $statusListFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Quote\Model\Quote\PaymentFactory $quotePaymentFactory,
        \Magento\Quote\Model\ResourceModel\Quote\Payment\CollectionFactory $quotePaymentCollectionFactory,
        \Magento\Framework\DataObject\Copy $objectCopyService,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Quote\Model\Quote\Item\Processor $itemProcessor,
        \Magento\Framework\DataObject\Factory $objectFactory,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $criteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Customer\Api\Data\AddressInterfaceFactory $addressDataFactory,
        \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerDataFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        \Magento\Quote\Model\Cart\CurrencyFactory $currencyFactory,
        \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor,
        \Magento\Quote\Model\Quote\TotalsCollector $totalsCollector,
        \Magento\Quote\Model\Quote\TotalsReader $totalsReader,
        \Magento\Quote\Model\ShippingFactory $shippingFactory,
        \Magento\Quote\Model\ShippingAssignmentFactory $shippingAssignmentFactory,
        \Cart2Quote\Quotation\Model\Quote\Status $statusObject,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $quoteValidator,
            $catalogProduct,
            $scopeConfig,
            $storeManager,
            $config,
            $quoteAddressFactory,
            $customerFactory,
            $groupRepository,
            $quoteItemCollectionFactory,
            $quoteItemFactory,
            $messageFactory,
            $statusListFactory,
            $productRepository,
            $quotePaymentFactory,
            $quotePaymentCollectionFactory,
            $objectCopyService,
            $stockRegistry,
            $itemProcessor,
            $objectFactory,
            $addressRepository,
            $criteriaBuilder,
            $filterBuilder,
            $addressDataFactory,
            $customerDataFactory,
            $customerRepository,
            $dataObjectHelper,
            $extensibleDataObjectConverter,
            $currencyFactory,
            $extensionAttributesJoinProcessor,
            $totalsCollector,
            $totalsReader,
            $shippingFactory,
            $shippingAssignmentFactory,
            $resource,
            $resourceCollection,
            $data
        );

        $this->timezone = $timezone;
        $this->datetime = $dateTime;
        $this->_quoteConfig = $quoteConfig;
        $this->_quoteHistoryFactory = $quoteHistoryFactory;
        $this->_historyCollectionFactory = $historyCollectionFactory;
        $this->_objectManager = $objectManager;
        $this->_session = $quoteSession;
        $this->_coreRegistry = $coreRegistry;
        $this->messageManager = $messageManager;
        $this->quoteItemUpdater = $quoteItemUpdater;
        $this->quoteRepository = $quoteRepository;
        $this->_quoteFactory = $quoteFactory;
        $this->_currencyFactory = $directoryCurrencyFactory;
        $this->_tierItemFactory = $tierItemFactory;
        $this->_statusObject = $statusObject;
        $this->tierItemCollectionFactory = $tierItemCollectionFactory;
        $this->itemSectionProvider = $itemSectionProvider;
        $this->itemSectionResourceModel = $itemSectionResourceModel;
        $this->tierItemResourceModel = $tierItemResourceModel;
        $this->quotationTaxHelper = $quotationTaxHelper;
        $this->quotationDataHelper = $quotationDataHelper;
        $this->appState = $context->getAppState();
        $this->authSession = $authSession;
        $this->sectionCollectionFactory = $sectionCollectionFactory;
        $this->sectionItemResourceModel = $sectionItemResourceModel;
        $this->stockCheck = $stockCheck;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * Gets the created_by value from quotation_quote
     *
     * @return string|null
     */
    public function getQuotationCreatedBy() {
        return $this->traitGetQuotationCreatedBy();
    }

    /**
     * Saves the created_by value to quotation_quote
     *
     * @param string $createdBy
     * @return Quote
     */
    public function setQuotationCreatedBy($createdBy)
    {
        return $this->traitSetQuotationCreatedBy($createdBy);
    }

    /**
     * Saves customer's reject message in the database
     *
     * @param string $reasonForRejection
     * @return $this
     */
    public function setRejectMessage($reasonForRejection)
    {
        return $this->traitSetRejectMessage($reasonForRejection);
    }

    /**
     * Retrieve current shipping method
     *
     * @return string
     */
    public function getShippingMethod()
    {
        return $this->traitGetShippingMethod();
    }

    /**
     * Set send request email
     *
     * @param bool $sendRequestEmail
     * @return $this
     */
    public function setSendRequestEmail($sendRequestEmail)
    {
        return $this->traitSetSendRequestEmail($sendRequestEmail);
    }

    /**
     * Get send request email
     *
     * @return $this
     */
    public function getSendRequestEmail()
    {
        return $this->traitGetSendRequestEmail();
    }

    /**
     * Set request email sent
     *
     * @param bool $requestEmailSent
     * @return $this
     */
    public function setRequestEmailSent($requestEmailSent)
    {
        return $this->traitSetRequestEmailSent($requestEmailSent);
    }

    /**
     * Get request email send
     *
     * @return $this
     */
    public function getRequestEmailSent()
    {
        return $this->traitGetRequestEmailSent();
    }

    /**
     * Set send quote canceled email
     *
     * @param bool $sendQuoteCanceledEmail
     * @return $this
     */
    public function setSendQuoteCanceledEmail($sendQuoteCanceledEmail)
    {
        return $this->traitSetSendQuoteCanceledEmail($sendQuoteCanceledEmail);
    }

    /**
     * Get send quote canceled email
     *
     * @return $this
     */
    public function getSendQuoteCanceledEmail()
    {
        return $this->traitGetSendQuoteCanceledEmail();
    }

    /**
     * Set quote canceled email sent
     *
     * @param bool $quoteCanceledEmailSent
     * @return $this
     */
    public function setQuoteCanceledEmailSent($quoteCanceledEmailSent)
    {
        return $this->traitSetQuoteCanceledEmailSent($quoteCanceledEmailSent);
    }

    /**
     * Get quote canceled email sent
     *
     * @return $this
     */
    public function getQuoteCanceledEmailSent()
    {
        return $this->traitGetQuoteCanceledEmailSent();
    }

    /**
     * Set send quote edited email
     *
     * @param boolean $sendQuoteEditedEmail
     * @return $this
     */
    public function setSendQuoteEditedEmail($sendQuoteEditedEmail)
    {
        return $this->traitSetSendQuoteEditedEmail($sendQuoteEditedEmail);
    }

    /**
     * Get send quote edited email
     *
     * @return boolean
     */
    public function getSendQuoteEditedEmail()
    {
        return $this->traitGetSendQuoteEditedEmail();
    }

    /**
     * Set quote edited email sent
     *
     * @param boolean $quoteEditedEmailSent
     * @return $this
     */
    public function setQuoteEditedEmailSent($quoteEditedEmailSent)
    {
        return $this->traitSetQuoteEditedEmailSent($quoteEditedEmailSent);
    }

    /**
     * Get quote edited email sent
     *
     * @return boolean
     */
    public function getQuoteEditedEmailSent()
    {
        return $this->traitGetQuoteEditedEmailSent();
    }

    /**
     * Set send proposal accepted email
     *
     * @param bool $sendProposalAcceptedEmail
     * @return $this
     */
    public function setSendProposalAcceptedEmail($sendProposalAcceptedEmail)
    {
        return $this->traitSetSendProposalAcceptedEmail($sendProposalAcceptedEmail);
    }

    /**
     * Get send proposal accepted email
     *
     * @return $this
     */
    public function getSendProposalAcceptedEmail()
    {
        return $this->traitGetSendProposalAcceptedEmail();
    }

    /**
     * Set proposal accepted email sent
     *
     * @param bool $proposalAcceptedEmailSent
     * @return $this
     */
    public function setProposalAcceptedEmailSent($proposalAcceptedEmailSent)
    {
        return $this->traitSetProposalAcceptedEmailSent($proposalAcceptedEmailSent);
    }

    /**
     * Get proposal accepted email sent
     *
     * @return $this
     */
    public function getProposalAcceptedEmailSent()
    {
        return $this->traitGetProposalAcceptedEmailSent();
    }

    /**
     * Set send proposal expired email
     *
     * @param bool $sendProposalExpiredEmail
     * @return $this
     */
    public function setSendProposalExpiredEmail($sendProposalExpiredEmail)
    {
        return $this->traitSetSendProposalExpiredEmail($sendProposalExpiredEmail);
    }

    /**
     * Get send proposal expired email
     *
     * @return $this
     */
    public function getSendProposalExpiredEmail()
    {
        return $this->traitGetSendProposalExpiredEmail();
    }

    /**
     * Set propozal expired email sent
     *
     * @param bool $proposalExpiredEmailSent
     * @return $this
     */
    public function setProposalExpiredEmailSent($proposalExpiredEmailSent)
    {
        return $this->traitSetProposalExpiredEmailSent($proposalExpiredEmailSent);
    }

    /**
     * Get propsal expired email sent
     *
     * @return $this
     */
    public function getProposalExpiredEmailSent()
    {
        return $this->traitGetProposalExpiredEmailSent();
    }

    /**
     * Set send proposal email
     *
     * @param bool $sendProposalEmail
     * @return $this
     */
    public function setSendProposalEmail($sendProposalEmail)
    {
        return $this->traitSetSendProposalEmail($sendProposalEmail);
    }

    /**
     * Get send proposal email
     *
     * @return $this
     */
    public function getSendProposalEmail()
    {
        return $this->traitGetSendProposalEmail();
    }

    /**
     * Set proposal email sent
     *
     * @param bool $proposalEmailSent
     * @return $this
     */
    public function setProposalEmailSent($proposalEmailSent)
    {
        return $this->traitSetProposalEmailSent($proposalEmailSent);
    }

    /**
     * Get proposal email sent
     *
     * @return $this
     */
    public function getProposalEmailSent()
    {
        return $this->traitGetProposalEmailSent();
    }

    /**
     * Set send reminder email
     *
     * @param bool $sendReminderEmail
     * @return $this
     */
    public function setSendReminderEmail($sendReminderEmail)
    {
        return $this->traitSetSendReminderEmail($sendReminderEmail);
    }

    /**
     * Get send reminder email
     *
     * @return $this
     */
    public function getSendReminderEmail()
    {
        return $this->traitGetSendReminderEmail();
    }

    /**
     * Set reminder email sent
     *
     * @param bool $reminderEmailSent
     * @return $this
     */
    public function setReminderEmailSent($reminderEmailSent)
    {
        return $this->traitSetReminderEmailSent($reminderEmailSent);
    }

    /**
     * Get reminder email sent
     *
     * @return $this
     */
    public function getReminderEmailSent()
    {
        return $this->traitGetReminderEmailSent();
    }

    /**
     * Get proposal sent
     *
     * @return string
     */
    public function getProposalSent()
    {
        return $this->traitGetProposalSent();
    }

    /**
     * Get fixed shipping price
     *
     * @return float
     */
    public function getFixedShippingPrice()
    {
        return $this->traitGetFixedShippingPrice();
    }

    /**
     * Set fixed shipping price
     *
     * @param float $fixedShippingPrice
     * @return $this
     */
    public function setFixedShippingPrice($fixedShippingPrice)
    {
        return $this->traitSetFixedShippingPrice($fixedShippingPrice);
    }

    /**
     * Create
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @return $this
     */
    public function create(\Magento\Quote\Model\Quote $quote)
    {
        return $this->traitCreate($quote);
    }

    /**
     * Sets the status for the quote.
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status)
    {
        return $this->traitSetStatus($status);
    }

    /**
     * Sets the state for the quote.
     *
     * @param string $state
     * @return $this
     */
    public function setState($state)
    {
        return $this->traitSetState($state);
    }

    /**
     * Retrieve quote configuration model
     *
     * @return Quote\Config
     */
    public function getConfig()
    {
        return $this->traitGetConfig();
    }

    /**
     * Set original subtotal
     *
     * @param float $originalSubtotal
     * @return $this
     */
    public function setOriginalSubtotal($originalSubtotal)
    {
        return $this->traitSetOriginalSubtotal($originalSubtotal);
    }

    /**
     * Set Base Original Subtotal
     *
     * @param float $originalBaseSubtotal
     * @return $this
     */
    public function setBaseOriginalSubtotal($originalBaseSubtotal)
    {
        return $this->traitSetBaseOriginalSubtotal($originalBaseSubtotal);
    }

    /**
     * Set Base Original Subtotal Incl Tax
     *
     * @param float $originalSubtotalInclTax
     * @return $this
     */
    public function setOriginalSubtotalInclTax($originalSubtotalInclTax)
    {
        return $this->traitSetOriginalSubtotalInclTax($originalSubtotalInclTax);
    }

    /**
     * Set Base Original Subtotal Incl Tax
     *
     * @param float $originalBaseSubtotalInclTax
     * @return $this
     */
    public function setBaseOriginalSubtotalInclTax($originalBaseSubtotalInclTax)
    {
        return $this->traitSetBaseOriginalSubtotalInclTax($originalBaseSubtotalInclTax);
    }

    /**
     * Get Original Subtotal Incl Tax
     *
     * @return float
     */
    public function getOriginalSubtotalInclTax()
    {
        return $this->traitGetOriginalSubtotalInclTax();
    }

    /**
     * Get Base Original Subtotal Incl Tax
     *
     * @return float
     */
    public function getBaseOriginalSubtotalInclTax()
    {
        return $this->traitGetBaseOriginalSubtotalInclTax();
    }

    /**
     * Get default expiry date of quote
     *
     * @return date
     */
    public function getDefaultExpiryDate()
    {
        return $this->traitGetDefaultExpiryDate();
    }

    /**
     * Get default reminder date of quote
     *
     * @return date
     */
    public function getDefaultReminderDate()
    {
        return $this->traitGetDefaultReminderDate();
    }

    /**
     * Save quote data
     *
     * @return $this
     * @throws \Exception
     */
    public function save()
    {
        return $this->traitSave();
    }

    /**
     * Set collect totals flag for quote
     *
     * @param bool $flag
     * @return $this
     */
    public function setRecollect($flag)
    {
        return $this->traitSetRecollect($flag);
    }

    /**
     * Recollect totals for customer cart.
     * - Set recollect totals flag for quote
     *
     * @return $this
     */
    public function recollectQuote()
    {
        return $this->traitRecollectQuote();
    }

    /**
     * Function that recalculates the new original subtotal
     *
     * @return $this
     */
    public function recalculateOriginalSubtotal()
    {
        return $this->traitRecalculateOriginalSubtotal();
    }

    /**
     * Get original price including tax
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param float $price
     * @return float|int
     */
    public function getOriginalPriceInclTax($item, $price)
    {
        return $this->traitGetOriginalPriceInclTax($item, $price);
    }

    /**
     * Get base original price including tax
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param float $basePrice
     * @return float|int
     */
    public function getBaseOriginalPriceInclTax($item, $basePrice)
    {
        return $this->traitGetBaseOriginalPriceInclTax($item, $basePrice);
    }

    /**
     * Get original tax amount
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param float $price
     * @return float
     */
    public function getOriginalTaxAmount($item, $price)
    {
        return $this->traitGetOriginalTaxAmount($item, $price);
    }

    /**
     * Get base original tax amount
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param float $price
     * @return float
     */
    public function getBaseOriginalTaxAmount($item, $price)
    {
        return $this->traitGetBaseOriginalTaxAmount($item, $price);
    }

    /**
     * Format subtotal
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param float $price
     * @return float
     */
    public function formatSubtotal($item, $price)
    {
        return $this->traitFormatSubtotal($item, $price);
    }

    /**
     * Check if item has parent and parent type is configurable or bundle
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return bool
     */
    public function isChildProduct($item)
    {
        return $this->traitIsChildProduct($item);
    }

    /**
     * Concert a price to the quote rate price
     * - Magento does not come with a currency conversion via the quote rates, only the active rates.
     *
     * @param int|float|double $price
     * @return double
     */
    public function convertPriceToQuoteCurrency($price)
    {
        return $this->traitConvertPriceToQuoteCurrency($price);
    }

    /**
     * Check if currency is different on this quote
     *
     * @return bool
     */
    public function isCurrencyDifferent()
    {
        return $this->traitIsCurrencyDifferent();
    }

    /**
     * Convert the rate of a price
     * - Todo: consider refactoring this to a helper
     *
     * @param int|float|double $price
     * @param int|float|double $rate
     * @param bool $base
     * @return double
     */
    public static function convertRate($price, $rate, $base = false)
    {
        return Quote::traitConvertRate($price, $rate, $base);
    }

    /**
     * Function that recalculates the new custom price total
     *
     * @return $this
     */
    public function recalculateCustomPriceTotal()
    {
        return $this->traitRecalculateCustomPriceTotal();
    }

    /**
     * Check if the current item is set to show prices including tax
     *
     * @param null|int $storeId
     * @return bool
     */
    public function priceIncludesTax($storeId = null)
    {
        return $this->traitPriceIncludesTax($storeId);
    }

    /**
     * Check if subtotal includes tax on this quote or a given store id
     *
     * @param int $storeId
     * @return bool
     */
    public function subtotalIncludesTax($storeId = null)
    {
        return $this->traitSubtotalIncludesTax($storeId);
    }

    /**
     * Set Custom Price Total
     *
     * @param float $customPriceTotal
     * @return $this
     */
    public function setCustomPriceTotal($customPriceTotal)
    {
        return $this->traitSetCustomPriceTotal($customPriceTotal);
    }

    /**
     * Set Base Custom Price Total
     *
     * @param float $baseCustomPriceTotal
     * @return $this
     */
    public function setBaseCustomPriceTotal($baseCustomPriceTotal)
    {
        return $this->traitSetBaseCustomPriceTotal($baseCustomPriceTotal);
    }

    /**
     * Function that recalculates the new custom price total
     *
     * @return $this
     */
    public function recalculateQuoteAdjustmentTotal()
    {
        return $this->traitRecalculateQuoteAdjustmentTotal();
    }

    /**
     * Getter for the tax amount
     *
     * @return int
     */
    public function getTaxAmount()
    {
        return $this->traitGetTaxAmount();
    }

    /**
     * Getter for the base tax amount
     *
     * @return int
     */
    public function getBaseTaxAmount()
    {
        return $this->traitGetBaseTaxAmount();
    }

    /**
     * Get Base Original Subtotal
     *
     * @return float
     */
    public function getBaseOriginalSubtotal()
    {
        return $this->traitGetBaseOriginalSubtotal();
    }

    /**
     * Get original subtotal
     *
     * @return mixed
     */
    public function getOriginalSubtotal()
    {
        return $this->traitGetOriginalSubtotal();
    }

    /**
     * Set Quote Adjustment
     *
     * @param float $quoteAdjustment
     * @return $this
     */
    public function setQuoteAdjustment($quoteAdjustment)
    {
        return $this->traitSetQuoteAdjustment($quoteAdjustment);
    }

    /**
     * Set Base Quote Adjustment
     *
     * @param float $baseQuoteAdjustment
     * @return $this
     */
    public function setBaseQuoteAdjustment($baseQuoteAdjustment)
    {
        return $this->traitSetBaseQuoteAdjustment($baseQuoteAdjustment);
    }

    /**
     * Remove tier item
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param int $qty
     * @return $this
     */
    public function removeTier(\Magento\Quote\Model\Quote\Item $item, $qty)
    {
        return $this->traitRemoveTier($item, $qty);
    }

    /**
     * Retrieve quote edit availability
     *
     * @return bool
     */
    public function canEdit()
    {
        return $this->traitCanEdit();
    }

    /**
     * Retrieve quote cancel availability
     *
     * @return bool
     */
    public function canCancel()
    {
        return $this->traitCanCancel();
    }

    /**
     * Check whether quote is canceled
     *
     * @return bool
     */
    public function isCanceled()
    {
        return $this->traitIsCanceled();
    }

    /**
     * Retrieve quote hold availability
     *
     * @return bool
     */
    public function canHold()
    {
        return $this->traitCanHold();
    }

    /**
     * Retrieve quote unhold availability
     *
     * @return bool
     */
    public function canUnhold()
    {
        return $this->traitCanUnhold();
    }

    /**
     * Check if comment can be added to quote history
     *
     * @return bool
     */
    public function canComment()
    {
        return $this->traitCanComment();
    }

    /*********************** STATUSES ***************************/

    /**
     * Can change quote request check
     *
     * @return bool
     */
    public function canChangeRequest()
    {
        return $this->traitCanChangeRequest();
    }

    /**
     * Return array of quote status history items without deleted.
     *
     * @return array
     */
    public function getAllStatusHistory()
    {
        return $this->traitGetAllStatusHistory();
    }

    /**
     * Return collection of quote status history items.
     *
     * @return ResourceModel\Quote\Status\History\Collection
     */
    public function getStatusHistoryCollection()
    {
        return $this->traitGetStatusHistoryCollection();
    }

    /**
     * Return collection of visible on frontend quote status history items.
     *
     * @return array
     */
    public function getVisibleStatusHistory()
    {
        return $this->traitGetVisibleStatusHistory();
    }

    /**
     * Get status history by id
     *
     * @param int $statusId
     * @return string|false
     */
    public function getStatusHistoryById($statusId)
    {
        return $this->traitGetStatusHistoryById($statusId);
    }

    /**
     * Set the quote status history object and the quote object to each other
     * - Adds the object to the status history collection, which is automatically saved when the quote is saved.
     * - See the entity_id attribute backend model.
     * - Or the history record can be saved standalone after this.
     *
     * @param \Cart2Quote\Quotation\Model\Quote\Status\History $history
     * @return $this
     */
    public function addStatusHistory(\Cart2Quote\Quotation\Model\Quote\Status\History $history)
    {
        return $this->traitAddStatusHistory($history);
    }

    /**
     * Quote saving
     *
     * @return $this
     * @throws \Exception
     */
    public function saveQuote()
    {
        return $this->traitSaveQuote();
    }

    /**
     * Parse data retrieved from request
     *
     * @param array $data
     * @return  $this
     */
    public function importPostData($data)
    {
        return $this->traitImportPostData($data);
    }

    /**
     * Set shipping method
     *
     * @param string $method
     * @return $this
     */
    public function setShippingMethod($method)
    {
        return $this->traitSetShippingMethod($method);
    }

    /**
     * Set payment method into quote
     *
     * @param string $method
     * @return $this
     */
    public function setPaymentMethod($method)
    {
        return $this->traitSetPaymentMethod($method);
    }

    /**
     * Add coupon code to the quote
     *
     * @param string $code
     * @return $this
     */
    public function applyCoupon($code)
    {
        return $this->traitApplyCoupon($code);
    }

    /**
     * Empty shipping method and clear shipping rates
     *
     * @return $this
     */
    public function resetShippingMethod()
    {
        return $this->traitResetShippingMethod();
    }

    /**
     * Collect shipping data for quote shipping address
     *
     * @return $this
     */
    public function collectShippingRates()
    {
        return $this->traitCollectShippingRates();
    }

    /**
     * Calculate totals
     *
     * @return void
     */
    public function collectRates()
    {
        $this->traitCollectRates();
    }

    /**
     * Set payment data into quote
     *
     * @param array $data
     * @return $this
     */
    public function setPaymentData($data)
    {
        return $this->traitSetPaymentData($data);
    }

    /**
     * Initialize data for price rules
     *
     * @return $this
     */
    public function initRuleData()
    {
        return $this->traitInitRuleData();
    }

    /**
     * Set shipping anddress to be same as billing
     *
     * @param bool $flag If true - don't save in address book and actually copy data across billing and shipping
     *                   addresses
     * @return $this
     */
    public function setShippingAsBilling($flag)
    {
        return $this->traitSetShippingAsBilling($flag);
    }

    /**
     * Add multiple products to current quotation quote
     *
     * @param array $products
     * @return $this
     */
    public function addProducts(array $products)
    {
        return $this->traitAddProducts($products);
    }

    /**
     * Check different product types stock quantities
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Framework\DataObject $config
     * @param boolean $qtyCheck
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function checkProduct($product, $config, $qtyCheck = false)
    {
        $this->traitCheckProduct($product, $config, $qtyCheck);
    }

    /**
     * Add product to the quote
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param null|float|\Magento\Framework\DataObject $request
     * @param null|string $processMode
     * @return \Magento\Quote\Model\Quote\Item|string
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addProduct(
        \Magento\Catalog\Model\Product $product,
        $request = null,
        $processMode = \Magento\Catalog\Model\Product\Type\AbstractType::PROCESS_MODE_FULL
    ) {
        return $this->traitAddProduct($product, $request, $processMode);
    }

    /**
     * Get quote item by product
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return bool|\Magento\Quote\Model\ResourceModel\Quote\Item
     */
    public function getItemByProduct($product)
    {
        return $this->traitGetItemByProduct($product);
    }

    /**
     * Get Items by product
     *
     * @param Product $product
     * @return array
     */
    public function getItemsByProduct($product)
    {
        return $this->traitGetItemsByProduct($product);
    }

    /**
     * Update base custom price
     *
     * @return $this
     */
    public function updateBaseCustomPrice()
    {
        return $this->traitUpdateBaseCustomPrice();
    }

    /**
     * Set proposal subtotal
     *
     * @param float $amount
     * @param bool $isPercentage
     * @return $this
     */
    public function setSubtotalProposal($amount, $isPercentage)
    {
        return $this->traitSetSubtotalProposal($amount, $isPercentage);
    }

    /**
     * Remove quote item
     *
     * @param int $item
     * @return $this
     */
    public function removeQuoteItem($item)
    {
        return $this->traitRemoveQuoteItem($item);
    }

    /**
     * Remove quote item by item identifier
     *
     * @param int $itemId
     * @return $this
     */
    public function removeQuotationItem($itemId)
    {
        return $this->traitRemoveQuotationItem($itemId);
    }

    /**
     * Get items collection
     *
     * @param bool $useCache
     * @return \Magento\Eav\Model\Entity\Collection\AbstractCollection
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getItemsCollection($useCache = true)
    {
        return $this->traitGetItemsCollection($useCache);
    }

    /**
     * Retrieve label of quote status
     *
     * @return string
     */
    public function getStatusLabel()
    {
        return $this->traitGetStatusLabel();
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->traitGetStatus();
    }

    /**
     * Retrieve label of quote status
     *
     * @return string
     */
    public function getStateLabel()
    {
        return $this->traitGetStateLabel();
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        return $this->traitGetState();
    }

    /**
     * Get formatted price value including quote currency rate to quote website currency
     *
     * @param float $price
     * @param bool $addBrackets
     * @return  string
     */
    public function formatPrice($price, $addBrackets = false)
    {
        return $this->traitFormatPrice($price, $addBrackets);
    }

    /**
     * Format price presision with or without brackets
     *
     * @param float $price
     * @param int $precision
     * @param bool $addBrackets
     * @return string
     */
    public function formatPricePrecision($price, $precision, $addBrackets = false)
    {
        return $this->traitFormatPricePrecision($price, $precision, $addBrackets);
    }

    /**
     * Get currency model instance. Will be used currency with which quote placed
     *
     * @return \Magento\Directory\Model\Currency
     */
    public function getQuoteCurrency()
    {
        return $this->traitGetQuoteCurrency();
    }

    /**
     * Getter for quote_currency_code
     *
     * @return string
     */
    public function getQuoteCurrencyCode()
    {
        return $this->traitGetQuoteCurrencyCode();
    }

    /**
     * Format base price
     *
     * @param float $price
     * @return string
     */
    public function formatBasePrice($price)
    {
        return $this->traitFormatBasePrice($price);
    }

    /**
     * Format base price prescision
     *
     * @param float $price
     * @param int $precision
     * @return string
     */
    public function formatBasePricePrecision($price, $precision)
    {
        return $this->traitFormatBasePricePrecision($price, $precision);
    }

    /**
     * Retrieve order website currency for working with base prices
     *
     * @return \Magento\Directory\Model\Currency
     */
    public function getBaseCurrency()
    {
        return $this->traitGetBaseCurrency();
    }

    /**
     * Retrieve passed currency for working with different currencies
     *
     * @param string $currency
     * @return \Magento\Directory\Model\Currency|\Magento\Quote\Model\Cart\Currency
     */
    public function getAnyCurrency($currency)
    {
        return $this->traitGetAnyCurrency($currency);
    }

    /**
     * Reset the quote currency to the current quote currency
     *
     * @return $this
     */
    public function resetQuoteCurrency()
    {
        return $this->traitResetQuoteCurrency();
    }

    /**
     * Retrieve text formatted price value including quote rate
     *
     * @param float $price
     * @return  string
     */
    public function formatPriceTxt($price)
    {
        return $this->traitFormatPriceTxt($price);
    }

    /**
     * Get customer name (by adding the first and last name togetter
     * - TODO: add midlename if available?
     *
     * @return string
     */
    public function getCustomerName()
    {
        return $this->traitGetCustomerName();
    }

    /**
     * Get formatted quote created date in store timezone
     *
     * @param string $format date format type (short|medium|long|full)
     * @return  string
     */
    public function getCreatedAtFormatted($format)
    {
        return $this->traitGetCreatedAtFormatted($format);
    }

    /**
     * Get customer note if getCustomerNoteNotify returns true
     *
     * @return string
     */
    public function getEmailCustomerNote()
    {
        return $this->traitGetEmailCustomerNote();
    }

    /**
     * Get formated expiry date
     *
     * @return string
     */
    public function getExpiryDateString()
    {
        return $this->traitGetExpiryDateString();
    }

    /**
     * Get formatted quote expiry date
     *
     * @param string $format date format type (short|medium|long|full)
     * @return  string
     */
    public function getExpiryDateFormatted($format)
    {
        return $this->traitGetExpiryDateFormatted($format);
    }

    /**
     * Sets the increment ID for the quote.
     *
     * @param string $id
     * @return $this
     */
    public function setIncrementId($id)
    {
        return $this->traitSetIncrementId($id);
    }

    /**
     * Sets the proposal sent for the quote.
     *
     * @param string $timestamp
     * @return $this
     */
    public function setProposalSent($timestamp)
    {
        return $this->traitSetProposalSent($timestamp);
    }

    /**
     * Function to check whether the quote can be accepted based on its state and status
     *
     * @return bool
     */
    public function canAccept()
    {
        return $this->traitCanAccept();
    }

    /**
     * Function to check whether the quote can show prices based on its state and status
     *
     * @return bool
     */
    public function showPrices()
    {
        return $this->traitShowPrices();
    }

    /**
     * Get Base Customer Price Total
     *
     * @return float
     */
    public function getBaseCustomPriceTotal()
    {
        return $this->traitGetBaseCustomPriceTotal();
    }

    /**
     * Get Customer Price Total
     *
     * @return float
     */
    public function getCustomPriceTotal()
    {
        return $this->traitGetCustomPriceTotal();
    }

    /**
     * Get Quote Adjustment
     *
     * @return float
     */
    public function getQuoteAdjustment()
    {
        return $this->traitGetQuoteAdjustment();
    }

    /**
     * Get Base Quote Adjustment
     *
     * @return float
     */
    public function getBaseQuoteAdjustment()
    {
        return $this->traitGetBaseQuoteAdjustment();
    }

    /**
     * Return quote entity type
     *
     * @return string
     */
    public function getEntityType()
    {
        return $this->traitGetEntityType();
    }

    /**
     * Get increment id
     *
     * @return string
     */
    public function getIncrementId()
    {
        return $this->traitGetIncrementId();
    }

    /**
     * Function that gets a hash to use in a url (for autologin urls)
     *
     * @return string
     */
    public function getUrlHash()
    {
        return $this->traitGetUrlHash();
    }

    /**
     * Function that generates a random hash of a given length
     *
     * @param int $length
     * @return string
     * @throws \Exception
     */
    public function getRandomHash($length = 40)
    {

        return $this->traitGetRandomHash($length);
    }

    /**
     * Concert a price to the quote base rate price
     * - Magento does not come with a currency conversion via the quote rates, only the active rates.
     *
     * @param int|float $price
     * @return double
     */
    public function convertPriceToQuoteBaseCurrency($price)
    {
        return $this->traitConvertPriceToQuoteBaseCurrency($price);
    }

    /**
     * Convert shipping price to quote base / currency amount
     *
     * @param int|float $price
     * @param bool $base
     * @return float
     */
    public function convertShippingPrice($price, $base)
    {
        return $this->traitConvertShippingPrice($price, $base);
    }

    /**
     * Get total item qty
     *
     * @return int
     */
    public function getTotalItemQty()
    {
        return $this->traitGetTotalItemQty();
    }

    /**
     * Get sections
     *
     * @param array $unassignedData
     * @return array
     */
    public function getSections($unassignedData = [])
    {
        return $this->traitGetSections($unassignedData);
    }

    /**
     * Get section items for a given section id
     *
     * @param int $sectionId
     * @return array
     */
    public function getSectionItems($sectionId)
    {
        return $this->traitGetSectionItems($sectionId);
    }

    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_traitConstruct();
    }

    /**
     * Sort the quote items to the itemId
     *
     * @param \Magento\Quote\Api\Data\CartItemInterface $compare
     * @param \Magento\Quote\Api\Data\CartItemInterface $to
     * @return int
     */
    private function sortItemId($compare, $to)
    {
        return $this->traitSortItemId($compare, $to);
    }

    /**
     * Sort the quote cart to a given order
     *
     * @param \Cart2Quote\Quotation\Model\Quote\Section|\Magento\Quote\Api\Data\CartItemInterface $compare
     * @param \Cart2Quote\Quotation\Model\Quote\Section|\Magento\Quote\Api\Data\CartItemInterface $to
     * @return int
     */
    private function sort($compare, $to)
    {
        return $this->traitSort($compare, $to);
    }

    /**
     * Check if the Quote has Optional Items
     *
     * @return bool
     */
    public function hasOptionalItems()
    {
        return $this->traitHasOptionalItems();
    }

    /**
     * Getter for the extention attributes
     * This is a fix for M2.1 and M2.2 as they can have ExtensionAttributes set to null
     *
     * @return \Magento\Quote\Api\Data\CartExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->traitGetExtensionAttributes();
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $item
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    private function assignToSection(\Magento\Quote\Model\Quote\Item $item)
    {
        $this->traitAssignToSection($item);
    }

    /**
     * Define customer object
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return $this
     */
    public function setCustomer(\Magento\Customer\Api\Data\CustomerInterface $customer = null)
    {
        return $this->traitSetCustomer($customer);
    }

    /**
     * Getter for the customer email
     * @return string
     */
    public function getCustomerEmail()
    {
        return $this->traitGetCustomerEmail();
    }
}
