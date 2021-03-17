<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Logger\Handler;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class SystemLogHandler extends AbstractProcessingHandler
{
    /**
     * @var LoggerInterface
     */
    private $defaultLogger;

    /**
     * SystemLogHandler constructor.
     * @param LoggerInterface $defaultLogger
     * @param int $level
     * @param bool $bubble
     */
    public function __construct(
        LoggerInterface $defaultLogger,
        $level = Logger::WARNING,
        $bubble = true
    ) {
        $this->defaultLogger = $defaultLogger;
        parent::__construct($level, $bubble);
    }

    /**
     * Writes the record down to the log of the implementing handler
     *
     * @param array $record
     * @return void
     */
    protected function write(array $record)
    {
        return $this->defaultLogger->log($record['level'], $record['message'], $record['context']);
    }
}
