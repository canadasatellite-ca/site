<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\ResourceModel\Quote;
/**
 * TierItem resourcemodel
 */
trait TierItem
{
    /**
     * Internal constructor
     *
     * @return void
     */
    private function _construct()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->_init('quotation_quote_tier_item', 'entity_id');
        $this->itemsTable = $this->getTable('quotation_quote_tier_item');
		}
	}
}
