<?php
/**
 * Cart2Quote
 */
namespace Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\Columns;

use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Api\StockStateInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Quote\Model\Quote\Item;

/**
 * Class CostPrice
 * @package Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\Columns
 */
class CostPrice extends \Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\DefaultRenderer
{
    const ALTERNATIVE_COST_FIELD = 'price';

    /**
     * Get item cost
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param bool|true $useAlternativeCostField
     * @return float
     */
    public function getItemCost(\Magento\Quote\Model\Quote\Item $item, $useAlternativeCostField = true)
    {
        $itemCost = $item->getCost();
        if (!$itemCost) {
            $itemCost = $item->getBaseCost();
        } elseif (!$itemCost && $useAlternativeCostField) {
            $itemCost = $item->getData(self::ALTERNATIVE_COST_FIELD);
        }

        return $itemCost;
    }

    /**
     * Get cost total
     *
     * @param bool|true $useAlternativeCostField
     * @return float
     */
    public function getCostTotal($useAlternativeCostField = true)
    {
        $totalCost = 0;
        foreach ($this->getQuote()->getAllVisibleItems() as $item) {
            $itemCost = $this->getItemCost($item, $useAlternativeCostField);
            $totalCost += $itemCost * $item->getQty();
        }

        return $totalCost;
    }
}