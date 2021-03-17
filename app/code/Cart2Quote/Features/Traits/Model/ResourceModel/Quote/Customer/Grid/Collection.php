<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\ResourceModel\Quote\Customer\Grid;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot;
/**
 * Trait Collection
 * @package Cart2Quote\Quotation\Model\ResourceModel\Quote\Customer\Grid
 */
trait Collection
{
    /**
     * Initialize db select
     *
     * @return $this
     */
    private function _initSelect()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			parent::_initSelect();
        $this->addCustomerIdFilter(
            $this->registry->registry(
                \Cart2Quote\Quotation\Controller\Adminhtml\Quote\Customer\Grid::CURRENT_CUSTOMER_ID
            )
        );
        return $this;
		}
	}
    /**
     * Add filtration by customer id
     *
     * @param int $customerId
     * @return $this
     */
    private function addCustomerIdFilter($customerId)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->getSelect()->where(
            'q.customer_id = ?',
            $customerId
        );
        return $this;
		}
	}
}
