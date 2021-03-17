<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Model\ResourceModel;

/**
 * Class RequestLog
 * @package Mageside\CanadaPostShipping\Model\ResourceModel
 */
class RequestLog extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Define main table and id field
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mageside_canadapost_logs', 'record_id');
    }

    /**
     * Clear old logs
     *
     * @param $date
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function clearLogsBeforeDate($date)
    {
        $connection = $this->getConnection();
        $connection->delete($this->getMainTable(), ['created_at < ?' => $date]);

        return $this;
    }
}
