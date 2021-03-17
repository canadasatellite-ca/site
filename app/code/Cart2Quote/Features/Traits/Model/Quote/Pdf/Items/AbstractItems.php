<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Quote\Pdf\Items;
/**
 * Quote Pdf Items renderer Abstract
 */
trait AbstractItems
{
    /**
     * Set Quote model
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return $this
     */
    private function setQuote(\Cart2Quote\Quotation\Model\Quote $quote)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->quote = $quote;
        return $this;
		}
	}
    /**
     * Retrieve quote object
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    private function getQuote()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if (null === $this->quote) {
            throw new \Magento\Framework\Exception\LocalizedException(__('The quote object is not specified.'));
        }
        return $this->quote;
		}
	}
    /**
     * Get the tier item prices to display
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getQuoteTierItemPricesForDisplay()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$quote = $this->getQuote();
        $item = $this->getItem();
        $tierItems = $item->getTierItems();
        $prices = [];
        foreach ($tierItems as $item) {
            if ($this->_taxData->displaySalesBothPrices()) {
                $prices[$item->getId()] = [
                    [
                        'label' => __('Excl. Tax') . ':',
                        'price' => $quote->formatPriceTxt($this->quotationTaxHelper->getPriceExclTax($item)),
                        'subtotal' => $quote->formatPriceTxt($item->getRowTotal()),
                    ],
                    [
                        'label' => __('Incl. Tax') . ':',
                        'price' => $quote->formatPriceTxt($item->getPriceInclTax()),
                        'subtotal' => $quote->formatPriceTxt($item->getRowTotalInclTax())
                    ],
                ];
            } elseif ($this->_taxData->displaySalesPriceInclTax()) {
                $prices[$item->getId()] = [
                    [
                        'label' => '',
                        'price' => $quote->formatPriceTxt($item->getPriceInclTax()),
                        'subtotal' => $quote->formatPriceTxt($item->getRowTotalInclTax()),
                    ],
                ];
            } else {
                $prices[$item->getId()] = [
                    [
                        'label' => '',
                        'price' => $quote->formatPriceTxt($this->quotationTaxHelper->getPriceExclTax($item)),
                        'subtotal' => $quote->formatPriceTxt($item->getRowTotal()),
                    ],
                ];
            }
        }
        return $prices;
		}
	}
}
