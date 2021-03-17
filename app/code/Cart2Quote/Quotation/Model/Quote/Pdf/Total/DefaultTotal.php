<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Quote\Pdf\Total;

/**
 * Sales Order Total PDF model
 */
class DefaultTotal extends \Magento\Sales\Model\Order\Pdf\Total\DefaultTotal
{
    use \Cart2Quote\Features\Traits\Model\Quote\Pdf\Total\DefaultTotal {
        getTotalsForDisplay as private traitGetTotalsForDisplay;
        getFullTaxInfo as private traitGetFullTaxInfo;
        appendEmptyRows as private traitAppendEmptyRows;
    }

    /**
     * Get total for display on PDF
     *
     * @return array
     */
    public function getTotalsForDisplay()
    {
        return $this->traitGetTotalsForDisplay();
    }

    /**
     * Get array of arrays with tax information for display in PDF
     * array(
     *  $index => array(
     *      'amount'   => $amount,
     *      'label'    => $label,
     *      'font_size'=> $font_size
     *  )
     * )
     *
     * @return array
     */
    public function getFullTaxInfo()
    {
        return $this->traitGetFullTaxInfo();
    }

    /**
     * Append empty row beneath current total
     *
     * @param array $totals
     * @param int $amount
     * @return array
     */
    public function appendEmptyRows($totals, $amount)
    {
        return $this->traitAppendEmptyRows($totals, $amount);
    }
}
