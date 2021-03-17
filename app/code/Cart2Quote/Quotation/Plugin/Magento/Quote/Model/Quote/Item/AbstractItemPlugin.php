<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Plugin\Magento\Quote\Model\Quote\Item;

/**
 * Class AbstractItemPlugin
 * @package Cart2Quote\Quotation\Plugin\Magento\Quote\Model\Quote\Item
 */
class AbstractItemPlugin
{
    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * AbstractItemPlugin constructor.
     *
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
    ) {
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * Get item price used for quote calculation process.
     *
     * This method get custom price (if it is defined) or original product final price
     *
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $subject
     * @param callable $proceed
     * @return callable|float
     */
    public function aroundGetCalculationPrice(\Magento\Quote\Model\Quote\Item\AbstractItem $subject, callable $proceed)
    {
        if (!$this->isQuotationCheckout($subject->getQuote())) {
            return $proceed();
        }

        $price = $subject->getData('calculation_price');
        if ($price === null) {
            if ($subject->hasCustomPrice()) {
                $price = $subject->getCustomPrice();
                $price = $this->priceCurrency->convert($price, $subject->getStore());
            } else {
                $price = $subject->getConvertedPrice();
            }
            $subject->setData('calculation_price', $price);
        }

        return $price;
    }

    /**
     * Get item price used for quote calculation process.
     *
     * This method get original custom price applied before tax calculation
     *
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $subject
     * @param callable $proceed
     * @return callable|float
     */
    public function aroundGetCalculationPriceOriginal(\Magento\Quote\Model\Quote\Item\AbstractItem $subject, callable $proceed)
    {
        if (!$this->isQuotationCheckout($subject->getQuote())) {
            return $proceed();
        }

        $price = $subject->getData('calculation_price');
        if ($price === null) {
            if ($subject->hasOriginalCustomPrice()) {
                $price = $subject->getOriginalCustomPrice();
                $price = $this->priceCurrency->convert($price, $subject->getStore());
            } else {
                $price = $subject->getConvertedPrice();
            }
            $subject->setData('calculation_price', $price);
        }

        return $price;
    }

    /**
     * Get calculation price used for quote calculation in base currency.
     *
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $subject
     * @param callable $proceed
     * @return callable|float
     */
    public function aroundGetBaseCalculationPrice(\Magento\Quote\Model\Quote\Item\AbstractItem $subject, callable $proceed)
    {
        if (!$this->isQuotationCheckout($subject->getQuote())) {
            return $proceed();
        }

        if (!$subject->hasBaseCalculationPrice()) {
            if ($subject->hasCustomPrice()) {
                $price = (double)$subject->getCustomPrice();
            } else {
                $price = $subject->getPrice();
            }
            $subject->setBaseCalculationPrice($price);
        }

        return $subject->getData('base_calculation_price');
    }

    /**
     * Get original calculation price used for quote calculation in base currency.
     *
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $subject
     * @param callable $proceed
     * @return callable|float
     */
    public function aroundGetBaseCalculationPriceOriginal(\Magento\Quote\Model\Quote\Item\AbstractItem $subject, callable $proceed)
    {
        if (!$this->isQuotationCheckout($subject->getQuote())) {
            return $proceed();
        }

        if (!$subject->hasBaseCalculationPrice()) {
            if ($subject->hasOriginalCustomPrice()) {
                $price = (double)$subject->getOriginalCustomPrice();
            } else {
                $price = $subject->getPrice();
            }
            $subject->setBaseCalculationPrice($price);
        }

        return $subject->getData('base_calculation_price');
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @return bool
     */
    private function isQuotationCheckout($quote)
    {
        return (bool)$quote->getLinkedQuotationId();
    }
}
