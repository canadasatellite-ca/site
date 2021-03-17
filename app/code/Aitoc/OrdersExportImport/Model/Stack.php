<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\OrdersExportImport\Model;

use Aitoc\OrdersExportImport\Api\Data\StackInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Stack
 *
 * @package Aitoc\OrdersExportImport\Model
 */
class Stack extends AbstractModel implements StackInterface
{
    protected function _construct()
    {
        $this->_init('Aitoc\OrdersExportImport\Model\ResourceModel\Stack');
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(self::STACK_ID);
    }

    /**
     * Get Export ID
     *
     * @return int|null
     */
    public function getExportId()
    {
        return $this->getData(self::EXPORT_ID);
    }

    /**
     * Get Status
     *
     * @return string|null
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * Get Error
     *
     * @return mixed
     */
    public function getError()
    {
        return $this->getData(self::ERROR);
    }

    /**
     * Get Percent
     *
     * @return string
     */
    public function getPercent()
    {
        return $this->getData(self::PERCENT);
    }

    /**
     * Get Cron Date
     *
     * @return string
     */
    public function getCronDate()
    {
        return $this->getData(self::CRON_DATE);
    }

    /**
     * Set ID
     *
     * @param $id
     *
     * @return \Aitoc\OrdersExportImport\Api\Data\StackInterface
     */
    public function setId($id)
    {
        $this->setData(self::STACK_ID, $id);
    }

    /**
     * Set Export ID
     *
     * @param $export
     *
     * @return mixed
     */
    public function setExportId($export)
    {
        $this->setData(self::EXPORT_ID, $export);
    }

    /**
     * Set Status
     *
     * @param $status
     *
     * @return mixed
     */
    public function setStatus($status)
    {
        $this->setData(self::STATUS, $status);
    }

    /**
     * Set Error
     *
     * @param $error
     *
     * @return \Aitoc\OrdersExportImport\Api\Data\StackInterface
     */
    public function setError($error)
    {
        $this->setData(self::ERROR, $error);
    }

    /**
     * Set Percent
     *
     * @param $percent
     *
     * @return \Aitoc\OrdersExportImport\Api\Data\StackInterface
     */
    public function setPercent($percent)
    {
        $this->setData(self::PERCENT, $percent);
    }

    /**
     * Set Cron Date
     *
     * @param $percent
     *
     * @return \Aitoc\OrdersExportImport\Api\Data\StackInterface
     */
    public function setCronDate($date)
    {
        $this->setData(self::CRON_DATE, $date);
    }

    /**
     * List Statuses
     *
     * @return array
     */
    public function listStatuses()
    {
        return [self::STATUS_ACTIVE, self::STATUS_FINISH, self::STATUS_QUEUE, self::STATUS_FAILURE];
    }
}
