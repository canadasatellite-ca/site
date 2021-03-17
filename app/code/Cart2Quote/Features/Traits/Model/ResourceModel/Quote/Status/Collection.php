<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\ResourceModel\Quote\Status;
/**
 * Flat quotaion quote status history collection
 */
trait Collection
{
    /**
     * Get collection data as options array
     *
     * @return array
     */
    private function toOptionArray()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->_toOptionArray('status', 'label');
		}
	}
    /**
     * Get collection data as options hash
     *
     * @return array
     */
    private function toOptionHash()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->_toOptionHash('status', 'label');
		}
	}
    /**
     * Add state code filter to collection
     *
     * @param string $state
     * @return $this
     */
    private function addStateFilter($state)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->joinStates();
        $this->getSelect()->where('state_table.state=?', $state);
        return $this;
		}
	}
    /**
     * Join quote states table
     *
     * @return $this
     */
    private function joinStates()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if (!$this->getFlag('states_joined')) {
            $this->_idFieldName = 'status_state';
            $this->getSelect()->joinLeft(
                ['state_table' => $this->quotationResourceModel->getTable('quotation_quote_status_state')],
                'main_table.status=state_table.status',
                ['state', 'is_default', 'visible_on_front']
            );
            $this->setFlag('states_joined', true);
        }
        return $this;
		}
	}
    /**
     * Define label order
     *
     * @param string $dir
     * @return $this
     */
    private function orderByLabel($dir = 'ASC')
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->getSelect()->order('main_table.label ' . $dir);
        return $this;
		}
	}
    /**
     * Internal constructor
     *
     * @return void
     */
    private function _construct()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->_init(
            \Cart2Quote\Quotation\Model\Quote\Status::class,
            \Cart2Quote\Quotation\Model\ResourceModel\Quote\Status::class
        );
		}
	}
}
