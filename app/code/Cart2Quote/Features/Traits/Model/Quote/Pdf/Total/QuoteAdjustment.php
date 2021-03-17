<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Quote\Pdf\Total;
/**
 * Trait QuoteAdjustment
 *
 * @package Cart2Quote\Quotation\Model\Quote\Pdf\Total
 */
trait QuoteAdjustment
{
    /**
     * Get Quote Reduction for display on PDF
     *
     * @return array
     */
    private function getTotalsForDisplay()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$totals = parent::getTotalsForDisplay();
        $showAdjustment = $this->scopeConfig->getValue(
            \Cart2Quote\Quotation\Block\Quote\Totals::XML_PATH_CART2QUOTE_QUOTATION_GLOBAL_SHOW_QUOTE_ADJUSTMENT
        );
        if ((int)$showAdjustment == 0 || ((int)$showAdjustment == 2 && $this->getAmount() == 0)) {
            unset($totals[0]);
        }
        return $totals;
		}
	}
    /**
     * Function to return the amount that should be included in QuoteReduction block
     *
     * @return mixed
     */
    private function getAmount()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$amount = $this->getSource()->getSubtotal() - $this->getSource()->getOriginalSubTotal();
        return $amount;
		}
	}
}
