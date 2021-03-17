<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\Columns;

/**
 * Class Discount
 *
 * @package Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\Columns
 */
class Discount extends \Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\DefaultRenderer
{
    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;

    /**
     * @var \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory
     */
    protected $ruleCollectionFactory;

    /**
     * @var array
     */
    private $availableDiscountHtml = [];

    /**
     * Discount constructor
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
     * @param \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory
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
        \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory,
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

        $this->ruleCollectionFactory = $ruleCollectionFactory;
    }

    /**
     * Retrieve discount price attribute html content
     *
     * @param string $code
     * @param bool $strong
     * @param string $separator
     * @return string
     * @see \Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\Columns\PriceQuoted::getPriceWithCorrectTax
     */
    public function displayDiscountPriceAttribute($code, $strong = false, $separator = '<br />')
    {
        $priceDataObject = $this->getItem();
        $basePrice = $priceDataObject->getData('base_' . $code);
        $price = $priceDataObject->getData($code);

        return $this->displayPrices(
            $basePrice,
            $price,
            $strong,
            $separator
        );
    }

    /**
     * Print the title's of the rules that might apply on this quote item
     */
    public function getAvailableDiscount()
    {
        $html = '';
        $appliedRuleIds = $this->getItem()->getAppliedRuleIds();
        if (is_string($appliedRuleIds) && isset($this->availableDiscountHtml[$appliedRuleIds])) {
            return $this->availableDiscountHtml[$appliedRuleIds];
        }

        $ruleIds = explode(',', $appliedRuleIds);
        $ruleIds = array_unique($ruleIds);

        $rules = $this->ruleCollectionFactory->create()->addFieldToFilter('rule_id', ['in' => $ruleIds]);
        /** @var \Magento\SalesRule\Model\Rule $rule */
        foreach ($rules as $rule) {
            if (($rule->getIsActive() == 1)
                && ($rule->getCouponType() == 1)
                && ($rule->getSimpleFreeShipping() == 0)
            ) {
                $html .= "- " . $rule->getName() . "\n\r";
            }
        }

        //save this result to a class cache
        if (is_string($appliedRuleIds) && !empty($appliedRuleIds)) {
            $this->availableDiscountHtml[$appliedRuleIds] = $html;
        }

        return $html;
    }
}
