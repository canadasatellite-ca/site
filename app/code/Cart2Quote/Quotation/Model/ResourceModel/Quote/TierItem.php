<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\ResourceModel\Quote;

/**
 * TierItem resourcemodel
 */
class TierItem extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    use \Cart2Quote\Features\Traits\Model\ResourceModel\Quote\TierItem {
        _construct as private _traitConstruct;
    }

    /**
     * Items table
     *
     * @var string
     */
    protected $itemsTable;

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
