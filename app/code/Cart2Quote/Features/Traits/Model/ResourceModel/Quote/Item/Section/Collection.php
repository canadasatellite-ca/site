<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\ResourceModel\Quote\Item\Section;
/**
 * Trait Collection
 * @package Cart2Quote\Quotation\Model\ResourceModel\Quote\Item\Section
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
            \Cart2Quote\Quotation\Model\Quote\Item\Section::class,
            \Cart2Quote\Quotation\Model\ResourceModel\Quote\Item\Section::class
        );
		}
	}
    /**
     * @param string|int $itemId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getSectionIdForItem($itemId)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$idsSelect = clone $this->getSelect();
        $idsSelect->reset(\Magento\Framework\DB\Select::ORDER);
        $idsSelect->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $idsSelect->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);
        $idsSelect->reset(\Magento\Framework\DB\Select::COLUMNS);
        $idsSelect->where(
            \Cart2Quote\Quotation\Api\Data\Quote\Item\SectionInterface::ITEM_ID . ' = ?',
            $itemId
        );
        $idsSelect->columns($this->getResource()->getIdFieldName(), 'main_table');
        $idsSelect->order(
            \sprintf(
                '%s %s',
                \Cart2Quote\Quotation\Api\Data\Quote\Item\SectionInterface::SORT_ORDER,
                \Magento\Framework\Data\Collection::SORT_ORDER_DESC
            )
        );
        return $this->getConnection()->fetchCol($idsSelect, $this->_bindParams);
		}
	}
}