<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\ResourceModel\Quote;

/**
 * Quote status resourcemodel
 */
class Status extends \Magento\Sales\Model\ResourceModel\Order\Status
{
    use \Cart2Quote\Features\Traits\Model\ResourceModel\Quote\Status {
        checkIsStatusUsed as private traitCheckIsStatusUsed;
        _construct as private _traitConstruct;
    }

    /**
     * Status labels table
     *
     * @var string
     */
    protected $labelsTable;

    /**
     * Status state table
     *
     * @var string
     */
    protected $stateTable;

    /**
     * Check is this status used in quotes
     *
     * @param string $status
     * @return bool
     */
    public function checkIsStatusUsed($status)
    {
        return $this->traitCheckIsStatusUsed($status);
    }

    /**
     * Internal constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_traitConstruct();
    }
}
