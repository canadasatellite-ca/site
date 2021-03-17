<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Quote\Pdf\Total;
/**
 * Trait Grandtotal
 *
 * @package Cart2Quote\Quotation\Model\Quote\Pdf\Total
 */
trait Grandtotal
{
    /**
     * Get grand total for display on PDF
     *
     * @return mixed
     */
    private function getTotalsForDisplay()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$emptyRowAmount = 2;
        $store = $this->getSource()->getStore();
        if (!$this->_taxConfig->displaySalesTaxWithGrandTotal($store)) {
            return $this->appendEmptyRows(parent::getTotalsForDisplay(), $emptyRowAmount);
        }
        $tax = 0;
        foreach ($this->getSource()->getAllVisibleItems() as $item) {
            $tax = $tax + $item->getTaxAmount();
        }
        $shippingTax = $this->getSource()->getShippingAddress()->getShippingTaxAmount();
        $amount = $this->getSource()->formatPriceTxt($this->getAmount());
        $amountExclTax = ($this->getAmount() - $tax) - $shippingTax;
        $amountExclTax = $amountExclTax > 0 ? $amountExclTax : 0;
        $amountExclTax = $this->getSource()->formatPriceTxt($amountExclTax);
        $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;
        $totals = [
            [
                'amount' => $this->getAmountPrefix() . $amountExclTax,
                'label' => __('Grand Total (Excl. Tax) ') . ':',
                'font_size' => $fontSize,
            ],
        ];
        if ($this->_taxConfig->displaySalesFullSummary($store)) {
            $totals = array_merge($totals, $this->getFullTaxInfo());
        }
        if (isset($shippingTax)) {
            $tax = $tax + $shippingTax;
        }
        $totals[] = [
            'amount' => $this->getAmountPrefix() . $this->getSource()->formatPriceTxt($tax),
            'label' => __('Tax') . ':',
            'font_size' => $fontSize,
        ];
        $totals[] = [
            'amount' => $this->getAmountPrefix() . $amount,
            'label' => __('Grand Total (Incl. Tax)') . ':',
            'font_size' => $fontSize,
        ];
        return $this->appendEmptyRows($totals, $emptyRowAmount);
		}
	}
}
