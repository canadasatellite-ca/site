<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Quote\Address\Total;

/**
 * Class QuoteProfit
 *
 * @package Cart2Quote\Quotation\Model\Quote\Address\Total
 */
class QuoteProfit extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    use \Cart2Quote\Features\Traits\Model\Quote\Address\Total\QuoteProfit {
        fetch as private traitFetch;
    }

    /**
     * @var \Cart2Quote\Quotation\Helper\CostPrice
     */
    protected $costPriceHelper;

    /**
     * QuoteProfit constructor.
     *
     * @param \Cart2Quote\Quotation\Helper\CostPrice $costPriceHelper
     */
    public function __construct(
        \Cart2Quote\Quotation\Helper\CostPrice $costPriceHelper
    ) {
        $this->costPriceHelper = $costPriceHelper;
    }

    /**
     * Assign quote profit amount and label to address object
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return array
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        return $this->traitFetch($quote, $total);
    }
}
