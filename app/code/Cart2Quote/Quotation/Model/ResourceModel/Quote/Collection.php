<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\ResourceModel\Quote;

/**
 * Quotes collection
 */
class Collection extends \Magento\Quote\Model\ResourceModel\Quote\Collection
{

    use \Cart2Quote\Features\Traits\Model\ResourceModel\Quote\Collection {
        getQuote as private traitGetQuote;
        getSearchCriteria as private traitGetSearchCriteria;
        setSearchCriteria as private traitSetSearchCriteria;
        getTotalCount as private traitGetTotalCount;
        setTotalCount as private traitSetTotalCount;
        setItems as private traitSetItems;
        _construct as private _traitConstruct;
        _initSelect as private _traitInitSelect;
    }

    /**
     * @var \Magento\Framework\Api\SearchCriteriaInterface
     */
    protected $searchCriteria;
    /**
     * @var string
     */
    protected $_idFieldName = 'quote_id';

    /**
     * Get Quotation by Quote Id
     *
     * @param int $quoteId
     * @return array
     */
    public function getQuote($quoteId)
    {
        return $this->traitGetQuote($quoteId);
    }

    /**
     * Get search criteria.
     *
     * @return \Magento\Framework\Api\SearchCriteriaInterface|null
     */
    public function getSearchCriteria()
    {
        return $this->traitGetSearchCriteria();
    }

    /**
     * Set search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null)
    {
        return $this->traitSetSearchCriteria($searchCriteria);
    }

    /**
     * Get total count.
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->traitGetTotalCount();
    }

    /**
     * Set total count.
     *
     * @param int $totalCount
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setTotalCount($totalCount)
    {
        return $this->traitSetTotalCount($totalCount);
    }

    /**
     * Set items list.
     *
     * @param \Magento\Framework\Api\ExtensibleDataInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null)
    {
        return $this->traitSetItems($items);
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
     * Init collection select
     *
     * @return $this
     */
    protected function _initSelect()
    {
        return $this->_traitInitSelect();
    }
}
