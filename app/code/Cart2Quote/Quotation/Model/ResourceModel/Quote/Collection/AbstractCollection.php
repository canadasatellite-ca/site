<?php /** @noinspection ALL */

/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\ResourceModel\Quote\Collection;

/**
 * Flat quotation quote collection
 */
abstract class AbstractCollection extends \Cart2Quote\Quotation\Model\ResourceModel\Collection\AbstractCollection
{
    use \Cart2Quote\Features\Traits\Model\ResourceModel\Quote\Collection\AbstractCollection {
        getQuotationQuote as private traitGetQuotationQuote;
        setQuotationQuote as private traitSetQuotationQuote;
        setQuoteFilter as private traitSetQuoteFilter;
    }

    /**
     * Order object
     *
     * @var \Cart2Quote\Quotation\Model\Quote
     */
    protected $_quotationQuote = null;

    /**
     * Order field for setOrderFilter
     *
     * @var string
     */
    protected $_quoteField = 'parent_id';

    /**
     * Retrieve quotation quote as parent collection object
     *
     * @return \Cart2Quote\Quotation\Model\Quote|null
     */
    public function getQuotationQuote()
    {
        return $this->traitGetQuotationQuote();
    }

    /**
     * Set quotation quote model as parent collection object
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return $this
     */
    public function setQuotationQuote($quote)
    {
        return $this->traitSetQuotationQuote($quote);
    }

    /**
     * Add quote filter
     *
     * @param int|\Cart2Quote\Quotation\Model\Quote $quote
     * @return $this
     */
    public function setQuoteFilter($quote)
    {
        return $this->traitSetQuoteFilter($quote);
    }
}
