<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Helper;

/**
 * Class Currency
 *
 * @package Cart2Quote\Quotation\Helper
 */
class Currency extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Admin helper
     *
     * @var \Magento\Sales\Helper\Admin
     */
    protected $adminHelper;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Sales\Helper\Admin $adminHelper
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Sales\Helper\Admin $adminHelper
    ) {
        $this->adminHelper = $adminHelper;
        parent::__construct(
            $context
        );
    }

    /**
     * Price attribute HTML getter
     *
     * @param \Cart2Quote\Quotation\Model\Quote $source
     * @param string $code
     * @param bool $strong
     * @param string $separator
     * @return string
     */
    public function displayPriceAttribute($source, $code, $strong = false, $separator = '<br/>')
    {
        // Display Price Attribute fix to set the order to the quote.
        $source = $source->setOrder($source);
        return $this->adminHelper->displayPriceAttribute($source, $code, $strong, $separator);
    }

    /**
     * Retrieve formated price
     *
     * @param \Cart2Quote\Quotation\Model\Quote $dataSource
     * @param float $value
     * @return string
     */
    public function formatPrice($dataSource, $value)
    {
        if ($dataSource->isCurrencyDifferent()) {
            return $this->displayPricesByCurrency($dataSource, $value);
        } else {
            return $dataSource->formatPrice($value);
        }
    }

    /**
     * Get the prices with the base and the set currency
     *
     * @param \Cart2Quote\Quotation\Model\Quote $dataObject
     * @param float $price
     * @param bool $strong
     * @param string $separator
     * @return string
     */
    public function displayPricesByCurrency($dataObject, $price, $strong = false, $separator = '<br/>')
    {
        // Display Price Attribute fix to set the order to the quote.
        $dataObject = $dataObject->setOrder($dataObject);
        $basePrice = $dataObject->convertPriceToQuoteBaseCurrency($price);
        return $this->displayPrices($dataObject, $basePrice, $price, $strong, $separator);
    }

    /**
     * Get base and normal prices html (block with base and place currency)
     *
     * @param   \Cart2Quote\Quotation\Model\Quote $dataObject
     * @param   float $basePrice
     * @param   float $price
     * @param   bool $strong
     * @param   string $separator
     * @return  string
     */
    public function displayPrices($dataObject, $basePrice, $price, $strong = false, $separator = '<br/>')
    {
        return $this->adminHelper->displayPrices($dataObject, $basePrice, $price, $strong, $separator);
    }
}
