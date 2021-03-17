<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Quote;

use Magento\Sales\Model\AbstractModel;
use Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface;

/**
 * Class TierItem
 *
 * @package Cart2Quote\Quotation\Model\Quote
 */
class TierItem extends AbstractModel implements QuoteTierItemInterface
{
    use \Cart2Quote\Features\Traits\Model\Quote\TierItem {
        getItemId as private traitGetItemId;
        setEntityId as private traitSetEntityId;
        getEntityId as private traitGetEntityId;
        getBaseOriginalPrice as private traitGetBaseOriginalPrice;
        getBaseCustomPrice as private traitGetBaseCustomPrice;
        setBaseCost as private traitSetBaseCost;
        getRowTotal as private traitGetRowTotal;
        setRowTotal as private traitSetRowTotal;
        getBaseRowTotal as private traitGetBaseRowTotal;
        setBaseRowTotal as private traitSetBaseRowTotal;
        getDiscountAmount as private traitGetDiscountAmount;
        setDiscountAmount as private traitSetDiscountAmount;
        getBaseDiscountAmount as private traitGetBaseDiscountAmount;
        setBaseDiscountAmount as private traitSetBaseDiscountAmount;
        getMakeOptional as private traitGetMakeOptional;
        setMakeOptional as private traitSetMakeOptional;
        setOriginalTaxAmount as private traitSetOriginalTaxAmount;
        getOriginalTaxAmount as private traitGetOriginalTaxAmount;
        setOriginalBaseTaxAmount as private traitSetOriginalBaseTaxAmount;
        getOriginalBaseTaxAmount as private traitGetOriginalBaseTaxAmount;
        setSelected as private traitSetSelected;
        getItem as private traitGetItem;
        resetQuoteItem as private traitResetQuoteItem;
        getQty as private traitGetQty;
        getOriginalPrice as private traitGetOriginalPrice;
        getBaseCost as private traitGetBaseCost;
        setSelectedChild as private traitSetSelectedChild;
        calculateChildTotalPrice as private traitCalculateChildTotalPrice;
        calculateChildPrice as private traitCalculateChildPrice;
        calculatePercentage as private traitCalculatePercentage;
        calculatePrice as private traitCalculatePrice;
        setItem as private traitSetItem;
        setItemId as private traitSetItemId;
        loadPriceOnItem as private traitLoadPriceOnItem;
        getCustomPrice as private traitGetCustomPrice;
        setCustomPrice as private traitSetCustomPrice;
        checkBundleRoundingIssue as private traitCheckBundleRoundingIssue;
        setDataByItem as private traitSetDataByItem;
        setQty as private traitSetQty;
        setNewPrice as private traitSetNewPrice;
        setPriceInclTax as private traitSetPriceInclTax;
        getCurrencyPrice as private traitGetCurrencyPrice;
        getBaseToQuoteRate as private traitGetBaseToQuoteRate;
        getOriginalPriceInclTax as private traitGetOriginalPriceInclTax;
        getBaseOriginalPriceInclTax as private traitGetBaseOriginalPriceInclTax;
        setBaseOriginalPrice as private traitSetBaseOriginalPrice;
        setBaseCustomPrice as private traitSetBaseCustomPrice;
        setOriginalPrice as private traitSetOriginalPrice;
        loadPriceOnProduct as private traitLoadPriceOnProduct;
        isSelected as private traitIsSelected;
        _construct as private _traitConstruct;
        getQuote as private traitGetQuote;
        getQuoteId as private traitGetQuoteId;
        addNewTierItem as private traitAddNewTierItem;
        editExistingTierItem as private traitEditExistingTierItem;
        checkQtyExistTiers as private traitCheckQtyExistTiers;
        deleteTierItem as private traitDeleteTierItem;
        setConfigurableBaseCost as private traitSetConfigurableBaseCost;
    }

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'quotation_quote_item';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'item';

    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\Collection
     */
    protected $tierItemResourceCollection;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     */
    protected $quoteRepository;

    /**
     * @var \Cart2Quote\Quotation\Helper\QuotationTaxHelper
     */
    protected $quotationTaxHelper;

    /**
     * @var \Cart2Quote\Quotation\Model\Quote
     */
    private $quote;

    /**
     * @var \Cart2Quote\Quotation\Model\QuoteFactory
     */
    protected $quotationFactory;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Cart2Quote\Quotation\Model\Quote\TierItemFactory
     */
    protected $tierItemFactory;

    /**
     * @var \Magento\Quote\Model\Quote\ItemFactory
     */
    protected $quoteItemFactory;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $quoteFactory;

    /**
     * TierItem constructor
     *
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\Collection $tierItemResourceCollection
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Cart2Quote\Quotation\Helper\QuotationTaxHelper $quotationTaxHelper
     * @param \Magento\Tax\Api\TaxCalculationInterface $taxCalculationService
     * @param \Cart2Quote\Quotation\Model\QuoteFactory $quotationFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Cart2Quote\Quotation\Model\Quote\TierItemFactory $tierItemFactory
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Magento\Quote\Model\Quote\ItemFactory $quoteItemFactory
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\Collection $tierItemResourceCollection,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Cart2Quote\Quotation\Helper\QuotationTaxHelper $quotationTaxHelper,
        \Magento\Tax\Api\TaxCalculationInterface $taxCalculationService,
        \Cart2Quote\Quotation\Model\QuoteFactory $quotationFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Cart2Quote\Quotation\Model\Quote\TierItemFactory $tierItemFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Quote\Model\Quote\ItemFactory $quoteItemFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $resource,
            $resourceCollection,
            $data
        );

        $this->tierItemResourceCollection = $tierItemResourceCollection;
        $this->quoteRepository = $quoteRepository;
        $this->quotationTaxHelper = $quotationTaxHelper;
        $this->taxCalculationService = $taxCalculationService;
        $this->quotationFactory = $quotationFactory;
        $this->productRepository = $productRepository;
        $this->tierItemFactory = $tierItemFactory;
        $this->quoteItemFactory = $quoteItemFactory;
        $this->quoteFactory = $quoteFactory;
    }

    /**
     * Get item id
     *
     * @return int $itemId
     */
    public function getItemId()
    {
        return $this->traitGetItemId();
    }

    /**
     * Set entity id
     *
     * @param int $entityId
     * @return $this
     */
    public function setEntityId($entityId)
    {
        return $this->traitSetEntityId($entityId);
    }

    /**
     * Set entity id
     *
     * @return int
     */
    public function getEntityId()
    {
        return $this->traitGetEntityId();
    }

    /**
     * Set base original price
     *
     * @return float
     */
    public function getBaseOriginalPrice()
    {
        return $this->traitGetBaseOriginalPrice();
    }

    /**
     * Get Base Custom Price
     *
     * @return float
     */
    public function getBaseCustomPrice()
    {
        return $this->traitGetBaseCustomPrice();
    }

    /**
     * Set base cost price
     *
     * @param float $baseCost
     * @return $this
     */
    public function setBaseCost($baseCost)
    {
        return $this->traitSetBaseCost($baseCost);
    }

    /**
     * Get cost price
     *
     * @return float
     */
    public function getRowTotal()
    {
        return $this->traitGetRowTotal();
    }

    /**
     * Set cost price
     *
     * @param float $costPrice
     * @return $this
     */
    public function setRowTotal($costPrice)
    {
        return $this->traitSetRowTotal($costPrice);
    }

    /**
     * Get base cost price
     *
     * @return float
     */
    public function getBaseRowTotal()
    {
        return $this->traitGetBaseRowTotal();
    }

    /**
     * Set base cost price
     *
     * @param float $baseCostPrice
     * @return $this
     */
    public function setBaseRowTotal($baseCostPrice)
    {
        return $this->traitSetBaseRowTotal($baseCostPrice);
    }

    /**
     * Get discount amount
     *
     * @return float
     */
    public function getDiscountAmount()
    {
        return $this->traitGetDiscountAmount();
    }

    /**
     * Set discount amount
     *
     * @param float $discountAmount
     * @return $this
     */
    public function setDiscountAmount($discountAmount)
    {
        return $this->traitSetDiscountAmount($discountAmount);
    }

    /**
     * Get base discount amount
     *
     * @return float
     */
    public function getBaseDiscountAmount()
    {
        return $this->traitGetBaseDiscountAmount();
    }

    /**
     * Set base discount amount
     *
     * @param float $baseDiscountAmount
     * @return $this
     */
    public function setBaseDiscountAmount($baseDiscountAmount)
    {
        return $this->traitSetBaseDiscountAmount($baseDiscountAmount);
    }

    /**
     * Make optional
     *
     * @return boolean
     */
    public function getMakeOptional()
    {
        return $this->traitGetMakeOptional();
    }

    /**
     * Set make optional
     *
     * @param boolean $makeOptional
     * @return $this
     */
    public function setMakeOptional($makeOptional)
    {
        return $this->traitSetMakeOptional($makeOptional);
    }

    /**
     * @param float $originalTaxAmount
     * @return $this|\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface
     */
    public function setOriginalTaxAmount($originalTaxAmount)
    {
        return $this->traitSetOriginalTaxAmount($originalTaxAmount);
    }

    /**
     * @return float
     */
    public function getOriginalTaxAmount()
    {
        return $this->traitGetOriginalTaxAmount();
    }

    /**
     * @param float $originalBaseTaxAmount
     * @return $this|\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface
     */
    public function setOriginalBaseTaxAmount($originalBaseTaxAmount)
    {
        return $this->traitSetOriginalBaseTaxAmount($originalBaseTaxAmount);
    }

    /**
     * @return float
     */
    public function getOriginalBaseTaxAmount()
    {
        return $this->traitGetOriginalBaseTaxAmount();
    }

    /**
     * Set selected
     *
     * @return \Magento\Quote\Model\Quote\Item $item
     */
    public function setSelected()
    {
        return $this->traitSetSelected();
    }

    /**
     * Get item
     *
     * @return \Magento\Quote\Model\Quote\Item
     */
    public function getItem()
    {
        return $this->traitGetItem();
    }

    /**
     * Reset the quote item prices
     *
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @return \Magento\Quote\Model\Quote\Item
     */
    protected function resetQuoteItem($quoteItem)
    {
        return $this->traitResetQuoteItem($quoteItem);
    }

    /**
     * Get qty
     *
     * @return float $qty
     */
    public function getQty()
    {
        return $this->traitGetQty();
    }

    /**
     * Get original price
     *
     * @return float
     */
    public function getOriginalPrice()
    {
        return $this->traitGetOriginalPrice();
    }

    /**
     * Get base cost price
     *
     * @return float
     */
    public function getBaseCost()
    {
        return $this->traitGetBaseCost();
    }

    /**
     * Set selected for child
     * - The child tier prices are calculated based on the parent tier item
     *
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @return $this
     */
    protected function setSelectedChild($quoteItem)
    {
        return $this->traitSetSelectedChild($quoteItem);
    }

    /**
     * Get total child price
     *
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem[] $children
     * @return int
     */
    protected function calculateChildTotalPrice($children)
    {
        return $this->traitCalculateChildTotalPrice($children);
    }

    /**
     * Calculate the child price
     *
     * @param \Magento\Quote\Model\Quote\Item $child
     * @param float $totalChildPrice
     * @return float
     */
    public function calculateChildPrice(\Magento\Quote\Model\Quote\Item $child, $totalChildPrice)
    {
        return $this->traitCalculateChildPrice($child, $totalChildPrice);
    }

    /**
     * Calculate percentage
     *
     * @param float $total
     * @param float $subject
     * @return float
     */
    public function calculatePercentage($total, $subject)
    {
        return $this->traitCalculatePercentage($total, $subject);
    }

    /**
     * Calculate price
     *
     * @param float $total
     * @param float $percentage
     * @return float
     */
    public function calculatePrice($total, $percentage)
    {
        return $this->traitCalculatePrice($total, $percentage);
    }

    /**
     * Set item
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return $this
     */
    public function setItem(\Magento\Quote\Model\Quote\Item $item)
    {
        return $this->traitSetItem($item);
    }

    /**
     * Set item id
     *
     * @param int $itemId
     * @return $this
     */
    public function setItemId($itemId)
    {
        return $this->traitSetItemId($itemId);
    }

    /**
     * Load the tier price on the quote item
     *
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @return $this
     */
    public function loadPriceOnItem(&$quoteItem)
    {
        return $this->traitLoadPriceOnItem($quoteItem);
    }

    /**
     * Get custom price
     *
     * @return float
     */
    public function getCustomPrice()
    {
        return $this->traitGetCustomPrice();
    }

    /**
     * Set custom price
     *
     * @param float $customPrice
     * @return $this
     */
    public function setCustomPrice($customPrice)
    {
        return $this->traitSetCustomPrice($customPrice);
    }

    /**
     * Adds the rounding differences to the tier item (won't be saved in the DB)
     * - You can use this to detect rounding issues for bundles
     *
     * @param float $totalCalculatedChildPrice
     */
    protected function checkBundleRoundingIssue($totalCalculatedChildPrice)
    {
        $this->traitCheckBundleRoundingIssue($totalCalculatedChildPrice);
    }

    /**
     * Set the tier item data base the quote item values
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param null|int $tierItemId
     * @param null|int $qty
     * @return $this
     */
    public function setDataByItem(\Magento\Quote\Model\Quote\Item $item, $tierItemId = null, $qty = null)
    {
        return $this->traitSetDataByItem($item, $tierItemId, $qty);
    }

    /**
     * Set qty
     *
     * @param float $qty
     * @return $this
     */
    public function setQty($qty)
    {
        return $this->traitSetQty($qty);
    }

    /**
     * Set prices to tier item
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return $this
     */
    protected function setNewPrice(\Magento\Quote\Model\Quote\Item $item)
    {
        return $this->traitSetNewPrice($item);
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return $this
     */
    protected function setPriceInclTax(\Magento\Quote\Model\Quote\Item $item)
    {
        return $this->traitSetPriceInclTax($item);
    }

    /**
     * Calculate quote currency to the price
     *
     * @param float $price
     * @return float
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCurrencyPrice($price)
    {
        return $this->traitGetCurrencyPrice($price);
    }

    /**
     * Get base_to_quote_rate from quote
     *
     * @return float
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBaseToQuoteRate()
    {
        return $this->traitGetBaseToQuoteRate();
    }

    /**
     * Get original price incl tax
     *
     * @return float
     */
    public function getOriginalPriceInclTax()
    {
        return $this->traitGetOriginalPriceInclTax();
    }

    /**
     * Get base original price incl tax
     *
     * @return float
     */
    public function getBaseOriginalPriceInclTax()
    {
        return $this->traitGetBaseOriginalPriceInclTax();
    }

    /**
     * Set base original price
     *
     * @param float $baseOriginalPrice
     * @return $this
     */
    public function setBaseOriginalPrice($baseOriginalPrice)
    {
        return $this->traitSetBaseOriginalPrice($baseOriginalPrice);
    }

    /**
     * Set Base Custom Price
     *
     * @param float $baseCustomPrice
     * @return $this
     */
    public function setBaseCustomPrice($baseCustomPrice)
    {
        return $this->traitSetBaseCustomPrice($baseCustomPrice);
    }

    /**
     * Set original price
     *
     * @param float $originalPrice
     * @return $this
     */
    public function setOriginalPrice($originalPrice)
    {
        return $this->traitSetOriginalPrice($originalPrice);
    }

    /**
     * Load the tier price on the product
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return $this
     */
    public function loadPriceOnProduct(&$product)
    {
        return $this->traitLoadPriceOnProduct($product);
    }

    /**
     * Is tier selected
     *
     * @return bool
     */
    public function isSelected()
    {
        return $this->traitIsSelected();
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
     * Get Quotation Quote
     *
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    public function getQuote()
    {
        return $this->traitGetQuote();
    }

    /**
     * Function that finds the quote id for this tier item
     *
     * @return int|null
     */
    public function getQuoteId()
    {
        return $this->traitGetQuoteId();
    }

    /**
     * Process new tier items
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return \Cart2Quote\Quotation\Model\Quote\TierItem
     * @throws \Exception
     */
    public function addNewTierItem($item)
    {
        return $this->traitAddNewTierItem($item);
    }

    /**
     * Edit existing tier item
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param array $postData
     * @throws \Exception
     */
    public function editExistingTierItem($item, $postData)
    {
        $this->traitEditExistingTierItem($item, $postData);
    }

    /**
     * Check if item quantity exist already in tier quantity
     *
     * @param int $itemId
     * @param int $qty
     * @return bool
     */
    public function checkQtyExistTiers($itemId, $qty)
    {
        return $this->traitCheckQtyExistTiers($itemId, $qty);
    }

    /**
     * Delete tier item
     *
     * @param int $tierItemId
     * @throws \Exception
     */
    public function deleteTierItem($tierItemId)
    {
        $this->traitDeleteTierItem($tierItemId);
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $item
     */
    public function setConfigurableBaseCost($item)
    {
        $this->traitSetConfigurableBaseCost($item);
    }
}
