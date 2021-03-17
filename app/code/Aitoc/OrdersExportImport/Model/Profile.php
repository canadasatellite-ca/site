<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\OrdersExportImport\Model;

use Aitoc\OrdersExportImport\Api\Data\ProfileInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Profile
 *
 * @package Aitoc\OrdersExportImport\Model
 */
class Profile extends AbstractModel implements ProfileInterface
{
    protected function _construct()
    {
        $this->_init('Aitoc\OrdersExportImport\Model\ResourceModel\Profile');
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(self::PROFILE_ID);
    }

    /**
     * Get Store ID
     *
     * @return int|null
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * Get Name
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * Get Parameters
     *
     * @return mixed
     */
    public function getConfig()
    {
        return $this->getData(self::CONFIG);
    }

    /**
     * Get Date
     *
     * @return string
     */
    public function getDate()
    {
        return $this->getData(self::DATE);
    }

    /**
     * Get Flag Date
     *
     * @return boolean|null
     */
    public function getFlagDate()
    {
        return $this->getData(self::FLAG_DATE);
    }

    /**
     * Get Cron Date
     *
     * @return string|null
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
     * @return \Aitoc\OrdersExportImport\Api\Data\ProfileInterface
     */
    public function setId($id)
    {
        $this->setData(self::PROFILE_ID, $id);
    }

    /**
     * Set Store ID
     *
     * @param $store
     *
     * @return mixed
     */
    public function setStoreId($store)
    {
        $this->setData(self::STORE_ID, $store);
    }

    /**
     * Set Name
     *
     * @param $name
     *
     * @return \Aitoc\OrdersExportImport\Api\Data\ProfileInterface
     */
    public function setName($name)
    {
        $this->setData(self::NAME, $name);
    }

    /**
     * Set Config
     *
     * @param $config
     *
     * @return \Aitoc\OrdersExportImport\Api\Data\ProfileInterface
     */
    public function setConfig($config)
    {
        $this->setData(self::CONFIG, $config);
    }

    /**
     * Set Date
     *
     * @param $date
     *
     * @return \Aitoc\OrdersExportImport\Api\Data\ProfileInterface
     */
    public function setDate($date)
    {
        $this->setData(self::DATE, $date);
    }

    /**
     * Set Flag Date
     *
     * @param $flag
     *
     * @return \Aitoc\OrdersExportImport\Api\Data\ProfileInterface
     */
    public function setFlagDate($flag)
    {
        $this->setData(self::FLAG_DATE, $flag);
    }

    /**
     * Set Cron Date
     *
     * @param $cron
     *
     * @return \Aitoc\OrdersExportImport\Api\Data\ProfileInterface
     */
    public function setCronDate($cron)
    {
        $this->setData(self::CRON_DATE, $cron);
    }

    public function getUnsConfig()
    {
        return unserialize($this->getConfig());
    }

    /**
     * @return string
     */
    public function getTypeFile()
    {
        $config = $this->getUnsConfig();
        $type   = '.xml';
        if (in_array($config['file_type'], [
                \Aitoc\OrdersExportImport\Model\Converter::FILE_TYPE_CSV,
                \Aitoc\OrdersExportImport\Model\Converter::FILE_TYPE_ADVANCED_CSV
            ])) {
            $type = '.csv';
        }

        return $type;
    }
}
