<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\ResourceModel\Quote;
/**
 * Quote status resourcemodel
 */
trait Status
{
    /**
     * Check is this status used in quotes
     *
     * @param string $status
     * @return bool
     */
    private function checkIsStatusUsed($status)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return (bool)$this->getConnection()->fetchOne(
            $this->getConnection()->select()
                ->from(['sfo' => $this->getTable('quotation_quote')], [])
                ->where('status = ?', $status)
                ->limit(1)
                ->columns([new \Zend_Db_Expr(1)])
        );
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
			$this->_init('quotation_quote_status', 'status');
        $this->_isPkAutoIncrement = false;
        $this->labelsTable = $this->getTable('quotation_quote_status_label');
        $this->stateTable = $this->getTable('quotation_quote_status_state');
		}
	}
}
