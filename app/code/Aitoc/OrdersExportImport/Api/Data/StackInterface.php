<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\OrdersExportImport\Api\Data;

/**
 * Interface StackInterface
 *
 * @package Aitoc\OrdersExportImport\Api\Data
 */
interface StackInterface
{
    const STACK_ID = 'stack_id';
    const EXPORT_ID = 'export_id';
    const STATUS = 'status';
    const ERROR = 'error';
    const PERCENT = 'percent';
    const CRON_DATE = 'cron_date';
    const STATUS_ACTIVE = 'active';
    const STATUS_FINISH = 'finish';
    const STATUS_QUEUE = 'queue';
    const STATUS_FAILURE = 'failure';
    
    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get Export ID
     *
     * @return int|null
     */
    public function getExportId();

    /**
     * Get Status
     *
     * @return string|null
     */
    public function getStatus();

    /**
     * Get Error
     *
     * @return mixed
     */
    public function getError();

    /**
     * Get Percent
     *
     * @return string
     */
    public function getPercent();

    /**
     * Get Cron Date
     *
     * @return string
     */
    public function getCronDate();

    /**
     * Set ID
     *
     * @param $id
     *
     * @return \Aitoc\OrdersExportImport\Api\Data\StackInterface
     */
    public function setId($id);

    /**
     * Set Export ID
     *
     * @param $export
     *
     * @return mixed
     */
    public function setExportId($export);

    /**
     * Set Status
     *
     * @param $status
     *
     * @return mixed
     */
    public function setStatus($status);

    /**
     * Set Error
     *
     * @param $error
     *
     * @return \Aitoc\OrdersExportImport\Api\Data\StackInterface
     */
    public function setError($error);

    /**
     * Set Percent
     *
     * @param $percent
     *
     * @return \Aitoc\OrdersExportImport\Api\Data\StackInterface
     */
    public function setPercent($percent);

    /**
     * Set Cron Date
     *
     * @return string
     */
    public function setCronDate($date);
}
