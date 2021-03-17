<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Admin\Quote;

use Magento\Quote\Model\Quote\Item;

/**
 * Class Create
 *
 * @package Cart2Quote\Quotation\Model\Admin\Quote
 */
class Create extends \Magento\Sales\Model\AdminOrder\Create
{

    use \Cart2Quote\Features\Traits\Model\Admin\Quote\Create {
        updateQuotationItems as private traitUpdateQuotationItems;
        setQtyInput as private traitSetQtyInput;
        setConfiguredQtyItem as private traitSetConfiguredQtyItem;
        setItemMergedMessage as private traitSetItemMergedMessage;
        setMergedErrorMessage as private traitSetMergedErrorMessage;
        updateTierItems as private traitUpdateTierItems;
        processOptionalValues as private traitProcessOptionalValues;
        processCustomPrice as private traitProcessCustomPrice;
        processItems as private traitProcessItems;
        processBundle as private traitProcessBundle;
        processConfigurable as private traitProcessConfigurable;
        processQuantity as private traitProcessQuantity;
        processExistingTierItems as private traitProcessExistingTierItems;
        processPercentageDiscount as private traitProcessPercentageDiscount;
        processCustomPriceTax as private traitProcessCustomPriceTax;
        processBaseCustomPrice as private traitProcessBaseCustomPrice;
        processCostPrice as private traitProcessCostPrice;
        processNewTierItems as private traitProcessNewTierItems;
        setCurrencyCustomPrice as private traitSetCurrencyCustomPrice;
        calculateTierPrices as private traitCalculateTierPrices;
        setCurrentTierItemData as private traitSetCurrentTierItemData;
        getCorrectedCustomPrice as private traitGetCorrectedCustomPrice;
    }

    /**
     * Tier item factory
     *
     * @var \Cart2Quote\Quotation\Model\Quote\TierItemFactory
     */
    protected $tierItemFactory;

    /**
     * Tier item collection factory
     *
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\CollectionFactory
     */
    protected $tierItemCollectionFactory;

    /**
     * The calculation quote (duplicate of the original)
     *
     * @var \Magento\Quote\Model\Quote
     */
    protected $calculationQuote;

    /**
     * @var \Magento\CatalogInventory\Model\StockStateProvider
     */
    protected $stockStateProvider;

    /**
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $quotationDataHelper;

    /**
     * @var \Cart2Quote\Quotation\Helper\QuotationTaxHelper
     */
    protected $quotationTaxHelper;

    /**
     * Create constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Sales\Model\Config $salesConfig
     * @param \Magento\Backend\Model\Session\Quote $quoteSession
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\DataObject\Copy $objectCopyService
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Sales\Model\AdminOrder\Product\Quote\Initializer $quoteInitializer
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
     * @param \Magento\Customer\Api\Data\AddressInterfaceFactory $addressFactory
     * @param \Magento\Customer\Model\Metadata\FormFactory $metadataFormFactory
     * @param \Magento\Customer\Api\GroupRepositoryInterface $groupRepository
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Sales\Model\AdminOrder\EmailSender $emailSender
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\Quote\Model\Quote\Item\Updater $quoteItemUpdater
     * @param \Magento\Framework\DataObject\Factory $objectFactory
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Customer\Api\AccountManagementInterface $accountManagement
     * @param \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerFactory
     * @param \Magento\Customer\Model\Customer\Mapper $customerMapper
     * @param \Magento\Quote\Api\CartManagementInterface $quoteManagement
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param \Magento\Sales\Api\OrderManagementInterface $orderManagement
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \Cart2Quote\Quotation\Model\Session $quotationSession
     * @param \Cart2Quote\Quotation\Model\Quote\TierItemFactory $tierItemFactory
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\CollectionFactory $tierItemCollectionFactory
     * @param \Magento\CatalogInventory\Model\StockStateProvider $stockStateProvider
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Cart2Quote\Quotation\Helper\Data $quotationDataHelper
     * @param \Cart2Quote\Quotation\Helper\QuotationTaxHelper $quotationTaxHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Sales\Model\Config $salesConfig,
        \Magento\Backend\Model\Session\Quote $quoteSession,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\DataObject\Copy $objectCopyService,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Sales\Model\AdminOrder\Product\Quote\Initializer $quoteInitializer,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Customer\Api\Data\AddressInterfaceFactory $addressFactory,
        \Magento\Customer\Model\Metadata\FormFactory $metadataFormFactory,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Sales\Model\AdminOrder\EmailSender $emailSender,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Quote\Model\Quote\Item\Updater $quoteItemUpdater,
        \Magento\Framework\DataObject\Factory $objectFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Customer\Api\AccountManagementInterface $accountManagement,
        \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerFactory,
        \Magento\Customer\Model\Customer\Mapper $customerMapper,
        \Magento\Quote\Api\CartManagementInterface $quoteManagement,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\Sales\Api\OrderManagementInterface $orderManagement,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Cart2Quote\Quotation\Model\Session $quotationSession,
        \Cart2Quote\Quotation\Model\Quote\TierItemFactory $tierItemFactory,
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\CollectionFactory $tierItemCollectionFactory,
        \Magento\CatalogInventory\Model\StockStateProvider $stockStateProvider,
        \Magento\Catalog\Helper\Product $productHelper,
        \Cart2Quote\Quotation\Helper\Data $quotationDataHelper,
        \Cart2Quote\Quotation\Helper\QuotationTaxHelper $quotationTaxHelper,
        array $data = []
    ) {
        parent::__construct(
            $objectManager,
            $eventManager,
            $coreRegistry,
            $salesConfig,
            $quoteSession,
            $logger,
            $objectCopyService,
            $messageManager,
            $quoteInitializer,
            $customerRepository,
            $addressRepository,
            $addressFactory,
            $metadataFormFactory,
            $groupRepository,
            $scopeConfig,
            $emailSender,
            $stockRegistry,
            $quoteItemUpdater,
            $objectFactory,
            $quoteRepository,
            $accountManagement,
            $customerFactory,
            $customerMapper,
            $quoteManagement,
            $dataObjectHelper,
            $orderManagement,
            $quoteFactory,
            $data
        );

        // Overwrite the Magento quote session with the Quotation Session.
        $this->_session = $quotationSession;
        $this->tierItemFactory = $tierItemFactory;
        $this->tierItemCollectionFactory = $tierItemCollectionFactory;
        $this->stockStateProvider = $stockStateProvider;
        $productHelper->setSkipSaleableCheck(true);
        $this->quotationDataHelper = $quotationDataHelper;
        $this->quotationTaxHelper = $quotationTaxHelper;
    }

    /**
     * Update quantity of quote items
     *
     * @param array $items
     * @return \Magento\Sales\Model\AdminOrder\Create|array
     * @throws \Exception|\Magento\Framework\Exception\LocalizedException
     */
    public function updateQuotationItems($items)
    {
        return $this->traitUpdateQuotationItems($items);
    }

    /**
     * Set correct qty value to item
     *
     * @param array $info
     */
    public function setQtyInput(&$info)
    {
        $this->traitSetQtyInput($info);
    }

    /**
     * Needed for merging equal configurable and bundle items
     *
     * @param array $items
     * @param \Magento\Quote\Model\Quote\Item $item
     */
    public function setConfiguredQtyItem(&$items, $item)
    {
        $this->traitSetConfiguredQtyItem($items, $item);
    }

    /**
     * Generate success message for configured item when merged with existing item
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     */
    public function setItemMergedMessage($item)
    {
        $this->traitSetItemMergedMessage($item);
    }

    /**
     * Generate error message for configured item with existing tier quantity
     *
     * @param int $itemId
     */
    public function setMergedErrorMessage($itemId)
    {
        $this->traitSetMergedErrorMessage($itemId);
    }

    /**
     * Update tier items of quotation items
     *
     * @param array $items
     * @return $this
     * @throws \Exception|\Magento\Framework\Exception\LocalizedException
     */
    public function updateTierItems($items)
    {
        return $this->traitUpdateTierItems($items);
    }

    /**
     * Process optional values
     * - Set the "on" value to true for saving
     *
     * @param array $tierItems
     * @return array
     */
    protected function processOptionalValues(array $tierItems)
    {
        return $this->traitProcessOptionalValues($tierItems);
    }

    /**
     * Check for allowed custom price value
     *
     * @param array $tierItems
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function processCustomPrice(array $tierItems)
    {
        $this->traitProcessCustomPrice($tierItems);
    }

    /**
     * Process item for quantity check
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param array $tierItems
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function processItems($item, array $tierItems)
    {
        $this->traitProcessItems($item, $tierItems);
    }

    /**
     * Process bundle product for quantity check
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param int $tierItemQty
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function processBundle($item, $tierItemQty)
    {
        $this->traitProcessBundle($item, $tierItemQty);
    }

    /**
     * Process configurable product for quantity check
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param int $tierItemQty
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function processConfigurable($item, $tierItemQty)
    {
        $this->traitProcessConfigurable($item, $tierItemQty);
    }

    /**
     * Check tier quantity for stock settings
     *
     * @param int $productId
     * @param int $qty
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function processQuantity($productId, $qty)
    {
        $this->traitProcessQuantity($productId, $qty);
    }

    /**
     * Process existing tier items
     *
     * @param int $item
     * @param array $info
     * @return \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\Collection
     */
    protected function processExistingTierItems($item, $info)
    {
        return $this->traitProcessExistingTierItems($item, $info);
    }

    /**
     * Process percentage discount on single line item
     *
     * @param \Cart2Quote\Quotation\Model\Quote\TierItem $tierItem
     * @param array $info
     * @return array
     */
    protected function processPercentageDiscount($tierItem, $info)
    {
        return $this->traitProcessPercentageDiscount($tierItem, $info);
    }

    /**
     * Process pcustom price tax
     *
     * @param \Cart2Quote\Quotation\Model\Quote\TierItem $tierItem
     * @param array $info
     * @return array
     */
    protected function processCustomPriceTax($tierItem, $info)
    {
        return $this->traitProcessCustomPriceTax($tierItem, $info);
    }

    /**
     * Function that generates the custom base price
     *
     * @param \Cart2Quote\Quotation\Model\Quote\TierItem $tierItem
     * @param array $info
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function processBaseCustomPrice($tierItem, $info)
    {
        return $this->traitProcessBaseCustomPrice($tierItem, $info);
    }

    /**
     * Process base cost on single line item
     *
     * @param \Cart2Quote\Quotation\Model\Quote\TierItem $tierItem
     * @param array $info
     * @return array
     */
    protected function processCostPrice($tierItem, $info)
    {
        return $this->traitProcessCostPrice($tierItem, $info);
    }

    /**
     * Process new tier items
     *
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\Collection $existingTiers
     * @param array $newTierItems
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\Collection
     */
    protected function processNewTierItems(
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\Collection $existingTiers,
        $newTierItems,
        $item
    ) {
        return $this->traitProcessNewTierItems($existingTiers, $newTierItems, $item);
    }

    /**
     * Calculate new custom_price when changing from currency to another in backend
     *
     * @param \Cart2Quote\Quotation\Model\Quote\TierItem $tierItem
     * @param \Magento\Quote\Model\Quote $quote
     */
    protected function setCurrencyCustomPrice($tierItem, $quote)
    {
        $this->traitSetCurrencyCustomPrice($tierItem, $quote);
    }

    /**
     * Calculate tier price
     *
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\Collection $tierItemCollection
     * @param int $quoteItemId
     * @param int $selectedTierId
     * @return \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\Collection
     */
    protected function calculateTierPrices(
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\Collection $tierItemCollection,
        $quoteItemId,
        $selectedTierId
    ) {
        return $this->traitCalculateTierPrices($tierItemCollection, $quoteItemId, $selectedTierId);
    }

    /**
     * Set the current tier item data to quote item
     *
     * @param \Cart2Quote\Quotation\Model\Quote\TierItem $tierItem
     * @param \Magento\Quote\Model\Quote\Item &$item
     */
    private function setCurrentTierItemData($tierItem, \Magento\Quote\Model\Quote\Item &$item)
    {
        $this->traitSetCurrentTierItemData($tierItem, $item);
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $tierItem
     * @param int|float $customPrice
     * @return int|float
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @see \Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\Columns\PriceQuoted::getPriceWithCorrectTax
     */
    private function getCorrectedCustomPrice(\Magento\Quote\Model\Quote\Item $item, $customPrice)
    {
        return $this->traitGetCorrectedCustomPrice($item, $customPrice);
    }
}
