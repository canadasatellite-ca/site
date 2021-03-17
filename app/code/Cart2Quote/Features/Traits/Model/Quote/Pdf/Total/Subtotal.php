<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Quote\Pdf\Total;
/**
 * Trait Subtotal
 *
 * @package Cart2Quote\Quotation\Model\Quote\Pdf\Total
 */
trait Subtotal
{
    /**
     * Get subtotal for display on PDF
     *
     * @return array
     */
    private function getTotalsForDisplay()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$store = $this->getSource()->getStore();
        $helper = $this->_taxHelper;
        //get amount excluding tax
        $amount = $this->getSource()->formatPriceTxt($this->getAmount());
        //get amount including tax
        if ($this->getSource()->getShippingAddress()->getSubtotalInclTax()) {
            $amountInclTax = $this->getSource()->getShippingAddress()->getSubtotalInclTax();
        } else {
            $amountInclTax = $this->getAmount()
                + $this->getSource()->getShippingAddress()->getTaxAmount()
                - $this->getSource()->getShippingAddress()->getShippingTaxAmount();
        }
        $amountInclTax = $this->getSource()->formatPriceTxt($amountInclTax);
        $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;
        $prefix = '';
        $showAdjustment = $this->scopeConfig->getValue(
            \Cart2Quote\Quotation\Block\Quote\Totals::XML_PATH_CART2QUOTE_QUOTATION_GLOBAL_SHOW_QUOTE_ADJUSTMENT
        );
        if ((bool)$showAdjustment && ($this->getQuote()->getSubtotal() != $this->getQuote()->getOriginalSubTotal())) {
            $origAmount = $this->getSource()->getOriginalSubTotal();
            $origAmountInclTax = $this->getQuote()->getOriginalSubtotalInclTax();
            $origAmountInclTax = $this->getSource()->formatPriceTxt($origAmountInclTax);
            $origAmount = $this->getSource()->formatPriceTxt($origAmount);
            $prefix = 'Quoted ';
            if ($helper->displaySalesSubtotalBoth($store)) {
                $totals = [
                    [
                        'amount' => $this->getAmountPrefix() . $origAmount,
                        'label' => __('Original Subtotal (Excl. Tax)') . ':',
                        'font_size' => $fontSize,
                    ],
                    [
                        'amount' => $this->getAmountPrefix() . $origAmountInclTax,
                        'label' => __('Original Subtotal (Incl. Tax)') . ':',
                        'font_size' => $fontSize
                    ],
                ];
            } elseif ($helper->displaySalesSubtotalInclTax($store)) {
                $totals = [
                    [
                        'amount' => $this->getAmountPrefix() . $origAmountInclTax,
                        'label' => __('Original ') . __($this->getTitle()) . ':',
                        'font_size' => $fontSize,
                    ],
                ];
            } else {
                $totals = [
                    [
                        'amount' => $this->getAmountPrefix() . $origAmount,
                        'label' => __('Original ') . __($this->getTitle()) . ':',
                        'font_size' => $fontSize,
                    ],
                ];
            }
        }
        if ($helper->displaySalesSubtotalBoth($store)) {
            $totals[] =
                [
                    'amount' => $this->getAmountPrefix() . $amount,
                    'label' => __($prefix) . __('Subtotal (Excl. Tax)') . ':',
                    'font_size' => $fontSize
                ];
            $totals[] =
                [
                    'amount' => $this->getAmountPrefix() . $amountInclTax,
                    'label' => __($prefix) . __('Subtotal (Incl. Tax)') . ':',
                    'font_size' => $fontSize
                ];
        } elseif ($helper->displaySalesSubtotalInclTax($store)) {
            $totals[] =
                [
                    'amount' => $this->getAmountPrefix() . $amountInclTax,
                    'label' => __($prefix) . __($this->getTitle()) . ':',
                    'font_size' => $fontSize
                ];
        } else {
            $totals[] =
                [
                    'amount' => $this->getAmountPrefix() . $amount,
                    'label' => __($prefix) . __($this->getTitle()) . ':',
                    'font_size' => $fontSize
                ];
        }
        return $totals;
		}
	}
}
