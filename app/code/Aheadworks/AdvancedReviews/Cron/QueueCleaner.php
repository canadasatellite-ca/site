<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Cron;

use Aheadworks\AdvancedReviews\Model\Config;
use Aheadworks\AdvancedReviews\Api\QueueManagementInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class QueueCleaner
 * @package Aheadworks\AdvancedReviews\Cron
 */
class QueueCleaner extends CronAbstract
{
    /**
     * Cron run interval in seconds: at least, 28 days
     */
    const RUN_INTERVAL = 28*24*60*60;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var QueueManagementInterface
     */
    private $queueManagement;

    /**
     * @param DateTime $dateTime
     * @param Config $config
     * @param QueueManagementInterface $queueManagement
     */
    public function __construct(
        DateTime $dateTime,
        Config $config,
        QueueManagementInterface $queueManagement
    ) {
        $this->config = $config;
        $this->queueManagement = $queueManagement;
        parent::__construct($dateTime);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if ($this->isLocked($this->config->getClearQueueLastExecTime(), self::RUN_INTERVAL)) {
            return $this;
        }

        $this->queueManagement->deleteProcessed();

        $this->config->setClearQueueLastExecTime($this->getCurrentTime());
        return $this;
    }
}
