<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Quote\Pdf\Total;
/**
 * Sales Order Total PDF model
 */
trait DefaultTotal
{
    /**
     * Get total for display on PDF
     *
     * @return array
     */
    private function getTotalsForDisplay()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$amount = $this->getSource()->formatPriceTxt($this->getAmount());
        if ($this->getAmountPrefix()) {
            $amount = $this->getAmountPrefix() . $amount;
        }
        $title = __($this->getTitle());
        if ($this->getTitleSourceField()) {
            $label = $title . ' (' . $this->getTitleDescription() . '):';
        } else {
            $label = $title . ':';
        }
        $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;
        $total = ['amount' => $amount, 'label' => $label, 'font_size' => $fontSize];
        return [$total];
		}
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
    private function getFullTaxInfo()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$fontSize = $this->getFontSize() ? $this->getFontSize() : 7;
        $taxClassAmount = $this->_taxHelper->getCalculatedTaxes($this->getQuote());
        if (!empty($taxClassAmount)) {
            foreach ($taxClassAmount as &$tax) {
                $percent = $tax['percent'] ? ' (' . $tax['percent'] . '%)' : '';
                $tax['amount'] = $this->getAmountPrefix() . $this->getQuote()->formatPriceTxt($tax['tax_amount']);
                $tax['label'] = __($tax['title']) . $percent . ':';
                $tax['font_size'] = $fontSize;
            }
        } else {
            /** @var \Magento\Tax\Model\ResourceModel\Sales\Order\Tax\Collection $orders */
            $orders = $this->_taxOrdersFactory->create();
            $rates = $orders->loadByOrder($this->getQuote())->toArray();
            $fullInfo = $this->_taxCalculation->reproduceProcess($rates['items']);
            $tax_info = [];
            if ($fullInfo) {
                foreach ($fullInfo as $info) {
                    if (isset($info['hidden']) && $info['hidden']) {
                        continue;
                    }
                    $_amount = $info['amount'];
                    foreach ($info['rates'] as $rate) {
                        $percent = $rate['percent'] ? ' (' . $rate['percent'] . '%)' : '';
                        $tax_info[] = [
                            'amount' => $this->getAmountPrefix() . $this->getQuote()->formatPriceTxt($_amount),
                            'label' => __($rate['title']) . $percent . ':',
                            'font_size' => $fontSize,
                        ];
                    }
                }
            }
            $taxClassAmount = $tax_info;
        }
        return $taxClassAmount;
		}
	}
    /**
     * Append empty row beneath current total
     *
     * @param array $totals
     * @param int $amount
     * @return array
     */
    private function appendEmptyRows($totals, $amount)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			for ($i = 0; $amount > $i; $i++) {
            $totals[] = [
                'amount' => '',
                'label' => '',
                'font_size' => '',
            ];
        }
        return $totals;
		}
	}
}
