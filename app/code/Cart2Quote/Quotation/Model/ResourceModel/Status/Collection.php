<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\ResourceModel\Status;

/**
 * Oder statuses grid collection
 */
class Collection extends \Cart2Quote\Quotation\Model\ResourceModel\Quote\Status\Collection
{
    use \Cart2Quote\Features\Traits\Model\ResourceModel\Status\Collection {
        _initSelect as private _traitInitSelect;
    }

    /**
     * Join quote states table
     *
     * @return $this
     */
    protected function _initSelect()
    {
        return $this->_traitInitSelect();
    }
}
