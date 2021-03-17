<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\ResourceModel\Status;
/**
 * Oder statuses grid collection
 */
trait Collection
{
    /**
     * Join quote states table
     *
     * @return $this
     */
    private function _initSelect()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			parent::_initSelect();
        $this->joinStates();
        return $this;
		}
	}
}
