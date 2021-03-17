<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\Columns;

use Magento\Quote\Model\Quote\Item;

/**
 * Class Margin
 *
 * @package Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\Columns
 */
class QuoteMargin extends \Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\DefaultRenderer
{
    const TOTAL = 'total';
    const INDIVIDUAL = 'individual';
    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;

    /**
     * @var \Cart2Quote\Quotation\Helper\MarginCalculation
     */
    private $marginCalculationHelper;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface
     */
    public $productRepositoryInterface;

    /**
     * QuoteMargin constructor.
     *
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface
     * @param \Cart2Quote\Quotation\Helper\MarginCalculation $marginCalculationHelper
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
        \Cart2Quote\Quotation\Helper\MarginCalculation $marginCalculationHelper,
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
        parent::__construct(
            $imageHelper,
            $productRepositoryInterface,
            $context,
            $stockRegistry,
            $stockConfiguration,
            $registry,
            $optionFactory,
            $quote,
            $emptyQuoteItem,
            $quotationTaxHelper,
            $itemPriceRenderer,
            $costPriceHelper,
            $data
        );

        $this->marginCalculationHelper = $marginCalculationHelper;
    }

    /**
     * Get total margin
     *
     * @param float $quoteTotal
     * @param float $costTotal
     * @return float
     */
    public function getTotalMargin($quoteTotal, $costTotal)
    {
        return $this->marginCalculationHelper->calculatePercentage($quoteTotal, $costTotal);
    }

    /**
     * Calculate the profit Margin based on
     * - the item's cost price and the quoted price.
     *
     * @param Item $item
     * @return string
     */
    public function getMargin(\Magento\Quote\Model\Quote\Item $item)
    {
        $margin = $this->marginCalculationHelper->itemMargin($item);
        if ($margin) {
            return $margin;
        }

        return null;
    }
}
