<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Model\ResourceModel\RequestLog;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'record_id';

    protected function _construct()
    {
        $this->_init(
            \Mageside\CanadaPostShipping\Model\RequestLog::class,
            \Mageside\CanadaPostShipping\Model\ResourceModel\RequestLog::class
        );
    }
}
