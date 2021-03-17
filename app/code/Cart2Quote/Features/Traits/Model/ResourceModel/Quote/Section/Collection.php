<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\ResourceModel\Quote\Section;
/**
 * Trait Collection
 * @package Cart2Quote\Quotation\Model\ResourceModel\Quote\Section
 */
trait Collection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    private function _construct()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->_init(
            \Cart2Quote\Quotation\Model\Quote\Section::class,
            \Cart2Quote\Quotation\Model\ResourceModel\Quote\Section::class
        );
		}
	}
    /**
     * @param string|int $quoteId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getSectionIdsForQuote($quoteId)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$idsSelect = clone $this->getSelect();
        $idsSelect->reset(\Magento\Framework\DB\Select::ORDER);
        $idsSelect->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $idsSelect->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);
        $idsSelect->reset(\Magento\Framework\DB\Select::COLUMNS);
        $idsSelect->where(
            \Cart2Quote\Quotation\Api\Data\Quote\SectionInterface::QUOTE_ID . ' = ?',
            $quoteId
        );
        $idsSelect->columns($this->getResource()->getIdFieldName(), 'main_table');
        $idsSelect->order(
            \sprintf(
                '%s %s',
                \Cart2Quote\Quotation\Api\Data\Quote\SectionInterface::SORT_ORDER,
                \Magento\Framework\Data\Collection::SORT_ORDER_DESC
            )
        );
        return $this->getConnection()->fetchCol($idsSelect, $this->_bindParams);
		}
	}
    /**
     * @param string|int $quoteId
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getUnassignedSectionIdForQuote($quoteId)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$idsSelect = clone $this->getSelect();
        $idsSelect->reset(\Magento\Framework\DB\Select::ORDER);
        $idsSelect->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $idsSelect->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);
        $idsSelect->reset(\Magento\Framework\DB\Select::COLUMNS);
        $idsSelect->where(
            \Cart2Quote\Quotation\Api\Data\Quote\SectionInterface::QUOTE_ID . ' = ?',
            $quoteId
        );
        $idsSelect->where(
            \Cart2Quote\Quotation\Api\Data\Quote\SectionInterface::IS_UNASSIGNED . ' = ?',
            true
        );
        $idsSelect->columns($this->getResource()->getIdFieldName(), 'main_table');
        $idsSelect->order(
            \sprintf(
                '%s %s',
                \Cart2Quote\Quotation\Api\Data\Quote\SectionInterface::SORT_ORDER,
                \Magento\Framework\Data\Collection::SORT_ORDER_DESC
            )
        );
        return $this->getConnection()->fetchOne($idsSelect, $this->_bindParams);
		}
	}
}