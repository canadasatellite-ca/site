<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\Columns;

use Cart2Quote\Quotation\Helper\CustomProduct;

/**
 * Class Name
 *
 * @package Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\Grid
 */
class Name extends \Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\DefaultRenderer
{
    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;

    /**
     * Catalog helper
     *
     * @var \Magento\Catalog\Helper\Data
     */
    protected $catalogHelper;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface
     */
    public $productRepositoryInterface;

    /**
     * @var CustomProduct
     */
    private $customProductHelper;

    /**
     * Name constructor.
     *
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Cart2Quote\Quotation\Helper\CustomProduct $customProductHelper
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
     * @param \Magento\Catalog\Helper\Data $catalogHelper
     * @param \Cart2Quote\Quotation\Helper\CostPrice $costPriceHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Helper\Image $imageHelper,
        CustomProduct $customProductHelper,
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
        \Magento\Catalog\Helper\Data $catalogHelper,
        \Cart2Quote\Quotation\Helper\CostPrice $costPriceHelper,
        array $data = []
    ) {
        $this->catalogHelper = $catalogHelper;
        $this->customProductHelper = $customProductHelper;

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
    }

    /**
     * Add line breaks and truncate value
     *
     * @param string $value
     * @return array
     */
    public function getFormattedOption($value)
    {
        $remainder = '';
        $value = $this->truncateString($value, 200, '', $remainder);
        $result = ['value' => nl2br($value), 'remainder' => nl2br($remainder)];

        return $result;
    }

    /**
     * Truncate string
     *
     * @param string $value
     * @param int $length
     * @param string $etc
     * @param string &$remainder
     * @param bool $breakWords
     * @return string
     */
    public function truncateString($value, $length = 80, $etc = '...', &$remainder = '', $breakWords = true)
    {
        return $this->filterManager->truncate(
            $value,
            ['length' => $length, 'etc' => $etc, 'remainder' => $remainder, 'breakWords' => $breakWords]
        );
    }

    /**
     * Get order options from item
     *
     * @return array
     */
    public function getOrderOptions()
    {
        return $this->customProductHelper->getItemOptions($this->getItem());
    }

    /**
     * Return html button which calls configure window
     *
     * @param Item $item
     * @return string
     */
    public function getConfigureButtonHtml($item)
    {
        $product = $item->getProduct();

        $options = ['label' => __('Configure')];
        if ($product->canConfigure()) {
            $options['onclick'] = sprintf('quote.showQuoteItemConfiguration(%s)', $item->getId());
        } else {
            $options['class'] = ' disabled';
            $options['title'] = __('This product does not have any configurable options');
        }

        return $this->getLayout()
            ->createBlock(\Magento\Backend\Block\Widget\Button::class)
            ->setData($options)
            ->toHtml();
    }

    /**
     * Split SKU of an item by dashes and spaces
     * - Words will not be broken, unless this length is greater than $length
     *
     * @param string $sku
     * @param int $length
     * @return string[]
     */
    public function splitSku($sku, $length = 30)
    {
        return $this->catalogHelper->splitSku($sku, $length);
    }
}
