<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Cron;

use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class CronAbstract
 * @package Aheadworks\AdvancedReviews\Cron
 */
abstract class CronAbstract
{
    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @param DateTime $dateTime
     */
    public function __construct(
        DateTime $dateTime
    ) {
        $this->dateTime = $dateTime;
    }

    /**
     * Main cron job entry point
     *
     * @return $this
     */
    abstract public function execute();

    /**
     * Is cron job locked
     *
     * @param int $lastExecTime
     * @param int $interval
     * @return bool
     */
    protected function isLocked($lastExecTime, $interval)
    {
        $now = $this->getCurrentTime();
        return $now < $lastExecTime + $interval;
    }

    /**
     * Get current time
     *
     * @return int
     */
    protected function getCurrentTime()
    {
        $now = $this->dateTime->timestamp();
        return $now;
    }
}
