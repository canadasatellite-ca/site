<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items;

use Cart2Quote\Quotation\Model\Quote\TierItem;
use Magento\CatalogInventory\Api\StockRegistryInterface;

/**
 * Class DefaultColumn
 *
 * @package Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items
 */
class DefaultRenderer extends \Magento\Sales\Block\Adminhtml\Items\Column\DefaultColumn implements FooterInterface
{
    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;

    /**
     * Quotation Quote
     *
     * @var \Cart2Quote\Quotation\Model\Quote
     */
    protected $quote;

    /**
     * Empty Quote item
     *
     * @var \Magento\Quote\Model\Quote\Item
     */
    protected $emptyQuoteItem;

    /**
     * @var \Cart2Quote\Quotation\Helper\QuotationTaxHelper
     */
    protected $quotationTaxHelper;

    /**
     * @var \Magento\Tax\Block\Item\Price\Renderer
     */
    protected $itemPriceRenderer;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    public $productRepositoryInterface;

    /**
     * @var \Cart2Quote\Quotation\Helper\CostPrice
     */
    protected $costPriceHelper;

    /**
     * DefaultRenderer constructor.
     *
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\Product\OptionFactory $optionFactory
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @param \Magento\Quote\Model\Quote\Item $emptyQuoteItem
     * @param \Cart2Quote\Quotation\Helper\QuotationTaxHelper $quotationTaxHelper
     * @param \Magento\Tax\Block\Item\Price\Renderer $itemPriceRenderer
     * @param \Cart2Quote\Quotation\Helper\CostPrice $costPriceHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\Product\OptionFactory $optionFactory,
        \Cart2Quote\Quotation\Model\Quote $quote,
        \Magento\Quote\Model\Quote\Item $emptyQuoteItem,
        \Cart2Quote\Quotation\Helper\QuotationTaxHelper $quotationTaxHelper,
        \Magento\Tax\Block\Item\Price\Renderer $itemPriceRenderer,
        \Cart2Quote\Quotation\Helper\CostPrice $costPriceHelper,
        array $data = []
    ) {
        $this->imageHelper = $imageHelper;
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->quote = $quote;
        $this->emptyQuoteItem = $emptyQuoteItem;
        $this->quotationTaxHelper = $quotationTaxHelper;
        $this->itemPriceRenderer = $itemPriceRenderer;
        $this->costPriceHelper = $costPriceHelper;
        parent::__construct(
            $context,
            $stockRegistry,
            $stockConfiguration,
            $registry,
            $optionFactory,
            $data
        );
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getProductImageUrl($product) {
        $imageUrl = $this->imageHelper->init(
            $product, 'product_thumbnail_image')
            ->setImageFile(
                $product->getSmallImage())
            ->resize(
                100, 100)
            ->getUrl();

        return $imageUrl;
    }

    /**
     * Check for and return applied percentage to quote row items
     *
     * @return int|string
     */
    public function getAppliedValue() {
        if ($this->quotationTaxHelper->priceIncludesTax()) {
            $price = round($this->getTierItem()->getOriginalPriceInclTax(), 2);
        } else {
            $price = $this->getTierItem()->getBaseOriginalPrice();
        }
        $customPrice = $this->getTierItem()->getCustomPrice();

        if ($price !== $customPrice) {
            $appliedDiscount = -round($customPrice / ($price / 100), 2) + 100;

            return $appliedDiscount;
        }

        return "";
    }

    /**
     * OVERWRITE Magento getOrder function
     * - Retrieve quote model object
     *
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    public function getOrder()
    {
        return $this->getQuote();
    }

    /**
     * Retrieve quote model object
     *
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    public function getQuote()
    {
        if (!$quote = $this->_coreRegistry->registry('current_quote')) {
            $this->_coreRegistry->register(
                'current_quote',
                $this->quote->load($this->getRequest()->getParam('quote_id'))
            );
        }

        return $quote;
    }

    /**
     * Format price with custom return
     *
     * @param int $value
     * @param string|int $zero
     * @return string
     */
    public function formatPriceZero($value, $zero)
    {
        if (isset($value) && $value > 0) {
            return $this->formatPrice($value);
        }

        return $zero;
    }

    /**
     * Get the footer HTML
     *
     * @return string
     */
    public function toFooterHtml()
    {
        return $this->getChildHtml('footer.' . $this->getNameInLayout());
    }

    /**
     * Get the item count
     *
     * @return int
     */
    public function getItemCount()
    {
        $count = $this->getData('item_count');

        if ($count === null) {
            $count = $this->getQuote()->getTotalItemQty();
            $this->setData('item_count', $count);
        }

        return $count;
    }

    /**
     * Get the items
     *
     * @return array|\Magento\Eav\Model\Entity\Collection\AbstractCollection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getItems()
    {
        return $this->getItemsBlock()->getItems();
    }

    /**
     * Get the item block
     *
     * @return \Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\GridItems
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getItemsBlock()
    {
        if (!$itemBlock = $this->getLayout()->getBlock('items')) {
            throw new \Magento\Framework\Exception\LocalizedException(
                'Quote Items render error: "items"' .
                ' needs to be a child of the block "' .
                $this->getNameInLayout() .
                '"'
            );
        }

        return $itemBlock;
    }

    /**
     * Get col span if there are more tier items
     *
     * @param string $columnName
     * @return string
     */
    public function getRowSpan($columnName)
    {
        $html = '';

        if ($this->getTotalTierItemCount() && !$this->isTierColumn($columnName)) {
            $html = sprintf('rowspan="%s"', $this->getTotalTierItemCount());
        }

        return $html;
    }

    /**
     * Get the tier item count
     *
     * @return int
     */
    public function getTotalTierItemCount()
    {
        return count($this->getItem()->getTierItems());
    }

    /**
     * Get item
     *
     * @return \Magento\Quote\Model\Quote\Item
     */
    public function getItem()
    {
        $item = $this->_getData('item');
        if ($item instanceof \Magento\Quote\Model\Quote\Item) {
            return $item;
        } else {
            return $this->emptyQuoteItem;
        }
    }

    /**
     * Check if column is tier column
     *
     * @param string $columnName
     * @return bool
     */
    public function isTierColumn($columnName)
    {
        $tierColumns = $this->getTierColumns();

        return isset($tierColumns[$columnName]) && $tierColumns[$columnName];
    }

    /**
     * Get empty column html
     *
     * @param \Magento\Framework\DataObject $item
     * @param string $column
     * @param null|string $field
     * @return string
     */
    public function getEmptyColumnHtml(\Magento\Framework\DataObject $item, $column, $field = null)
    {
        $html = '';
        $columnRenderer = $this->getColumnRenderer($column, $item->getProductType());
        $emptyColumn = $columnRenderer->getChildBlock('empty.' . $columnRenderer->getNameInLayout());
        if ($emptyColumn) {
            $html = $emptyColumn->setItem($item)->setField($field)->toHtml();
        }

        return $html;
    }

    /**
     * Get the select tier css class
     *
     * @return string
     */
    public function getSelectedTierClass()
    {
        if ($this->getItem()->getIsSelectedTier()) {
            return 'selected-tier-row';
        } else {
            return '';
        }
    }

    /**
     * Get the item id
     *
     * @return int|null
     */
    public function getItemId()
    {
        return $this->getItem()->getId();
    }

    /**
     * Get the tier item set by the parent
     *
     * @return TierItem|null
     */
    public function getTierItem()
    {
        return $this->getItem()->getTierItem();
    }

    /**
     * Get flag for first tier item
     *
     * @return bool|null
     */
    public function getIsFirstTierItem()
    {
        return $this->getItem()->getIsFirstTierItem();
    }

    /**
     * Get the current tier count (starts from 0)
     *
     * @return int|null
     */
    public function getTierItemCount()
    {
        return $this->getItem()->getTierItemCount();
    }

    /**
     * Get flag for selected tier (the tier selected for this quote item)
     *
     * @return bool|null
     */
    public function getIsSelectedTier()
    {
        return $this->getItem()->getIsSelectedTier();
    }

    /**
     * Check if prices of product in catalog include tax
     *
     * @return bool
     */
    public function priceIncludesTax()
    {
        return $this->quotationTaxHelper->priceIncludesTax($this->getItem()->getStoreId());
    }

    /**
     * Get original price including tax
     *
     * @param \Cart2Quote\Quotation\Model\Quote\TierItem $tierItem
     * @return float
     */
    public function getOriginalPriceInclTax($tierItem)
    {
        return $this->quotationTaxHelper->getOriginalPriceInclTax($tierItem);
    }

    /**
     * Get base original price including tax
     *
     * @param \Cart2Quote\Quotation\Model\Quote\TierItem $tierItem
     * @return float
     */
    public function getBaseOriginalPriceInclTax($tierItem)
    {
        return $this->quotationTaxHelper->getBaseOriginalPriceInclTax($tierItem);
    }

    /**
     * Calculate base total amount for the (tier) item including tax
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return float
     */
    public function getBaseTotalAmountInclTax(\Magento\Quote\Model\Quote\Item $item)
    {
        $tierItem = $item->getCurrentTierItem();
        if (isset($tierItem)) {
            return $item->getCurrentTierItem()->getBaseRowTotal()
                - $item->getCurrentTierItem()->getBaseDiscountAmount()
                + $item->getCurrentTierItem()->getBaseTaxAmount()
                + $item->getCurrentTierItem()->getBaseDiscountTaxCompensationAmount();
        }

        return 0.00;
    }

    /**
     * Calculate total amount for the (tier) item including tax
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return float
     */
    public function getTotalAmountInclTax(\Magento\Quote\Model\Quote\Item $item)
    {
        $tierItem = $item->getCurrentTierItem();
        if (isset($tierItem)) {
            return $item->getCurrentTierItem()->getRowTotal()
                - $item->getCurrentTierItem()->getDiscountAmount()
                + $item->getCurrentTierItem()->getTaxAmount()
                + $item->getCurrentTierItem()->getBaseDiscountTaxCompensationAmount();
        }

        return 0.00;
    }

    /**
     * Get cost total
     *
     * @return float | null
     */
    public function getCostTotal()
    {
        return $this->costPriceHelper->getCostTotal($this->getQuote());
    }

    /**
     * Get item cost
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return float | null
     */
    public function getItemCost(\Magento\Quote\Model\Quote\Item $item)
    {
        return $this->costPriceHelper->getItemCost($this->getQuote(), $item, false);
    }

    /**
     * Get item cost
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return float | null
     */
    public function getItemBaseCost(\Magento\Quote\Model\Quote\Item $item)
    {
        return $this->costPriceHelper->getItemBaseCost($this->getQuote(), $item, false);
    }

    /**
     * Checks if every item in the Quote has a Cost Price
     *
     * @return bool
     */
    public function getEveryItemHasCost()
    {
        foreach ($this->getQuote()->getAllVisibleItems() as $item) {
            if ($this->getItemCost($item) === null) {
                return false;
            }
        }

        return true;
    }

    /**
     * Return whether display setting is to display price including tax
     *
     * @return bool
     */
    public function displayPriceInclTax()
    {
        return $this->itemPriceRenderer->displayPriceInclTax();
    }

    /**
     * Return whether display setting is to display price excluding tax
     *
     * @return bool
     */
    public function displayPriceExclTax()
    {
        return $this->itemPriceRenderer->displayPriceExclTax();
    }

    /**
     * Return whether display setting is to display both price including tax and price excluding tax
     *
     * @return bool
     */
    public function displayBothPrices()
    {
        return $this->itemPriceRenderer->displayBothPrices();
    }

    /**
     * Get quote base original subtotal including tax
     *
     * @return float
     */
    public function getQuoteBaseOriginalSubtotalInclTax()
    {
        return $this->quotationTaxHelper->getQuoteBaseOriginalSubtotalInclTax($this->getOrder());
    }

    /**
     * Get quote original subtotal including tax
     *
     * @return float
     */
    public function getQuoteOriginalSubtotalInclTax()
    {
        return $this->quotationTaxHelper->getQuoteOriginalSubtotalInclTax($this->getOrder());
    }

    /**
     * Retrieve custom price attribute html content
     *
     * @param string $code
     * @param bool $strong
     * @param string $separator
     * @return string
     * @see \Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\Columns\PriceQuoted::getPriceWithCorrectTax
     */
    public function displayCustomPriceAttribute($code, $strong = false, $separator = '<br />')
    {
        $store = $this->getOrder()->getStore();
        $priceDataObject = $this->getPriceDataObject();
        $basePrice = $priceDataObject->getData('base_' . $code);
        $price = $priceDataObject->getData($code);

        if ($this->quotationTaxHelper->priceIncludesTax($store)
            && !$this->quotationTaxHelper->applyTaxOnCustomPrice($store)
        ) {
            //check this until MC-30483 is fixed: https://github.com/magento/magento2/issues/26394
            $magentoVersion = $this->quotationTaxHelper->getMagentoVersion();
            if (version_compare($magentoVersion, "2.3.1", ">")) {
                //for this version of magento we need to remove add tax on the custom price total
                $baseOriginalSubtotal = $priceDataObject->getData('base_original_subtotal');
                $baseOriginalSubtotalInclTax = $priceDataObject->getData('base_original_subtotal_incl_tax');
                $rate = ((100 / $baseOriginalSubtotal) * $baseOriginalSubtotalInclTax) / 100;
                $basePrice = $basePrice * $rate;
                $price = $price * $rate;
            }
        }

        return $this->displayPrices(
            $basePrice,
            $price,
            $strong,
            $separator
        );
    }
}
