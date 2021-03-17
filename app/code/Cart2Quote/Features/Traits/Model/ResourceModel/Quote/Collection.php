<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\ResourceModel\Quote;
/**
 * Quotes collection
 */
trait Collection
{
    /**
     * Get Quotation by Quote Id
     *
     * @param int $quoteId
     * @return array
     */
    private function getQuote($quoteId)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$quote = $this->getConnection()
            ->fetchRow(
                $this->getConnection()
                    ->select()
                    ->from(['quotation_quote' => $this->getMainTable()])
                    ->where('quote_id = ?', $quoteId)
                    ->columns('*')
            );
        return $quote;
		}
	}
    /**
     * Get search criteria.
     *
     * @return \Magento\Framework\Api\SearchCriteriaInterface|null
     */
    private function getSearchCriteria()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->searchCriteria;
		}
	}
    /**
     * Set search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    private function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->searchCriteria = $searchCriteria;
        return $this;
		}
	}
    /**
     * Get total count.
     *
     * @return int
     */
    private function getTotalCount()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getSize();
		}
	}
    /**
     * Set total count.
     *
     * @param int $totalCount
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    private function setTotalCount($totalCount)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this;
		}
	}
    /**
     * Set items list.
     *
     * @param \Magento\Framework\Api\ExtensibleDataInterface[] $items
     * @return $this
     */
    private function setItems(array $items = null)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if (!$items) {
            return $this;
        }
        foreach ($items as $item) {
            $this->addItem($item);
        }
        return $this;
		}
	}
    /**
     * Resource initialization
     *
     * @return void
     */
    private function _construct()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->_init(
            \Cart2Quote\Quotation\Model\Quote::class,
            \Cart2Quote\Quotation\Model\ResourceModel\Quote::class
        );
		}
	}
    /**
     * Init collection select
     *
     * @return $this
     */
    private function _initSelect()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->getSelect()->from(['main_table' => $this->getMainTable()]);
        $this->getSelect()->joinLeft(
            $this->getTable('quote'),
            'entity_id=quote_id',
            $cols = '*',
            $schema = null
        );
        return $this;
		}
	}
}
