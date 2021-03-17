<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\OrdersExportImport\Model;

use Aitoc\OrdersExportImport\Api\Data\ExportInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Export
 *
 * @package Aitoc\OrdersExportImport\Model
 */
class Export extends AbstractModel implements ExportInterface
{
    protected function _construct()
    {
        $this->_init('Aitoc\OrdersExportImport\Model\ResourceModel\Export');
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(self::EXPORT_ID);
    }

    /**
     * Get Store ID
     *
     * @return int|null
     */
    public function getProfileId()
    {
        return $this->getData(self::PROFILE_ID);
    }

    /**
     * Get DateTime
     *
     * @return string|null
     */
    public function getDt()
    {
        return $this->getData(self::DT);
    }

    /**
     * Get Filename
     *
     * @return mixed
     */
    public function getFilename()
    {
        return $this->getData(self::FILENAME);
    }

    /**
     * Get Parameters
     *
     * @return string
     */
    public function getSerializedConfig()
    {
        return $this->getData(self::SERIALIZED_CONFIG);
    }

    /**
     * Get Type File
     *
     * @return boolean|null
     */
    public function getTypeFile()
    {
        return $this->getData(self::TYPE_FILE);
    }

    /**
     * Get Count Orders
     *
     * @return string|null
     */
    public function getOrdersCount()
    {
        return $this->getData(self::ORDERS_COUNT);
    }

    /**
     * Get Is Cron
     *
     * @return string|null
     */
    public function getIsCron()
    {
        return $this->getData(self::IS_CRON);
    }

    /**
     * Get Status
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * Set ID
     *
     * @param $id
     *
     * @return \Aitoc\OrdersExportImport\Api\Data\ExportInterface
     */
    public function setId($id)
    {
        $this->setData(self::EXPORT_ID, $id);
    }

    /**
     * Set Profile ID
     *
     * @param $profile
     *
     * @return mixed
     */
    public function setProfileId($profile)
    {
        $this->setData(self::PROFILE_ID, $profile);
    }

    /**
     * Set Name
     *
     * @param $date
     *
     * @return \Aitoc\OrdersExportImport\Api\Data\ExportInterface
     */
    public function setDt($date)
    {
        $this->setData(self::DT, $date);
    }

    /**
     * Set Filename
     *
     * @param $file
     *
     * @return \Aitoc\OrdersExportImport\Api\Data\ExportInterface
     */
    public function setFilename($file)
    {
        $this->setData(self::FILENAME, $file);
    }

    /**
     * Set Config
     *
     * @param $config
     *
     * @return \Aitoc\OrdersExportImport\Api\Data\ExportInterface
     */
    public function setSerializedConfig($config)
    {
        $this->setData(self::SERIALIZED_CONFIG, $config);
    }

    /**
     * Set Type File
     *
     * @param $type
     *
     * @return \Aitoc\OrdersExportImport\Api\Data\ExportInterface
     */
    public function setTypeFile($type)
    {
        $this->setData(self::TYPE_FILE, $type);
    }

    /**
     * Set Orders Count
     *
     * @param $count
     *
     * @return \Aitoc\OrdersExportImport\Api\Data\ExportInterface
     */
    public function setOrdersCount($count)
    {
        $this->setData(self::ORDERS_COUNT, $count);
    }

    /**
     * Set Is Cron
     *
     * @param $cron
     *
     * @return \Aitoc\OrdersExportImport\Api\Data\ExportInterface
     */
    public function setIsCron($cron)
    {
        $this->setData(self::IS_CRON, $cron);
    }

    /**
     * @param $status
     */
    public function setStatus($status)
    {
        $this->setData(self::STATUS, $status);
    }

    /**
     * @param null $date
     * @return mixed
     */
    public function addStack($date = null)
    {
        $stack = \Magento\Framework\App\ObjectManager::getInstance()->create('Aitoc\OrdersExportImport\Model\Stack');
        $stack->setExportId($this->getId());
        $stack->setStatus(0);
        if (!$date) {
            $date = date('Y-m-d H:i:s');
        }
        $stack->setCronDate($date);
        $stack->save();

        return $stack;
    }
}
