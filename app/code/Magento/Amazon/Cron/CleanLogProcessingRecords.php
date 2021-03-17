<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Cron;

use Magento\Amazon\Logger\AscClientLogger;
use Magento\Amazon\Model\ResourceModel\LogProcessing;

class CleanLogProcessingRecords
{
    /**
     * @var LogProcessing
     */
    private $logProcessing;
    /**
     * @var AscClientLogger
     */
    private $logger;
    /**
     * @var int
     */
    private $minutesToKeepLogs;

    /**
     * CleanLogProcessingRecords constructor.
     *
     * @param LogProcessing $logProcessing
     * @param AscClientLogger $logger
     * @param int $minutesToKeepLogs
     */
    public function __construct(
        LogProcessing $logProcessing,
        AscClientLogger $logger,
        int $minutesToKeepLogs = 10
    ) {
        $this->minutesToKeepLogs = $minutesToKeepLogs;
        $this->logger = $logger;
        $this->logProcessing = $logProcessing;
    }

    /**
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     */
    public function execute()
    {
        $logsDeleted = $this->logProcessing->deleteLogsOlderThan($this->minutesToKeepLogs);
        if ($logsDeleted) {
            $this->logger->warn("Logs that stuck are cleaned by the cron job: ${logsDeleted}");
        }
    }
}
