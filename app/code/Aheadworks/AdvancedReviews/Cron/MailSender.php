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
 * Class MailSender
 * @package Aheadworks\AdvancedReviews\Cron
 */
class MailSender extends CronAbstract
{
    /**
     * Cron run interval in seconds
     */
    const RUN_INTERVAL = 300;

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
        parent::__construct($dateTime);
        $this->config = $config;
        $this->queueManagement = $queueManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if ($this->isLocked($this->config->getSendEmailsLastExecTime(), self::RUN_INTERVAL)) {
            return $this;
        }

        $this->queueManagement->sendScheduled();

        $this->config->setSendEmailsLastExecTime($this->getCurrentTime());
        return $this;
    }
}
