<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Quote\Pdf\Total;
/**
 * Trait Discount
 *
 * @package Cart2Quote\Quotation\Model\Quote\Pdf\Total
 */
trait Discount
{
    /**
     * Get the underscore cache
     *
     * @return array
     */
    private static function getUnderscoreCache()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return self::$_underscoreCache;
		}
	}
    /**
     * Get the tax config
     *
     * @return \Magento\Tax\Model\Config
     */
    private function getTaxConfig()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->_taxConfig;
		}
	}
    /**
     * Get tax calculation
     *
     * @return \Magento\Tax\Model\Calculation
     */
    private function getTaxCalculation()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->_taxCalculation;
		}
	}
    /**
     * Get tax orders factory
     *
     * @return \Magento\Tax\Model\ResourceModel\Sales\Order\Tax\CollectionFactory
     */
    private function getTaxOrdersFactory()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->_taxOrdersFactory;
		}
	}
    /**
     * Get quote
     *
     * @return mixed
     */
    private function getQuote()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->_quote;
		}
	}
    /**
     * Get the tax helper
     *
     * @return \Magento\Tax\Helper\Data
     */
    private function getTaxHelper()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->_taxHelper;
		}
	}
    /**
     * Get discounts for display on PDF
     *
     * @return array
     */
    private function getTotalsForDisplay()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$totals = [];
        $amount = 0;
        $quote = $this->getSource();
        $couponCode = $quote->getCouponCode();
        $title = __($this->getTitle());
        $label = null;
        if ($couponCode != '') {
            $label = $title . ' (' . $couponCode . '):';
            $amount = $this->getAmount();
        }
        $totals = array_merge($totals, parent::getTotalsForDisplay());
        //being a discount we want the outcome to be negative
        if ($amount > 0) {
            $amount = $amount - ($amount * 2);
        }
        if ($label != null) {
            $totals[0]['amount'] = $quote->formatPriceTxt($amount);
            $totals[0]['label'] = $label;
        }
        return $totals;
		}
	}
    /**
     * Get the total
     *
     * @return float
     */
    private function getAmount()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getSource()->getSubtotalWithDiscount() - $this->getSource()->getSubtotal();
		}
	}
}
