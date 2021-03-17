<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\ResourceModel\Quote;
/**
 * Section resourcemodel
 */
trait Section
{
    /**
     * Internal constructor
     *
     * @return void
     */
    private function _construct()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->_init('quotation_quote_sections', 'section_id');
        $this->itemsTable = $this->getTable('quotation_quote_sections', $this->connectionName);
		}
	}
    /**
     * @param $quoteId
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Db_Statement_Exception
     */
    private function unassignedExistsForQuote($quoteId)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$select = $this->getConnection()->select()
            ->from($this->getMainTable())
            ->where(\Cart2Quote\Quotation\Api\Data\Quote\SectionInterface::QUOTE_ID . ' = ?', $quoteId)
            ->where(\Cart2Quote\Quotation\Api\Data\Quote\SectionInterface::IS_UNASSIGNED . ' = ?', true);
        return $select->query()->rowCount() > 0;
		}
	}
}
