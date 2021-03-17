<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\OrdersExportImport\Api\Data;

/**
 * Interface Profile
 *
 * @package Aitoc\OrdersExportImport\Api\Data
 */
interface ProfileInterface
{
    const PROFILE_ID = 'profile_id';
    const STORE_ID = 'store_id';
    const NAME = 'name';
    const CONFIG = 'config';
    const DATE = 'date';
    const FLAG_DATE = 'flag_date';
    const CRON_DATE = 'crondate';
    const ALL_STATUSES = 'All Statuses';
    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get Store ID
     *
     * @return int|null
     */
    public function getStoreId();

    /**
     * Get Name
     *
     * @return string|null
     */
    public function getName();

    /**
     * Get Parameters
     *
     * @return mixed
     */
    public function getConfig();

    /**
     * Get Date
     *
     * @return string
     */
    public function getDate();

    /**
     * Get Flag Date
     *
     * @return boolean|null
     */
    public function getFlagDate();

    /**
     * Get Cron Date
     *
     * @return string|null
     */
    public function getCronDate();

    /**
     * Set ID
     *
     * @param $id
     *
     * @return \Aitoc\OrdersExportImport\Api\Data\ProfileInterface
     */
    public function setId($id);

    /**
     * Set Store ID
     *
     * @param $store
     *
     * @return mixed
     */
    public function setStoreId($store);

    /**
     * Set Name
     *
     * @param $name
     *
     * @return \Aitoc\OrdersExportImport\Api\Data\ProfileInterface
     */
    public function setName($name);

    /**
     * Set Config
     *
     * @param $config
     *
     * @return \Aitoc\OrdersExportImport\Api\Data\ProfileInterface
     */
    public function setConfig($config);

    /**
     * Set Date
     *
     * @param $date
     *
     * @return \Aitoc\OrdersExportImport\Api\Data\ProfileInterface
     */
    public function setDate($date);

    /**
     * Set Flag Date
     *
     * @param $flag
     *
     * @return \Aitoc\OrdersExportImport\Api\Data\ProfileInterface
     */
    public function setFlagDate($flag);

    /**
     * Set Cron Date
     *
     * @param $cron
     *
     * @return \Aitoc\OrdersExportImport\Api\Data\ProfileInterface
     */
    public function setCronDate($cron);
}
