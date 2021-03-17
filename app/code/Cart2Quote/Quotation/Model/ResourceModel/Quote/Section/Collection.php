<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\ResourceModel\Quote\Section;

/**
 * Class Collection
 * @package Cart2Quote\Quotation\Model\ResourceModel\Quote\Section
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    use \Cart2Quote\Features\Traits\Model\ResourceModel\Quote\Section\Collection {
        _construct as private _traitConstruct;
        getSectionIdsForQuote as private traitGetSectionIdsForQuote;
        getUnassignedSectionIdForQuote as private traitGetUnassignedSectionIdForQuote;
    }

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_traitConstruct();
    }

    /**
     * @param string|int $quoteId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSectionIdsForQuote($quoteId)
    {
        return $this->traitGetSectionIdsForQuote($quoteId);
    }

    /**
     * @param string|int $quoteId
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getUnassignedSectionIdForQuote($quoteId)
    {
        return $this->traitGetUnassignedSectionIdForQuote($quoteId);
    }
}