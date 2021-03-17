<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Cron;

/**
 * Class ProcessShipment
 * @package Mageside\CanadaPostShipping\Cron
 */
class Logs
{
    const DAYS_SAVE = 3;

    /**
     * @var \Mageside\CanadaPostShipping\Model\ResourceModel\RequestLog
     */
    private $requestLog;

    /**
     * Logs constructor.
     * @param \Mageside\CanadaPostShipping\Model\ResourceModel\RequestLog $requestLog
     */
    public function __construct(
        \Mageside\CanadaPostShipping\Model\ResourceModel\RequestLog $requestLog
    ) {
        $this->requestLog = $requestLog;
    }

    /**
     * Clear logs started by cron
     */
    public function clearLogs()
    {
        try {
            $clearDate = date(
                'Y-m-d',
                strtotime("-" . self::DAYS_SAVE . " days")
            );
            $this->requestLog->clearLogsBeforeDate($clearDate);
        } catch (\Exception $exception) {

        }
    }
}
