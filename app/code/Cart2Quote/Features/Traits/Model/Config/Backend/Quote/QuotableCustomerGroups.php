<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Config\Backend\Quote;
/**
 * Backend model for products quotable by default setting
 * Trait QuotableCustomerGroups
 * @package Cart2Quote\Quotation\Model\Config\Backend\Quote
 */
trait QuotableCustomerGroups
{
    /**
     * To options array
     *
     * @return array
     */
    private function toOptionArray()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->customerGroupCollection->toOptionArray();
		}
	}
}
