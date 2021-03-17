<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\ResourceModel\Quote\Status;
use Cart2Quote\Quotation\Model\ResourceModel\EntityAbstract;
use Cart2Quote\Quotation\Model\Spi\QuoteStatusHistoryResourceInterface;
/**
 * Flat quotation quote status history resourcemodel
 */
trait History
{
    /**
     * Perform actions before object save
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			parent::_beforeSave($object);
        $warnings = $this->validator->validate($object);
        if (!empty($warnings)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __("Cannot save comment:\n%1", implode("\n", $warnings))
            );
        }
        return $this;
		}
	}
    /**
     * Model initialization
     *
     * @return void
     */
    private function _construct()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->_init('quotation_quote_status_history', 'entity_id');
		}
	}
}
