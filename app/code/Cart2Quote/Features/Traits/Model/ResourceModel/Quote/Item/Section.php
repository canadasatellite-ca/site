<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\ResourceModel\Quote\Item;
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
			$this->_init(
            'quotation_quote_section_items',
            \Cart2Quote\Quotation\Api\Data\Quote\Item\SectionInterface::SECTION_ITEM_ID
        );
        $this->itemsTable = $this->getTable('quotation_quote_section_items', $this->connectionName);
		}
	}
}
