<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\Columns;

/**
 * Class PriceQuoted
 *
 * @package Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\Columns
 */
class PriceQuoted extends \Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\DefaultRenderer
{
    /**
     * Checks if negative profit is allowed. If true, negative profit is not allowed
     *
     * @return boolean
     */
    private function isDisabledNegativeProfit()
    {
        return $this->_scopeConfig->getValue(
            'cart2quote_advanced/negativeprofit/disable_negative_profit',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Calculates a minimum price that is allowed to set on an item in a proposal
     *
     * @return int
     */
    public function getMinPrice()
    {
        $item = $this->getItem();
        if (!$this->isDisabledNegativeProfit() || $item == null) {
            return 0;
        }

        return $item->getBaseCost() != null ? $item->getBaseCost() : 0;
    }

    /**
     * Workaround for issue MC-30483: https://github.com/magento/magento2/issues/26394
     * Most likely introduced in M2.3.2
     *
     * @param int|float $price
     * @return int|float
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @see \Cart2Quote\Quotation\Model\Admin\Quote\Create::getCorrectedCustomPrice
     */
    public function getPriceWithCorrectTax($price)
    {
        $item = $this->getItem();
        if ($this->quotationTaxHelper->priceIncludesTax($item->getStore())
            && !$this->quotationTaxHelper->applyTaxOnCustomPrice($item->getStore())
        ) {
            //check this until MC-30483 is fixed: https://github.com/magento/magento2/issues/26394
            $magentoVersion = $this->quotationTaxHelper->getMagentoVersion();
            if (version_compare($magentoVersion, "2.3.1", ">")) {
                //for this version of magento we need to remove add tax on the custom price input
                $rate = $this->quotationTaxHelper->getTaxCalculationRate($item, true);
                $price = $price * $rate;
            }
        }

        return $price;
    }
}
