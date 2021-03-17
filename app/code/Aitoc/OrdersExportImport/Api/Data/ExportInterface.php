<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\OrdersExportImport\Api\Data;

/**
 * Interface ExportInterface
 *
 * @package Aitoc\OrdersExportImport\Api\Data
 */
interface ExportInterface
{
    const EXPORT_ID = 'export_id';
    const PROFILE_ID = 'profile_id';
    const DT = 'dt';
    const FILENAME = 'filename';
    const SERIALIZED_CONFIG = 'serialized_config';
    const TYPE_FILE = 'type_file';
    const ORDERS_COUNT = 'orders_count';
    const IS_CRON = 'is_cron';
    const STATUS = 'status';
    const LOCAL_SERVER = 0;
    const REMOTE_FTP = 1;
    const EMAIL = 2;

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
    public function getProfileId();

    /**
     * Get DateTime
     *
     * @return string|null
     */
    public function getDt();

    /**
     * Get Filename
     *
     * @return mixed
     */
    public function getFilename();

    /**
     * Get Parameters
     *
     * @return string
     */
    public function getSerializedConfig();

    /**
     * Get Type File
     *
     * @return boolean|null
     */
    public function getTypeFile();

    /**
     * Get Count Orders
     *
     * @return string|null
     */
    public function getOrdersCount();
    /**
     * Get Is Cron
     *
     * @return string|null
     */
    public function getIsCron();

    /**
     * Get status
     *
     * @return mixed
     */
    public function getStatus();

    /**
     * Set ID
     *
     * @param $id
     *
     * @return \Aitoc\OrdersExportImport\Api\Data\ExportInterface
     */
    public function setId($id);

    /**
     * Set Profile ID
     *
     * @param $profile
     *
     * @return mixed
     */
    public function setProfileId($profile);

    /**
     * Set Name
     *
     * @param $date
     *
     * @return \Aitoc\OrdersExportImport\Api\Data\ExportInterface
     */
    public function setDt($date);

    /**
     * Set Filename
     *
     * @param $file
     *
     * @return \Aitoc\OrdersExportImport\Api\Data\ExportInterface
     */
    public function setFilename($file);

    /**
     * Set Config
     *
     * @param $config
     *
     * @return \Aitoc\OrdersExportImport\Api\Data\ExportInterface
     */
    public function setSerializedConfig($config);

    /**
     * Set Type File
     *
     * @param $type
     *
     * @return \Aitoc\OrdersExportImport\Api\Data\ExportInterface
     */
    public function setTypeFile($type);

    /**
     * Set Orders Count
     *
     * @param $count
     *
     * @return \Aitoc\OrdersExportImport\Api\Data\ExportInterface
     */
    public function setOrdersCount($count);

    /**
     * Set Is Cron
     *
     * @param $cron
     *
     * @return \Aitoc\OrdersExportImport\Api\Data\ExportInterface
     */
    public function setIsCron($cron);

    /**
     * @param $status
     * @return mixed
     */
    public function setStatus($status);
}
