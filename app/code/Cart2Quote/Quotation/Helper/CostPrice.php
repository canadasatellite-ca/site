<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Helper;

/**
 * Class CostPrice
 *
 * @package Cart2Quote\Quotation\Helper
 */
class CostPrice extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * CostPrice constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Get total cost
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return float
     */
    public function getCostTotal($quote)
    {
        $totalCost = 0;
        foreach ($quote->getAllVisibleItems() as $item) {
            $itemCost = $this->getItemCost($quote, $item, true);
            $totalCost += $itemCost * $item->getQty();
        }

        return $totalCost;
    }

    /**
     * Get item base_cost
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param boolean $isTotals
     * @return float|null
     */
    public function getItemBaseCost($quote, \Magento\Quote\Model\Quote\Item $item, $isTotals)
    {
        $tierItem = $item->getTierItem();

        if ($isTotals) {
            $tierItem = $item->getCurrentTierItem();
        }

        $itemCost = 0;
        if ($tierItem) {
            $itemCost = $tierItem->getBaseCost();
        }

        if ($itemCost <= 0) {
            $itemCost = $item->getBaseCost();
        }

        return $itemCost;
    }

    /**
     * Get item cost
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param boolean $isTotals
     * @return float|null
     */
    public function getItemCost($quote, \Magento\Quote\Model\Quote\Item $item, $isTotals)
    {
        $itemCost = $this->getItemBaseCost($quote, $item, $isTotals);

        $quoteCurrency = $quote->getQuoteCurrency();

        if ($quoteCurrency != $quote->getBaseCurrency()) {
            try {
                $itemCost = $this->storeManager->getStore()->getBaseCurrency()->convert($itemCost, $quoteCurrency);
            } catch (\Exception $e) {
                $logMessage = sprintf("No conversion rate set: %s", $e);
                $this->_logger->notice($logMessage);

                return null;
            }
        }

        return $itemCost;
    }
}
