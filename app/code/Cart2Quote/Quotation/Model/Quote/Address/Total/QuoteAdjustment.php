<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Quote\Address\Total;

/**
 * Class QuoteAdjustment
 *
 * @package Cart2Quote\Quotation\Model\Quote\Address\Total
 */
class QuoteAdjustment extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    use \Cart2Quote\Features\Traits\Model\Quote\Address\Total\QuoteAdjustment {
        fetch as private traitFetch;
    }

    /**
     * @var \Magento\Weee\Helper\Data
     */
    protected $weeeHelper;

    /**
     * QuoteAdjustment constructor
     *
     * @param \Magento\Weee\Helper\Data $weeeHelper
     */
    public function __construct(
        \Magento\Weee\Helper\Data $weeeHelper
    ) {
        $this->weeeHelper = $weeeHelper;
    }

    /**
     * Assign quote adjustment amount and label to address object
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
